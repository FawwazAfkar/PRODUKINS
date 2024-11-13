<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesananResource\Pages;
use App\Filament\Resources\PesananResource\RelationManagers;
use App\Models\Pesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // id auto-generated

                // Tanggal Pesan
                DatePicker::make('tanggal_pesan')
                    ->label('Tanggal Pesan')
                    ->required()
                    ->default(now()),
                
                // Nama Produk
                TextInput::make('nama_produk')
                    ->label('Nama Produk')
                    ->placeholder('Masukkan Nama Produk')
                    ->required(),
                
                // Jumlah
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required()
                    ->default(1),
                
                // Nama Pelanggan
                TextInput::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->placeholder('Masukkan Nama Pelanggan')
                    ->required(),

                // Alamat Pengiriman
                TextInput::make('alamat_pengiriman')
                    ->label('Alamat Pengiriman')
                    ->placeholder('Masukkan Alamat Pengiriman')
                    ->required(),

                // Kontak Pelanggan
                TextInput::make('kontak_pelanggan')
                    ->label('Kontak Pelanggan')
                    ->placeholder('Masukkan Kontak Pelanggan')
                    ->required(),

                // Status Pesanan
                Select::make('status_pesanan')
                    ->label('Status Pesanan')
                    ->options([
                        'diproses' => 'Diproses',
                        'tertunda' => 'Tertunda',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                    ])
                    ->required()
                    ->default('diproses'),

                // Catatan
                TextInput::make('catatan')
                    ->label('Catatan')
                    ->placeholder('Masukkan Catatan')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Pesanan')
                    ->sortable(),

                TextColumn::make('tanggal_pesan')
                    ->label('Tanggal Pesan')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable(),

                TextColumn::make('status_pesanan')
                    ->label('Status Pesanan')
                    ->badge()
                    ->getStateUsing(fn ($record) => match ($record->status_pesanan) {
                        'diproses' => 'Diproses',
                        'tertunda' => 'Tertunda',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                    })
                    ->color(fn ($record) => match ($record->status_pesanan) {
                        'diproses' => 'primary',
                        'tertunda' => 'warning',
                        'dikirim' => 'info',
                        'selesai' => 'success',
                    }),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}
