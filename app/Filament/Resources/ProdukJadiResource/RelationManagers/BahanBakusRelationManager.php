<?php

namespace App\Filament\Resources\ProdukJadiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BahanBakusRelationManager extends RelationManager
{
    protected static string $relationship = 'bahanBakus';

    protected static ?string $title = 'Bahan Baku';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_bahan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('jumlah')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_bahan')
            ->columns([
                TextColumn::make('nama_bahan'),
                TextInputColumn::make('jumlah'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->default(1)
                            ->rules(['required', 'numeric', 'min:1']),
                    ]
                ),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
