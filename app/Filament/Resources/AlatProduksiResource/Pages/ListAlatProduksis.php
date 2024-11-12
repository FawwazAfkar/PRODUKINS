<?php

namespace App\Filament\Resources\AlatProduksiResource\Pages;

use App\Filament\Resources\AlatProduksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlatProduksis extends ListRecords
{
    protected static string $resource = AlatProduksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
