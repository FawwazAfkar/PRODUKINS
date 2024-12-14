<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class MonthlyProductionTrend extends ChartWidget
{
    protected static ?string $heading = 'Tren Produksi Bulanan';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    // Add filter property
    public ?string $filter = null;

    // Set default year on mount
    public function mount(): void
    {
        $this->filter = now()->year;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    // Configure filter
    protected function getFilters(): ?array
    {
        return $this->getYearFilterOptions();
    }

    // Get available years for filter
    protected function getYearFilterOptions(): array
    {
        $years = \App\Models\Produksi::selectRaw('YEAR(tanggal_mulai) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return array_combine($years, $years);
    }

    // Modify getData to use selected year
    protected function getData(): array
    {
        $year = $this->filter ?? now()->year;
        
        $monthlyProduction = \App\Models\Produksi::query()
        ->selectRaw('MONTH(tanggal_mulai) as month, SUM(jumlah_produksi) as total')
        ->whereYear('tanggal_mulai', $year)
        ->where('status_produksi', 'selesai')
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $monthlyData = array_fill(1, 12, 0);
    foreach ($monthlyProduction as $month => $total) {
        $monthlyData[$month] = $total;
    }

    $data = [
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        'data' => $monthlyData
    ];

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'datasets' => [
                [
                    'label' => 'Jumlah Produksi',
                    'data' => array_values($monthlyData),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.7)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
}