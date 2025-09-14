<?php

namespace App\Filament\Resources\PhysicalSigningEvaluationResource\Pages;

use App\Filament\Resources\PhysicalSigningEvaluationResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\PhysicalSigningEvaluation as Loans;
use App\Models\User;

class CreatePhysicalSigningEvaluation extends CreateRecord
{
    protected static string $resource = PhysicalSigningEvaluationResource::class;


    protected function handleRecordCreation(array $data): Model
    {


        $status = $data['physical_verification'];
        $data = Loans::where('loan_number', $data['loan_number'])->first();



        User::where('case_number', $data->case_number)
        ->update(['case_number' => null]);
        if (!$data) {
            Notification::make()
                ->title('Invalid Loan Number')
                ->body('The loan number you entered is invalid.')
                ->warning()
                ->send();
                 $this->halt();
        }


        if ($status === 'Loan has been approved') {

            $data->update([
                'physical_verification' => 1
            ]);
        } else {
            $data->update([
                'physical_verification' => 0
            ]);
        }



        return $data;
    }


    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }
}
