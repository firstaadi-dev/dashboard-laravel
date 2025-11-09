<?php

namespace App\Filament\Pages\Reports;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Transaction;
use App\Models\PurchaseOrder;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;

class FinancialReportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected string $view = 'filament.pages.reports.financial-report-page';

    protected static UnitEnum|string|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Financial Report';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public $startDate;
    public $endDate;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_financial_reports') || $user->hasRole('super_admin'));
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    public function getFinancialData(): array
    {
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        // Revenue from completed transactions
        $revenue = Transaction::where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Cost of Goods Sold from purchase orders
        $cogs = PurchaseOrder::whereIn('status', ['received', 'approved'])
            ->whereBetween('order_date', [$startDate, $endDate])
            ->sum('total_amount');

        $grossProfit = $revenue - $cogs;
        $grossProfitMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        // Operating Expenses from journal entries (expense accounts)
        $expenseAccounts = Account::where('type', 'expense')->pluck('id');

        $operatingExpenses = JournalEntryLine::whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
            $query->where('status', 'posted')
                ->whereBetween('entry_date', [$startDate, $endDate]);
        })
        ->whereIn('account_id', $expenseAccounts)
        ->sum('debit');

        $netProfit = $grossProfit - $operatingExpenses;
        $netProfitMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        // Balance Sheet
        $assets = $this->getAccountsBalance('asset', $endDate);
        $liabilities = $this->getAccountsBalance('liability', $endDate);
        $equity = $this->getAccountsBalance('equity', $endDate);

        $totalAssets = collect($assets)->sum('balance');
        $totalLiabilities = collect($liabilities)->sum('balance');
        $totalEquity = collect($equity)->sum('balance') + $netProfit; // Add current period profit to equity

        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'gross_profit_margin' => $grossProfitMargin,
            'operating_expenses' => $operatingExpenses,
            'net_profit' => $netProfit,
            'net_profit_margin' => $netProfitMargin,
            'assets' => $assets,
            'total_assets' => $totalAssets,
            'liabilities' => $liabilities,
            'total_liabilities' => $totalLiabilities,
            'equity' => $equity,
            'total_equity' => $totalEquity,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];
    }

    private function getAccountsBalance(string $type, $endDate): array
    {
        $accounts = Account::where('type', $type)
            ->where('is_active', true)
            ->get();

        $balances = [];
        foreach ($accounts as $account) {
            $debit = JournalEntryLine::whereHas('journalEntry', function ($query) use ($endDate) {
                $query->where('status', 'posted')
                    ->where('entry_date', '<=', $endDate);
            })
            ->where('account_id', $account->id)
            ->sum('debit');

            $credit = JournalEntryLine::whereHas('journalEntry', function ($query) use ($endDate) {
                $query->where('status', 'posted')
                    ->where('entry_date', '<=', $endDate);
            })
            ->where('account_id', $account->id)
            ->sum('credit');

            // Calculate balance based on account type
            $balance = match ($type) {
                'asset', 'expense' => $debit - $credit,
                'liability', 'equity', 'revenue' => $credit - $debit,
                default => 0,
            };

            if ($balance != 0) {
                $balances[] = [
                    'account' => $account->name,
                    'code' => $account->code,
                    'balance' => $balance,
                ];
            }
        }

        return $balances;
    }

    public function exportToPdf()
    {
        $data = $this->getFinancialData();

        $pdf = Pdf::loadView('pdf.financial-report', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'financial-report-' . date('Y-m-d') . '.pdf');
    }
}
