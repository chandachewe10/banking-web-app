<?php

namespace App\Filament\Resources\PhysicalSigningEvaluationResource\Pages;

use App\Filament\Resources\PhysicalSigningEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPhysicalSigningEvaluation extends ViewRecord
{
    protected static string $resource = PhysicalSigningEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
