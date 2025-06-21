<?php

namespace App\Filament\Resources\DisbursementsResource\Pages;

use App\Filament\Resources\DisbursementsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDisbursements extends ListRecords
{
    protected static string $resource = DisbursementsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
