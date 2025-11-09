<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $inventoryData = $this->getInventoryData();
        @endphp

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::section>
                <x-slot name="heading">
                    Total Products
                </x-slot>
                <div class="text-3xl font-bold text-primary-600">
                    {{ number_format($inventoryData['total_products']) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Stock Value
                </x-slot>
                <div class="text-3xl font-bold text-success-600">
                    Rp {{ number_format($inventoryData['total_stock_value'], 0, ',', '.') }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Low Stock Items
                </x-slot>
                <div class="text-3xl font-bold text-warning-600">
                    {{ number_format($inventoryData['low_stock_count']) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Out of Stock
                </x-slot>
                <div class="text-3xl font-bold text-danger-600">
                    {{ number_format($inventoryData['out_of_stock_count']) }}
                </div>
            </x-filament::section>
        </div>

        {{-- Stock Movement Summary --}}
        <x-filament::section>
            <x-slot name="heading">
                Stock Movement (Last 30 Days)
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Stock In</div>
                        <div class="text-2xl font-bold text-green-600">{{ number_format($inventoryData['stock_in_30_days']) }}</div>
                    </div>
                    <x-filament::icon
                        icon="heroicon-o-arrow-down-tray"
                        class="w-12 h-12 text-green-600"
                    />
                </div>

                <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Stock Out</div>
                        <div class="text-2xl font-bold text-red-600">{{ number_format($inventoryData['stock_out_30_days']) }}</div>
                    </div>
                    <x-filament::icon
                        icon="heroicon-o-arrow-up-tray"
                        class="w-12 h-12 text-red-600"
                    />
                </div>
            </div>
        </x-filament::section>

        {{-- Low Stock Products Alert --}}
        @if($inventoryData['low_stock_products']->count() > 0)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon
                        icon="heroicon-o-exclamation-triangle"
                        class="w-5 h-5 text-warning-600"
                    />
                    Low Stock Products Alert
                </div>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Min Stock</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($inventoryData['low_stock_products'] as $product)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $product->SKU }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $product->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $product->category?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($product->stock) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ number_format($product->min_stock ?? 20) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($product->stock <= 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            Out of Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            Low Stock
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
        @endif

        {{-- Recent Stock Movements --}}
        <x-filament::section>
            <x-slot name="heading">
                Recent Stock Movements (Last 30 Days)
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Previous</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">New Stock</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($inventoryData['stock_movements'] as $movement)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $movement->movement_date->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $movement->product?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($movement->type === 'in')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            In
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            Out
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($movement->quantity) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ number_format($movement->previous_stock) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($movement->new_stock) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">
                                    No stock movements found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
