<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Products\Widgets\StockWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // ...
    public function getWidgets(): array
    {
        return [
            StockWidget::class,
        ];
    }
}
