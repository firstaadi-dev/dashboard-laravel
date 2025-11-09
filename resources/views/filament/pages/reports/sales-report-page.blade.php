<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters Form --}}
        <x-filament::section>
            <x-slot name="heading">
                Report Filters
            </x-slot>

            <form wire:submit="generate">
                {{ $this->form }}
            </form>
        </x-filament::section>

        @php
            $salesData = $this->getSalesData();
        @endphp

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::section>
                <x-slot name="heading">
                    Total Sales
                </x-slot>
                <div class="text-3xl font-bold text-primary-600">
                    Rp {{ number_format($salesData['total_sales'], 0, ',', '.') }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Total Transactions
                </x-slot>
                <div class="text-3xl font-bold text-success-600">
                    {{ number_format($salesData['transaction_count']) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Average Transaction
                </x-slot>
                <div class="text-3xl font-bold text-info-600">
                    Rp {{ number_format($salesData['average_transaction'], 0, ',', '.') }}
                </div>
            </x-filament::section>
        </div>

        {{-- Sales Chart --}}
        <x-filament::section>
            <x-slot name="heading">
                Sales Over Time
            </x-slot>

            <div>
                <canvas id="salesChart" height="80"></canvas>
            </div>

            @script
            <script>
                const salesCtx = document.getElementById('salesChart');
                const salesData = @js($salesData['sales_by_day']);

                new Chart(salesCtx, {
                    type: 'line',
                    data: {
                        labels: Object.keys(salesData),
                        datasets: [{
                            label: 'Sales (Rp)',
                            data: Object.values(salesData),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
            @endscript
        </x-filament::section>

        {{-- Top Selling Products --}}
        <x-filament::section>
            <x-slot name="heading">
                Top Selling Products
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity Sold</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($salesData['top_products'] as $product)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $product['product'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ number_format($product['quantity']) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($product['total'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">
                                    No products found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Payment Methods --}}
        <x-filament::section>
            <x-slot name="heading">
                Payment Methods Breakdown
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment Method</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transaction Count</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($salesData['payment_methods'] as $method => $data)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ ucfirst($method) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ number_format($data['count']) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($data['total'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">
                                    No payment methods found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>

    @assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @endassets
</x-filament-panels::page>
