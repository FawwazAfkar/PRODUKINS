<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlatProduksiResource\Pages;
use App\Filament\Resources\AlatProduksiResource\RelationManagers;
use App\Models\AlatProduksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlatProduksiResource extends Resource
{
    protected static ?string $model = AlatProduksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function getLabel(): ?string
    {
        return 'Alat Produksi';
    }

    public static function getPluralLabel(): string
    {
        return 'Alat Produksi';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // id auto-generated

                // Nama Alat
                TextInput::make('nama_alat')
                    ->label('Nama Alat')
                    ->placeholder('Masukkan Nama Alat')
                    ->required(),
                
                // Tanggal Perawatan
                DatePicker::make('tanggal_perawatan')
                    ->label('Tanggal Perawatan')
                    ->required()
                    ->default(now()),
                
                // Status Alat
                Select::make('status_alat')
                    ->label('Status Alat')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'rusak' => 'Rusak',
                        'dalam_perawatan' => 'Dalam Perawatan',
                    ])
                    ->required()
                    ->default('tersedia'),
                
                // Cataan Perawatan
                TextInput::make('catatan_perawatan')
                    ->label('Catatan Perawatan')
                    ->placeholder('Masukkan Catatan Perawatan')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Alat')
                    ->sortable(),
                
                TextColumn::make('nama_alat')
                ->label('Nama Alat')
                ->wrap()
                ->searchable()
                ->sortable(),

                TextColumn::make('tanggal_perawatan')
                    ->label('Tanggal Perawatan')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('catatan_perawatan')
                ->label('Catatan')
                ->wrap()
                ->searchable(),

                TextColumn::make('status_alat')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn ($record) => match ($record->status_alat) {
                        'tersedia' => 'Tersedia',
                        'rusak' => 'Rusak',
                        'dalam_perawatan' => 'Dalam Perawatan',
                    })
                    ->color(fn ($record) => match ($record->status_alat) {
                        'tersedia' => 'success',
                        'rusak' => 'danger',
                        'dalam_perawatan' => 'warning',
                    }),
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
            'index' => Pages\ListAlatProduksis::route('/'),
            'create' => Pages\CreateAlatProduksi::route('/create'),
            'edit' => Pages\EditAlatProduksi::route('/{record}/edit'),
        ];
    }
}
