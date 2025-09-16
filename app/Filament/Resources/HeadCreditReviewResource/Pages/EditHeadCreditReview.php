<?php

namespace App\Filament\Resources\HeadCreditReviewResource\Pages;

use App\Filament\Resources\HeadCreditReviewResource;
use App\Models\BranchManagerEvaluation;
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
        $data['verified_by'] = auth()->user()->id;
        $record->update($data);

        // Add Data to the second step
        BranchManagerEvaluation::create(

            [
                'borrower_id' => $record->borrower_id,
                'loan_type_id' => $record->loan_type_id,
                'loan_status' => $record->loan_status,
                'loan_release_date' => $record->loan_release_date,
                'principal_amount' => $record->principal_amount,
                'loan_purpose' => $record->loan_purpose,
                'interest_rate' => $record->interest_rate,
                'interest_amount' => $record->interest_amount,
                'processing_fee' => $record->processing_fee,
                'arrangement_fee' => $record->arrangement_fee,
                'insurance_fee' => $record->insurance_fee,
                'total_repayment' => $record->total_repayment,
                'case_number' => $record->case_number,
                'loan_duration' => $record->loan_duration,
                'duration_period' => $record->duration_period,
                'email' => $record->email,
                'crb_scoring' => $record->crb_scoring,
                'employer_verification' => $record->employer_verification,
                'due_diligence' => $record->due_diligence,
                'comments' => $record->comments,
                'credit_appraisal_report' => $record->credit_appraisal_report,
                'verified_by' => auth()->user()->id,
                'is_approved_on_step_one' => $record->is_approved_on_step_one,
                'is_approved_on_step_two' => $record->is_approved_on_step_two,
                'is_approved_on_step_three' => $record->is_approved_on_step_three,
                'is_approved_on_step_four' => $record->is_approved_on_step_four,
                'loan_number' => $record->loan_number,
                'physical_verification' => $record->physical_verification,
                'loan_agreement_file_path' => $record->loan_agreement_file_path
            ]
        );


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
