<?php

namespace App\Filament\Resources\DisbursementsResource\Pages;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\DisbursementsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditDisbursements extends EditRecord
{
    protected static string $resource = DisbursementsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
          //  Actions\DeleteAction::make(),
        ];
    }

protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['verified_by'] =auth()->user()->id;
        $data['loan_status'] = $data['verified_by'] == 1 ? 'approved' : 'denied';
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
