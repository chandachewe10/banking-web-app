<?php

namespace App\Filament\Resources\CreditEvaluationResource\Pages;

use App\Filament\Resources\CreditEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCreditEvaluation extends CreateRecord
{
    protected static string $resource = CreditEvaluationResource::class;




      protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Evaluated')
            ->body('Credit evaluation successful.');
    }

}
