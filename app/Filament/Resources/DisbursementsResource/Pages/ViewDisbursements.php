<?php

namespace App\Filament\Resources\DisbursementsResource\Pages;

use App\Filament\Resources\DisbursementsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDisbursements extends ViewRecord
{
    protected static string $resource = DisbursementsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
