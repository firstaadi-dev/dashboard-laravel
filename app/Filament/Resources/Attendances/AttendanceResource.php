<?php

namespace App\Filament\Resources\Attendances;

use App\Filament\Resources\Attendances\Pages\ManageAttendances;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';

    public static function canViewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_attendances') || $user->hasRole('super_admin');
    }

    public static function canCreate(User $user): bool
    {
        return $user->hasPermissionTo('create_attendances') || $user->hasRole('super_admin');
    }

    public static function canEdit(User $user, $record): bool
    {
        return $user->hasPermissionTo('edit_attendances') || $user->hasRole('super_admin');
    }

    public static function canDelete(User $user, $record): bool
    {
        return $user->hasPermissionTo('delete_attendances') || $user->hasRole('super_admin');
    }

    public static function canDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_attendances') || $user->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'first_name')
                    ->searchable(['first_name', 'last_name'])
                    ->preload()
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn (Employee $record) => "{$record->first_name} {$record->last_name}"),

                DatePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->default(now())
                    ->native(false),

                TimePicker::make('clock_in')
                    ->label('Clock In')
                    ->seconds(false)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::calculateWorkHours($get, $set);
                    }),

                TimePicker::make('clock_out')
                    ->label('Clock Out')
                    ->seconds(false)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::calculateWorkHours($get, $set);
                    }),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'sick' => 'Sick',
                        'permission' => 'Permission',
                        'holiday' => 'Holiday',
                    ])
                    ->required()
                    ->default('present')
                    ->native(false),

                TextInput::make('work_hours')
                    ->label('Work Hours')
                    ->disabled()
                    ->dehydrated()
                    ->suffix('minutes')
                    ->numeric(),

                TextInput::make('overtime_hours')
                    ->label('Overtime Hours')
                    ->disabled()
                    ->dehydrated()
                    ->default(0)
                    ->suffix('minutes')
                    ->numeric(),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    protected static function calculateWorkHours(Get $get, Set $set): void
    {
        $clockIn = $get('clock_in');
        $clockOut = $get('clock_out');

        if ($clockIn && $clockOut) {
            try {
                $start = Carbon::parse($clockIn);
                $end = Carbon::parse($clockOut);

                $workMinutes = $start->diffInMinutes($end);
                $set('work_hours', $workMinutes);
            } catch (\Exception $e) {
                $set('work_hours', 0);
            }
        } else {
            $set('work_hours', 0);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->formatStateUsing(fn ($record) => $record->employee ? "{$record->employee->first_name} {$record->employee->last_name}" : '-')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('clock_in')
                    ->label('Clock In')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('clock_out')
                    ->label('Clock Out')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'sick' => 'info',
                        'permission' => 'gray',
                        'holiday' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('work_hours')
                    ->label('Work Hours')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $hours = floor($state / 60);
                        $minutes = $state % 60;
                        return sprintf('%02d:%02d', $hours, $minutes);
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'sick' => 'Sick',
                        'permission' => 'Permission',
                        'holiday' => 'Holiday',
                    ]),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('From Date'),
                        DatePicker::make('date_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttendances::route('/'),
        ];
    }
}
