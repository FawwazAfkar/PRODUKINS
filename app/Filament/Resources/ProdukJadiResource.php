<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukJadiResource\Pages;
use App\Filament\Resources\ProdukJadiResource\RelationManagers;
use App\Filament\Resources\ProdukJadiResource\RelationManagers\BahanBakusRelationManager;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Livewire\wrap;

class ProdukJadiResource extends Resource
{
    protected static ?string $model = ProdukJadi::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $title = 'Produk Jadi';
    protected static ?string $slug = 'produk-jadi';

    public static function form(Form $form): Form{
    return $form
        ->schema([
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

            // Harga
            TextInput::make('harga')
                ->label('Harga')
                ->required()
                ->default(0),

            // Stok
            TextInput::make('stok')
                ->label('Stok')
                ->required()
                ->readOnly()
                ->default(0),
            
            // Picture upload
            FileUpload::make('gambar')
            ->label('Gambar Produk')
            ->image()
            ->disk('public')
            ->directory('gambar_produk_jadi')
            ->required(false),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->disk('public')
                    ->size(100)
                    ->getStateUsing(function ($record) {
                        return asset('storage/' . $record->gambar);
                    }),

                TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('harga')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')) // menurut PEUBI
                    ->searchable()
                    ->sortable(),

                TextColumn::make('stok')
                    ->label('Stok')
                    ->searchable()
                    ->sortable(),

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
            BahanBakusRelationManager::class,
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
