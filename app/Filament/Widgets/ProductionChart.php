<?php

namespace App\Filament\Widgets;

use App\Models\Produksi;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ProductionChart extends ChartWidget
{
    protected static ?string $heading = 'Produksi Bulanan';
    protected static ?int $sort = 1;
    protected static string $color = 'info';

    protected function getData(): array
    {
        //
        $data = Trend::model(Produksi::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Produksi',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
