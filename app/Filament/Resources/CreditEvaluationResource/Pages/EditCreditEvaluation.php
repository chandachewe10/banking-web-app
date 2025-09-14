<?php

namespace App\Filament\Resources\CreditEvaluationResource\Pages;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\CreditEvaluationResource;
use Filament\Notifications\Notification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCreditEvaluation extends EditRecord
{
    protected static string $resource = CreditEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
           // Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['verified_by'] =auth()->user()->id;
        $record->update($data);





















        return $record;
    }


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
