<?php

namespace App\Filament\Resources\BranchManagerReviewResource\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\BranchManagerReviewResource;
use Filament\Notifications\Notification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranchManagerReview extends EditRecord
{
    protected static string $resource = BranchManagerReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //  Actions\DeleteAction::make(),
        ];
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['verified_by'] = auth()->user()->id;
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
