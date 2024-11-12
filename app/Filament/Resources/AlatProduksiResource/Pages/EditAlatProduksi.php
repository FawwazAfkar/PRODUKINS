<?php

namespace App\Filament\Resources\AlatProduksiResource\Pages;

use App\Filament\Resources\AlatProduksiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlatProduksi extends EditRecord
{
    protected static string $resource = AlatProduksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
