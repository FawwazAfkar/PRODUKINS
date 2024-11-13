<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class MonthlyProductionTrend extends ChartWidget
{
    protected static ?string $heading = 'Tren Produksi Bulanan';
    protected int | string | array $columnSpan = '1';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => [
                [
                    'label' => 'Jumlah Produksi',
                    'data' => [110, 95, 85, 70, 80, 105, 90, 40, 55, 75, 95, 100],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.7)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
