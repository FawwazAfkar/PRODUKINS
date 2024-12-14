<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pesanan;

class OrderStatusDistribution extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Pesanan';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        // Get count of orders by status
        $orderCounts = Pesanan::query()
            ->selectRaw('status_pesanan, COUNT(*) as count')
            ->groupBy('status_pesanan')
            ->pluck('count', 'status_pesanan')
            ->toArray();

        // Calculate total orders
        $total = array_sum($orderCounts);

        // Calculate percentages and prepare labels
        $data = [];
        $labels = [];
        
        foreach ($orderCounts as $status => $count) {
            $labels[] = $status;
            $data[] = round(($count / $total) * 100, 1);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(255, 205, 86)', // Yellow for processing
                        'rgb(54, 162, 235)', // Blue for shipped
                        'rgb(75, 192, 192)', // Green for completed
                        'rgb(255, 99, 132)', // Red for other statuses
                    ],
                ],
            ],
        ];
    }
}