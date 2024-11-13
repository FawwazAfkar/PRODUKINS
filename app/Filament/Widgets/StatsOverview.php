<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Produksi;
use App\Models\Pesanan;
use App\Models\BahanBaku;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Produksi', Produksi::count())
                ->icon('heroicon-o-presentation-chart-line')
                ->color('blue'),
            Stat::make('Pesanan', Pesanan::count())
                ->icon('heroicon-o-shopping-cart')
                ->color('green'),
            Stat::make('Bahan Baku', BahanBaku::count())
                ->icon('heroicon-o-wallet')
                ->color('yellow'),
        ];
    }
}
