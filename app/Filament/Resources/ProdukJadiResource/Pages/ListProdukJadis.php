<?php

namespace App\Filament\Resources\ProdukJadiResource\Pages;

use App\Filament\Resources\ProdukJadiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProdukJadis extends ListRecords
{
    protected static string $resource = ProdukJadiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
