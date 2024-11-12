<?php

namespace App\Filament\Resources\ProdukJadiResource\Pages;

use App\Filament\Resources\ProdukJadiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProdukJadi extends EditRecord
{
    protected static string $resource = ProdukJadiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
