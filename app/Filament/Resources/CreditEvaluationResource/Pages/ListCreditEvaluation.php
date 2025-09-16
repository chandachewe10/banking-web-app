<?php

namespace App\Filament\Resources\CreditEvaluationResource\Pages;

use App\Filament\Resources\CreditEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditEvaluation extends ListRecords
{
    protected static string $resource = CreditEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
