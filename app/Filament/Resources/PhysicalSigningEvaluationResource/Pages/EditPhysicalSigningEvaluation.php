<?php

namespace App\Filament\Resources\PhysicalSigningEvaluationResource\Pages;

use App\Filament\Resources\PhysicalSigningEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPhysicalSigningEvaluation extends EditRecord
{
    protected static string $resource = PhysicalSigningEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
