<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        $orders = Order::whereBetween('created_at', [$startOfDay, $endOfDay])->where('status', 'completed')->with(['items', 'user'])->get();
        $totalSales = $orders->sum('total_amount');
        $orderCount = $orders->count();

        $payments = Payment::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->selectRaw('payment_method, sum(amount) as total')
            ->groupBy('payment_method')
            ->get();

        $productSales = \App\Models\OrderItem::whereIn('order_id', $orders->pluck('id'))
            ->selectRaw('product_name, sum(quantity) as total_quantity, sum(subtotal) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->get();

        $walletDeposits = \App\Models\StudentTransaction::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('type', 'deposit')
            ->selectRaw('payment_method, sum(amount) as total')
            ->groupBy('payment_method')
            ->get();

        $walletDepositsLog = \App\Models\StudentTransaction::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('type', 'deposit')
            ->with(['student', 'user'])
            ->get();

        return view('admin.reports.daily', compact('date', 'orders', 'totalSales', 'orderCount', 'payments', 'productSales', 'walletDeposits', 'walletDepositsLog'));
    }
}
