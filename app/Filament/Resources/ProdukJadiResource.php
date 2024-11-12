<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukJadiResource\Pages;
use App\Filament\Resources\ProdukJadiResource\RelationManagers;
use App\Models\ProdukJadi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Livewire\wrap;

class ProdukJadiResource extends Resource
{
    protected static ?string $model = ProdukJadi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // id auto-generated

                // Nama Produk
                TextInput::make('nama_produk')
                    ->label('Nama Produk')
                    ->placeholder('Masukkan Nama Produk')
                    ->required(),
                
                // Kategori
                TextInput::make('kategori')
                    ->label('Kategori')
                    ->placeholder('Masukkan Kategori Produk')
                    ->required(),

                // Bahan Baku
                TextInput::make('bahan_baku')
                    ->label('Bahan Baku')
                    ->required()
                    ->maxLength(255),
                
                // Stok
                TextInput::make('stok')
                    ->label('Stok')
                    ->required()
                    ->default(0),
                
                // Harga
                TextInput::make('harga')
                    ->label('Harga')
                    ->required()
                    ->default(0),

                // Status Produk (with a select field)
                Select::make('status_produk')
                    ->label('Status Produk')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'dalam_proses' => 'Dalam Proses',
                        'tidak_tersedia' => 'Tidak Tersedia',
                    ])
                    ->default('tersedia')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bahan_baku')
                    ->label('Bahan Baku')
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('stok')
                    ->label('Stok')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')) // menurut PEUBI
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status_produk')
                    ->badge()
                    ->label('Status Produk')
                    ->getStateUsing(fn ($record) => match ($record->status_produk) {
                        'tersedia' => 'Tersedia',
                        'dalam_proses' => 'Dalam Proses',
                        'tidak_tersedia' => 'Tidak Tersedia',
                    })
                    ->color(fn ($record) => match ($record->status_produk) {
                        'tersedia' => 'success',
                        'dalam_proses' => 'warning',
                        'tidak_tersedia' => 'danger',
                    })
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
            'index' => Pages\ListProdukJadis::route('/'),
            'create' => Pages\CreateProdukJadi::route('/create'),
            'edit' => Pages\EditProdukJadi::route('/{record}/edit'),
        ];
    }
}
