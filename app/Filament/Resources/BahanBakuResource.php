<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BahanBakuResource\Pages;
use App\Filament\Resources\BahanBakuResource\RelationManagers;
use App\Models\BahanBaku;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BahanBakuResource extends Resource
{
    protected static ?string $model = BahanBaku::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    public static function getLabel(): ?string
    {
        return 'Bahan Baku';
    }
    public static function getPluralLabel(): string
    {
        return 'Bahan Baku';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_bahan')
                    ->label('Nama Bahan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('stok')
                    ->label('Stok')
                    ->required()
                    ->maxLength(255),
                TextInput::make('unit')
                    ->label('Satuan')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_bahan')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Bahan'),
                TextColumn::make('stok_with_unit')
                    ->label('Stok')
                    ->getStateUsing(fn($record) => $record->stok . ' ' . $record->unit)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('Tambah Stok') // Custom Add Stock Action
                    ->getStateUsing(fn() => true)
                    ->icon('heroicon-o-plus-circle')
                    ->action(
                        Action::make('addStock') 
                        ->label('Tambah Stok')
                        ->icon('heroicon-o-plus-circle')
                        ->action(function (BahanBaku $record, array $data): void {
                            $record->increment('stok', $data['amount']);
                        })
                        ->form([
                            Forms\Components\TextInput::make('amount')
                                ->label('Stock yang Ditambah')
                                ->required()
                                ->numeric()
                                ->minValue(1),
                        ]),
                    )
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
            'index' => Pages\ListBahanBakus::route('/'),
            'create' => Pages\CreateBahanBaku::route('/create'),
            'edit' => Pages\EditBahanBaku::route('/{record}/edit'),
        ];
    }
}
