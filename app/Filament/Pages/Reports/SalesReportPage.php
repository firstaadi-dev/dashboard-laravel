<?php

namespace App\Filament\Pages\Reports;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class SalesReportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.reports.sales-report-page';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Sales Report';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public $startDate;
    public $endDate;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_sales_reports') || $user->hasRole('super_admin'));
    }

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');

        $this->form->fill([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->default(Carbon::now()->startOfMonth())
                    ->native(false),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->required()
                    ->default(Carbon::now())
                    ->native(false),
            ])
            ->columns(2)
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate Report')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    $this->startDate = $this->data['start_date'];
                    $this->endDate = $this->data['end_date'];
                }),

            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(fn () => $this->exportToPdf()),
        ];
    }

    public function getSalesData(): array
    {
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        $transactions = Transaction::where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $totalSales = $transactions->sum('total_amount');
        $transactionCount = $transactions->count();
        $averageTransaction = $transactionCount > 0 ? $totalSales / $transactionCount : 0;

        // Sales by day
        $salesByDay = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->transaction_date)->format('Y-m-d');
        })->map(function ($dayTransactions) {
            return $dayTransactions->sum('total_amount');
        })->toArray();

        // Top selling products
        $topProducts = TransactionItem::whereHas('transaction', function ($query) use ($startDate, $endDate) {
            $query->where('status', 'completed')
                ->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->with('product')
        ->get()
        ->groupBy('product_id')
        ->map(function ($items) {
            return [
                'product' => $items->first()->product?->name ?? 'Unknown',
                'quantity' => $items->sum('quantity'),
                'total' => $items->sum('subtotal'),
            ];
        })
        ->sortByDesc('total')
        ->take(10)
        ->values()
        ->toArray();

        // Payment methods breakdown
        $paymentMethods = $transactions->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount'),
                ];
            })
            ->toArray();

        return [
            'total_sales' => $totalSales,
            'transaction_count' => $transactionCount,
            'average_transaction' => $averageTransaction,
            'sales_by_day' => $salesByDay,
            'top_products' => $topProducts,
            'payment_methods' => $paymentMethods,
        ];
    }

    public function exportToPdf()
    {
        $data = $this->getSalesData();
        $data['start_date'] = $this->startDate;
        $data['end_date'] = $this->endDate;

        $pdf = Pdf::loadView('pdf.sales-report', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'sales-report-' . date('Y-m-d') . '.pdf');
    }
}
