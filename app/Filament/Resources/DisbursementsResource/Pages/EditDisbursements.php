<?php

namespace App\Filament\Resources\DisbursementsResource\Pages;

use App\Filament\Resources\DisbursementsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDisbursements extends EditRecord
{
    protected static string $resource = DisbursementsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
