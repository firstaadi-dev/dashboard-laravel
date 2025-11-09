<?php

namespace App\Filament\Resources\Employees;

use App\Exports\EmployeesExport;
use App\Filament\Resources\Employees\Pages\ManageEmployees;
use BackedEnum;
use UnitEnum;
use App\Filament\Tables\Columns;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static UnitEnum|string|null $navigationGroup = 'Human Resources';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_employees') || $user->hasRole('super_admin'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('create_employees') || $user->hasRole('super_admin'));
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('edit_employees') || $user->hasRole('super_admin'));
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_employees') || $user->hasRole('super_admin'));
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_employees') || $user->hasRole('super_admin'));
    }

    public static function canForceDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_employees') || $user->hasRole('super_admin'));
    }

    public static function canForceDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_employees') || $user->hasRole('super_admin'));
    }

    public static function canRestore($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_employees') || $user->hasRole('super_admin'));
    }

    public static function canRestoreAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_employees') || $user->hasRole('super_admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('employee_code')
                            ->label('Employee Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->alphaDash()
                            ->placeholder('e.g., EMP-001'),

                        Select::make('user_id')
                            ->label('User Account')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Link to system user account (optional)'),

                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),

                        DatePicker::make('birth_date')
                            ->label('Date of Birth')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()->subYears(17)),

                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Address Information')
                    ->schema([
                        Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->maxLength(255),

                        TextInput::make('province')
                            ->maxLength(255),

                        TextInput::make('id_card_number')
                            ->label('ID Card Number (KTP)')
                            ->maxLength(255)
                            ->placeholder('e.g., 1234567890123456'),

                        TextInput::make('tax_id')
                            ->label('Tax ID (NPWP)')
                            ->maxLength(255)
                            ->placeholder('e.g., 12.345.678.9-012.345'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Employment Details')
                    ->schema([
                        TextInput::make('position')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Senior Developer'),

                        TextInput::make('department')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., IT Department'),

                        Select::make('employment_status')
                            ->label('Employment Status')
                            ->options([
                                'permanent' => 'Permanent',
                                'contract' => 'Contract',
                                'probation' => 'Probation',
                                'internship' => 'Internship',
                            ])
                            ->required()
                            ->default('contract')
                            ->native(false),

                        DatePicker::make('join_date')
                            ->label('Join Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->nullable()
                            ->helperText('Leave empty for active employees'),

                        TextInput::make('basic_salary')
                            ->label('Basic Salary')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp.')
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->inputMode('decimal'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_code')
                    ->label('Employee Code')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Code copied!')
                    ->weight('bold'),

                TextColumn::make('first_name')
                    ->label('First Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('position')
                    ->label('Position')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('department')
                    ->label('Department')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('employment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'permanent' => 'success',
                        'contract' => 'info',
                        'probation' => 'warning',
                        'internship' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                TextColumn::make('join_date')
                    ->label('Join Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('basic_salary')
                    ->label('Salary')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                ...Columns::timestamps(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('department')
                    ->label('Department')
                    ->options(fn () => Employee::query()
                        ->distinct()
                        ->pluck('department', 'department')
                        ->toArray())
                    ->native(false),

                SelectFilter::make('employment_status')
                    ->label('Employment Status')
                    ->options([
                        'permanent' => 'Permanent',
                        'contract' => 'Contract',
                        'probation' => 'Probation',
                        'internship' => 'Internship',
                    ])
                    ->native(false),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ])
                    ->native(false),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => Excel::download(new EmployeesExport(), 'employees-' . date('Y-m-d') . '.xlsx'))
                    ->color('success'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployees::route('/'),
        ];
    }
}
