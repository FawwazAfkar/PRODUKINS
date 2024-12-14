<?php

namespace App\Filament\Widgets;

use App\Models\Produksi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?string $heading = 'Produksi Terakhir';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Produksi::query() 
                ->latest('id') 
                ->limit(10) 
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID Produksi')
                    ->sortable(),

                TextColumn::make('produkJadi.nama_produk')
                    ->label('Nama Produk')
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('jumlah_produksi')
                    ->label('Jumlah Produksi')
                    ->sortable(),

                TextColumn::make('bahan_baku')
                    ->label('Bahan Baku')
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status_produksi')
                    ->badge()
                    ->label('Status Produksi')
                    ->getStateUsing(fn ($record) => match ($record->status_produksi) {
                        'proses' => 'Dalam Proses',
                        'pending' => 'Pending',
                        'selesai' => 'Selesai',
                    })
                    ->color(fn ($record) => match ($record->status_produksi) {
                        'proses' => 'warning',
                        'pending' => 'gray',
                        'selesai' => 'success',
                    })
            ]);
    }
}
