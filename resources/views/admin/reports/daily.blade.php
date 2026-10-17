<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('End of Day (EOD) Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('admin.reports.daily') }}" class="flex items-center space-x-4">
                    <label for="date" class="font-bold text-gray-700">Select Date:</label>
                    <input type="date" name="date" id="date" value="{{ $date }}" class="border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow transition-colors">Generate Report</button>
                </form>
                <button onclick="window.print()" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded shadow flex items-center gap-2 transition-colors print:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print EOD
                </button>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-green-500">
                    <div class="p-6 text-center">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Sales Revenue</h3>
                        <p class="text-5xl font-black text-gray-900">${{ number_format($totalSales, 2) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500">
                    <div class="p-6 text-center">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Completed Orders</h3>
                        <p class="text-5xl font-black text-gray-900">{{ $orderCount }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-purple-500">
                    <div class="p-6 text-center">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Wallet Deposits</h3>
                        <p class="text-5xl font-black text-gray-900">${{ number_format($walletDeposits->sum('total'), 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Cash Flow -->
            @php
                $cashSales = $payments->firstWhere('payment_method', 'cash')->total ?? 0;
                $cashDeposits = $walletDeposits->firstWhere('payment_method', 'cash')->total ?? 0;
                $mobileSales = $payments->firstWhere('payment_method', 'mobile')->total ?? 0;
                $mobileDeposits = $walletDeposits->firstWhere('payment_method', 'mobile')->total ?? 0;
            @endphp
            <div class="bg-gray-800 rounded-xl shadow-lg p-6 text-white grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Physical Cash Expected (Drawer)</h3>
                    <p class="text-4xl font-black text-green-400">${{ number_format($cashSales + $cashDeposits, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">(${{ number_format($cashSales, 2) }} Sales + ${{ number_format($cashDeposits, 2) }} Wallet Deposits)</p>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Mobile Money Expected</h3>
                    <p class="text-4xl font-black text-blue-400">${{ number_format($mobileSales + $mobileDeposits, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">(${{ number_format($mobileSales, 2) }} Sales + ${{ number_format($mobileDeposits, 2) }} Wallet Deposits)</p>
                </div>
            </div>

            <!-- Payment Breakdown -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-black text-gray-800 border-b pb-3 mb-6">Payment Methods Breakdown</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @forelse($payments as $payment)
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 flex flex-col items-center justify-center shadow-sm">
                                <span class="font-bold text-gray-500 uppercase tracking-widest text-xs mb-2">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                                <span class="font-black text-2xl text-blue-600">${{ number_format($payment->total, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic col-span-3 text-center py-4 bg-gray-50 rounded">No payments recorded for this date.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Wallet Top-Ups Log -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 printable-area">
                <div class="p-6">
                    <h3 class="text-lg font-black text-purple-800 border-b pb-3 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                        Wallet Top-Ups ({{ $date }})
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-purple-50 border-b border-purple-100">
                                    <th class="py-3 px-4 text-xs font-bold text-purple-600 uppercase tracking-wider">Time</th>
                                    <th class="py-3 px-4 text-xs font-bold text-purple-600 uppercase tracking-wider">Student</th>
                                    <th class="py-3 px-4 text-xs font-bold text-purple-600 uppercase tracking-wider">Payment Method</th>
                                    <th class="py-3 px-4 text-xs font-bold text-purple-600 uppercase tracking-wider">Cashier</th>
                                    <th class="py-3 px-4 text-xs text-right font-bold text-purple-600 uppercase tracking-wider">Amount Added</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-purple-50">
                                @forelse($walletDepositsLog as $deposit)
                                <tr class="hover:bg-purple-50 transition-colors">
                                    <td class="py-3 px-4 text-sm text-gray-500 whitespace-nowrap">{{ $deposit->created_at->format('h:i A') }}</td>
                                    <td class="py-3 px-4 font-bold text-gray-800 text-sm whitespace-nowrap">{{ $deposit->student->name ?? 'Unknown' }}</td>
                                    <td class="py-3 px-4 font-bold text-gray-600 text-sm whitespace-nowrap uppercase">{{ $deposit->payment_method ?? 'Unknown' }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600 whitespace-nowrap">{{ $deposit->user->name ?? 'System' }}</td>
                                    <td class="py-3 px-4 text-right font-black text-green-600 whitespace-nowrap">+${{ number_format($deposit->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-8 px-4 text-center text-purple-400 bg-purple-50 shadow-inner">
                                        No wallet deposits recorded for this date.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Products Sold -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 printable-area">
                <div class="p-6">
                    <h3 class="text-lg font-black text-gray-800 border-b pb-3 mb-4">Products Sold ({{ $date }})</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Product Name</th>
                                    <th class="py-3 px-4 text-xs text-center font-bold text-gray-500 uppercase tracking-wider">Quantity Sold</th>
                                    <th class="py-3 px-4 text-xs text-right font-bold text-gray-500 uppercase tracking-wider">Gross Revenue (Pre-Discount)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($productSales as $sale)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 font-bold text-gray-700 text-sm whitespace-nowrap">{{ $sale->product_name }}</td>
                                    <td class="py-3 px-4 text-sm text-center font-black text-blue-600 whitespace-nowrap">{{ $sale->total_quantity }}</td>
                                    <td class="py-3 px-4 text-right font-black text-gray-900 whitespace-nowrap">${{ number_format($sale->total_revenue, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-8 px-4 text-center text-gray-500 bg-gray-50 shadow-inner">
                                        No products sold on this date.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg printable-area">
                <div class="p-6">
                    <h3 class="text-lg font-black text-gray-800 border-b pb-3 mb-4">Detailed Transactions ({{ $date }})</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Order #</th>
                                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Products</th>
                                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Cashier</th>
                                    <th class="py-3 px-4 text-xs text-right font-bold text-gray-500 uppercase tracking-wider">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 font-bold text-gray-700 text-sm whitespace-nowrap">{{ $order->order_number }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-500 whitespace-nowrap">{{ $order->created_at->format('h:i A') }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        @foreach($order->items as $item)
                                            <span class="inline-block bg-blue-50 border border-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded mr-1 mb-1">{{ $item->quantity }}x {{ $item->product_name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600 whitespace-nowrap">{{ $order->user->name ?? 'System' }}</td>
                                    <td class="py-3 px-4 text-right font-black text-gray-900 whitespace-nowrap">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-8 px-4 text-center text-gray-500 bg-gray-50 shadow-inner">
                                        No transactions recorded for this date.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <style>
        @media print {
            body { background: white !important; }
            nav, header, form, button { display: none !important; }
            .py-12 { padding-top: 0 !important; }
            .shadow-sm { box-shadow: none !important; border: 1px solid #ddd; }
        }
    </style>
</x-app-layout>
