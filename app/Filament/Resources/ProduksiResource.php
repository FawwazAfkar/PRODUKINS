<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduksiResource\Pages;
use App\Filament\Resources\ProduksiResource\RelationManagers;
use App\Models\Produksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProduksiResource extends Resource
{
    protected static ?string $model = Produksi::class;

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
                
                // Tanggal Mulai
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now()),
                
                // Jam Mulai
                TimePicker::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->required()
                    ->default(now()->format('H:i')),

                // Tanggal Selesai
                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->default(now()),

                // Jam Selesai
                TimePicker::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->required()
                    ->default(now()->format('H:i')),

                // Jumlah Produksi
                TextInput::make('jumlah_produksi')
                    ->label('Jumlah Produksi')
                    ->required()
                    ->numeric()
                    ->minValue(1),

                // Bahan Baku
                TextInput::make('bahan_baku')
                    ->label('Bahan Baku')
                    ->required()
                    ->maxLength(255),

                // Status Produksi (with a select field)
                Select::make('status_produksi')
                    ->label('Status Produksi')
                    ->options([
                        'active' => 'Dalam Proses',
                        'pending' => 'Pending',
                        'completed' => 'Selesai',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Produksi')
                    ->sortable(),

                TextColumn::make('nama_produk')
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
            'index' => Pages\ListProduksis::route('/'),
            'create' => Pages\CreateProduksi::route('/create'),
            'edit' => Pages\EditProduksi::route('/{record}/edit'),
        ];
    }
}
