<?php

namespace App\Filament\Resources\PhysicalSigningEvaluationResource\Pages;

use App\Filament\Resources\PhysicalSigningEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPhysicalSigningEvaluations extends ListRecords
{
    protected static string $resource = PhysicalSigningEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
