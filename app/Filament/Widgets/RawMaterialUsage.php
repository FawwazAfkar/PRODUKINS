<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class RawMaterialUsage extends ChartWidget
{
    protected static ?string $heading = 'Penggunaan Bahan Baku Utama';
    protected int | string | array $columnSpan = '1';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        return [
            'labels' => ['Kayu', 'Besi', 'Kain', 'Plastik', 'Cat'],
            'datasets' => [
                [
                    'label' => 'Jumlah Penggunaan (ton)',
                    'data' => [110, 90, 95, 80, 70],
                    'backgroundColor' => 'rgba(153, 102, 255, 0.7)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
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
