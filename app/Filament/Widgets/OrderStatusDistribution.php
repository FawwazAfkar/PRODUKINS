<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class OrderStatusDistribution extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Pesanan';
    protected int | string | array $columnSpan = '1';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        return [
            'labels' => ['Dalam Proses', 'Dikirim', 'Selesai'],
            'datasets' => [
                [
                    'data' => [35, 20, 45], // Percentage distribution
                    'backgroundColor' => [
                        'rgba(255, 205, 86, 0.7)', // Yellow
                        'rgba(54, 162, 235, 0.7)',  // Blue
                        'rgba(75, 192, 192, 0.7)',  // Green
                    ],
                    'borderColor' => [
                        'rgba(255, 205, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
