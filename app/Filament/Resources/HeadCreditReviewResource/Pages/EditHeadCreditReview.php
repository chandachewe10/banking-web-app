<?php

namespace App\Filament\Resources\HeadCreditReviewResource\Pages;

use App\Filament\Resources\HeadCreditReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EditHeadCreditReview extends EditRecord
{
    protected static string $resource = HeadCreditReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
         //   Actions\DeleteAction::make(),
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
            ->body('Evaluation successful.');
    }
}
