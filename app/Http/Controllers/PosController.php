<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Student;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StudentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->with(['products' => function($q) {
            $q->where('is_active', true);
        }])->get();
        
        // Students with balance > 0 to simplify selection or all active
        $students = Student::where('is_active', true)->orderBy('name')->get();

        return view('pos.index', compact('categories', 'students'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.payment_method' => 'required|in:cash,mobile,student_wallet',
            'payments' => 'required|array',
            'payments.*.method' => 'required|in:cash,mobile,student_wallet',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.student_id' => 'nullable|exists:students,id',
        ]);

        return DB::transaction(function () use ($request) {
            // Check for mixed categories
            $categoryIds = collect($request->cart)->map(function($item) {
                $product = Product::find($item['product_id']);
                return $product ? $product->category_id : null;
            })->unique();

            if ($categoryIds->count() > 1) {
                return response()->json(['success' => false, 'message' => 'Orders cannot contain items from mixed categories. Please split them into separate tickets.'], 400);
            }

            $rawTotalAmount = 0;
            $expectedTotalAmount = 0;
            $requiresQueue = false;
            
            // Calculate total and prepare items
            foreach ($request->cart as $item) {
                $product = Product::with('category')->findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                
                $rawTotalAmount += $subtotal;
                
                $walletDiscount = $product->category->wallet_discount ?? 0;
                if ($walletDiscount > 0 && isset($item['payment_method']) && $item['payment_method'] === 'student_wallet') {
                    $discount = $walletDiscount * $item['quantity'];
                    $subtotal = max(0, $subtotal - $discount);
                }
                
                if ($product->category && $product->category->requires_queue) {
                    $requiresQueue = true;
                }
                
                $expectedTotalAmount += $subtotal;
            }

            // Verify total payments match discounted total amount
            $totalPaid = collect($request->payments)->sum('amount');
            if (round($totalPaid, 2) != round($expectedTotalAmount, 2)) {
                return response()->json(['success' => false, 'message' => 'Total payments must exactly match the discounted order total.'], 400);
            }

            // Verify student wallet balances
            foreach ($request->payments as $paymentData) {
                if ($paymentData['method'] === 'student_wallet') {
                    if (empty($paymentData['student_id'])) {
                        return response()->json(['success' => false, 'message' => 'Student account is required for Student Wallet payment type.'], 400);
                    }
                    $student = Student::lockForUpdate()->findOrFail($paymentData['student_id']);
                    if ($student->balance < $paymentData['amount']) {
                        return response()->json(['success' => false, 'message' => "Insufficient wallet balance for student: {$student->name}"], 400);
                    }
                }
            }

            // Generate Queue Number if needed
            $queueNumber = null;
            $categoryId = $categoryIds->first();
            
            if ($requiresQueue && $categoryId) {
                $lastOrder = Order::whereDate('created_at', \Carbon\Carbon::today())
                    ->whereNotNull('queue_number')
                    ->whereHas('items.product', function($q) use ($categoryId) {
                        $q->where('category_id', $categoryId);
                    })
                    ->orderBy('id', 'desc')
                    ->first();
                
                if ($lastOrder && $lastOrder->queue_number) {
                    $queueNumber = $lastOrder->queue_number >= 100 ? 1 : $lastOrder->queue_number + 1;
                } else {
                    $queueNumber = 1;
                }
            }

            // Create Order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'total_amount' => $expectedTotalAmount,
                'status' => 'completed',
                'queue_number' => $queueNumber,
            ]);

            // Create Order Items
            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);
                // Stock deduction removed
            }

            // Process Payments
            foreach ($request->payments as $paymentData) {
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $paymentData['method'],
                    'amount' => $paymentData['amount'],
                    'student_id' => $paymentData['student_id'] ?? null,
                ]);

                if ($paymentData['method'] === 'student_wallet') {
                    $student = Student::findOrFail($paymentData['student_id']);
                    $student->decrement('balance', $paymentData['amount']);
                    
                    StudentTransaction::create([
                        'student_id' => $student->id,
                        'user_id' => Auth::id(),
                        'type' => 'deduction',
                        'amount' => $paymentData['amount'],
                        'reference' => 'Order ' . $order->order_number,
                        'description' => 'POS Checkout Payment via Wallet',
                    ]);
                }
            }

            $order->load(['items.product.category', 'payments.student', 'user']);

            return response()->json([
                'success' => true, 
                'message' => 'Order completed successfully.',
                'order' => $order
            ]);
        });
    }

    public function receipt(Order $order)
    {
        $order->load(['items', 'payments', 'user', 'student']);
        return view('pos.receipt', compact('order'));
    }
}
