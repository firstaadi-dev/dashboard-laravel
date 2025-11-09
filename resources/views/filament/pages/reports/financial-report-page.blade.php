<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters Form --}}
        <x-filament::section>
            <x-slot name="heading">
                Report Period
            </x-slot>

            <form wire:submit="generate">
                {{ $this->form }}
            </form>
        </x-filament::section>

        @php
            $financialData = $this->getFinancialData();
        @endphp

        {{-- Profit & Loss Statement --}}
        <x-filament::section>
            <x-slot name="heading">
                Profit & Loss Statement
            </x-slot>
            <x-slot name="description">
                Period: {{ Carbon\Carbon::parse($financialData['start_date'])->format('d M Y') }} - {{ Carbon\Carbon::parse($financialData['end_date'])->format('d M Y') }}
            </x-slot>

            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Revenue</span>
                    <span class="text-lg font-bold text-success-600">Rp {{ number_format($financialData['revenue'], 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Cost of Goods Sold</span>
                    <span class="text-lg font-medium text-gray-600 dark:text-gray-400">(Rp {{ number_format($financialData['cogs'], 0, ',', '.') }})</span>
                </div>

                <div class="flex justify-between items-center py-3 bg-blue-50 dark:bg-blue-900/20 px-4 rounded-lg">
                    <div>
                        <span class="font-bold text-gray-900 dark:text-gray-100">Gross Profit</span>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">({{ number_format($financialData['gross_profit_margin'], 2) }}%)</span>
                    </div>
                    <span class="text-xl font-bold text-primary-600">Rp {{ number_format($financialData['gross_profit'], 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Operating Expenses</span>
                    <span class="text-lg font-medium text-gray-600 dark:text-gray-400">(Rp {{ number_format($financialData['operating_expenses'], 0, ',', '.') }})</span>
                </div>

                <div class="flex justify-between items-center py-4 bg-green-50 dark:bg-green-900/20 px-4 rounded-lg">
                    <div>
                        <span class="text-xl font-bold text-gray-900 dark:text-gray-100">Net Profit</span>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">({{ number_format($financialData['net_profit_margin'], 2) }}%)</span>
                    </div>
                    <span class="text-2xl font-bold {{ $financialData['net_profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                        Rp {{ number_format($financialData['net_profit'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </x-filament::section>

        {{-- Balance Sheet --}}
        <x-filament::section>
            <x-slot name="heading">
                Balance Sheet
            </x-slot>
            <x-slot name="description">
                As of {{ Carbon\Carbon::parse($financialData['end_date'])->format('d M Y') }}
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Assets --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Assets</h3>
                    <div class="space-y-2">
                        @forelse($financialData['assets'] as $asset)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $asset['account'] }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $asset['code'] }}</div>
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($asset['balance'], 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No asset accounts found</p>
                        @endforelse

                        <div class="flex justify-between items-center py-3 bg-primary-50 dark:bg-primary-900/20 px-4 rounded-lg mt-3">
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">Total Assets</span>
                            <span class="text-xl font-bold text-primary-600">
                                Rp {{ number_format($financialData['total_assets'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Liabilities & Equity --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Liabilities & Equity</h3>
                    <div class="space-y-4">
                        {{-- Liabilities --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Liabilities</h4>
                            @forelse($financialData['liabilities'] as $liability)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $liability['account'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $liability['code'] }}</div>
                                    </div>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($liability['balance'], 0, ',', '.') }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No liability accounts</p>
                            @endforelse
                        </div>

                        {{-- Equity --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Equity</h4>
                            @forelse($financialData['equity'] as $equity)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $equity['account'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $equity['code'] }}</div>
                                    </div>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($equity['balance'], 0, ',', '.') }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No equity accounts</p>
                            @endforelse
                        </div>

                        <div class="flex justify-between items-center py-3 bg-success-50 dark:bg-success-900/20 px-4 rounded-lg">
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">Total Liabilities & Equity</span>
                            <span class="text-xl font-bold text-success-600">
                                Rp {{ number_format($financialData['total_liabilities'] + $financialData['total_equity'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Balance Check --}}
            <div class="mt-6 p-4 rounded-lg {{ abs($financialData['total_assets'] - ($financialData['total_liabilities'] + $financialData['total_equity'])) < 0.01 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Balance Verification</span>
                    @if(abs($financialData['total_assets'] - ($financialData['total_liabilities'] + $financialData['total_equity'])) < 0.01)
                        <span class="flex items-center text-green-600">
                            <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5 mr-1" />
                            Balanced
                        </span>
                    @else
                        <span class="flex items-center text-red-600">
                            <x-filament::icon icon="heroicon-o-x-circle" class="w-5 h-5 mr-1" />
                            Out of Balance
                        </span>
                    @endif
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
