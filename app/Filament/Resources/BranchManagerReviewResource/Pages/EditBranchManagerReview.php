<?php

namespace App\Filament\Resources\BranchManagerReviewResource\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\BranchManagerReviewResource;
use App\Models\BranchManagerEvaluation;
use App\Models\Disbursements;
use App\Models\Loans;
use App\Models\LoanType;
use Filament\Notifications\Notification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Carbon\Carbon;

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
        /** @var BranchManagerEvaluation $record */
        $data['verified_by'] = auth()->user()->id;
        $record->update($data);
        $record->refresh();

        $loanNumber = $record->loan_number
            ?: sprintf('LN-%s-%s', $record->case_number ?? $record->id, $record->borrower_id);

        if (! $record->loan_number) {
            $record->update(['loan_number' => $loanNumber]);
        }

        $loanTypeId = LoanType::resolveId($record->loan_type_id, $record->loan_purpose);

        $loanAttributes = [
            'borrower_id'               => $record->borrower_id,
            'loan_type_id'              => $loanTypeId,
            'loan_status'               => $record->loan_status,
            'loan_release_date'         => $record->loan_release_date,
            'principal_amount'          => $record->principal_amount,
            'loan_purpose'              => $record->loan_purpose,
            'interest_rate'             => $record->interest_rate,
            'interest_amount'           => $record->interest_amount,
            'processing_fee'            => $record->processing_fee,
            'arrangement_fee'           => $record->arrangement_fee,
            'insurance_fee'             => $record->insurance_fee,
            'total_repayment'           => $record->total_repayment,
            'case_number'               => $record->case_number,
            'loan_number'               => $loanNumber,
            'loan_duration'             => $record->loan_duration,
            'duration_period'           => $record->duration_period,
            'email'                     => $record->email,
            'crb_scoring'               => $data['crb_scoring'] ?? $record->crb_scoring,
            'employer_verification'     => $data['employer_verification'] ?? $record->employer_verification,
            'due_diligence'             => $data['due_diligence'] ?? $record->due_diligence,
            'comments'                  => $data['comments'] ?? $record->comments,
            'credit_appraisal_report'   => $data['credit_appraisal_report'] ?? $record->credit_appraisal_report,
            'verified_by'               => auth()->id(),
            'is_approved_on_step_one'   => $record->is_approved_on_step_one,
            'is_approved_on_step_two'   => $record->is_approved_on_step_two,
            'is_approved_on_step_three' => $data['is_approved_on_step_three'] ?? $record->is_approved_on_step_three,
            'is_approved_on_step_four'  => $record->is_approved_on_step_four,
            'physical_verification'     => $record->physical_verification,
            'loan_agreement_file_path'  => $record->loan_agreement_file_path,
        ];

        $loan = Loans::updateOrCreate(
            ['loan_number' => $loanNumber],
            $loanAttributes,
        );

        Disbursements::create([
            'loan_id'           => $loan->id,
            'amount'            => $record->principal_amount,
            'method'            => 'cash',
            'reference_number'  => $record->transaction_reference ?? null,
            'disbursed_at'      => Carbon::now(),
            'authorized'        => 0,
            'authorized_by'     => null,
            'notes'             => $record->comments,
        ]);

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
