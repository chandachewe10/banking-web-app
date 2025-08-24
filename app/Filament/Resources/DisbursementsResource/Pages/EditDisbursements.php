<?php

namespace App\Filament\Resources\DisbursementsResource\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\DisbursementsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Carbon\Carbon;
use App\Notifications\LoanStatusNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        $data['verified_by'] = auth()->user()->id;
        $data['loan_status'] = $data['is_approved_on_step_four'] == 1 ? 'approved' : 'denied';
        $data['loan_number'] = IdGenerator::generate(['table' => 'loans', 'length' => 8, 'prefix' => date('Y')]);
        $record->update($data);

        //Check if they have the Loan Agreement Form template for this type of loan
        $loan_agreement_text = \App\Models\LoanAgreementForms::where('loan_type_id', "=", 1)->first();
        if (!$loan_agreement_text) {
            Notification::make()
                ->warning()
                ->title('Invalid Agreement Form!')
                ->body('Please create a template first if you want to compile the Loan Agreement Form')
                ->persistent()
                ->actions([
                    Action::make('create')
                        ->button()
                        ->url(route('filament.admin.resources.loan-agreement-forms.create'), shouldOpenInNewTab: true),
                ])
                ->send();

            $this->halt();
        } else {

            $loanDetails = \App\Models\Loans::where('loan_number',"=",$data['loan_number'])->first();
            
            $loan_duration = $loanDetails->loan_duration;
            $loan_release_date = $loanDetails->loan_release_date;
            $loan_date = $loan_release_date;
            $loanDate = Carbon::parse($loan_date);

            $loan_due_date = $loanDate->addMonths((int)$loan_duration);


            $borrower = \App\Models\Borrower::findOrFail($loanDetails->borrower_id);
            $loan_type = \App\Models\LoanType::findOrFail(1);

            $company_name = auth()->user()->name;
            $borrower_name = $borrower->first_name . ' ' . $borrower->last_name;
            $borrower_email = $borrower->email ?? '';
            $borrower_phone = $borrower->mobile ?? '';
            $loan_name = $loan_type->loan_name;
            $loan_interest_rate = $loanDetails->interest_rate;
            $loan_amount = $loanDetails->principal_amount;
            $loan_duration = $loanDetails->loan_duration;
            $loan_release_date = $loanDetails->loan_release_date;
            $loan_repayment_amount = $loanDetails->repayment_amount;
            $loan_interest_amount = $loanDetails->interest_amount;
            $loan_due_date = $loan_due_date;
            $loan_number = $data['loan_number'];
            // The original content with placeholders
            $template_content = $loan_agreement_text->loan_agreement_text;


            // Replace placeholders with actual data
            $template_content = str_replace('[Company Name]', $company_name, $template_content);
            $template_content = str_replace('[Borrower Name]', $borrower_name, $template_content);
            $template_content = str_replace('[Loan Tenure]', $loan_duration, $template_content);
            $template_content = str_replace('[Loan Interest Percentage]', $loan_interest_rate, $template_content);
            $template_content = str_replace('[Loan Interest Fee]', $loan_interest_amount, $template_content);
            $template_content = str_replace('[Loan Amount]', $loan_amount, $template_content);
            $template_content = str_replace('[Loan Repayments Amount]', $loan_repayment_amount, $template_content);
            $template_content = str_replace('[Loan Due Date]', $loan_due_date, $template_content);
            $template_content = str_replace('[Borrower Email]', $borrower_email, $template_content);
            $template_content = str_replace('[Borrower Phone]', $borrower_phone, $template_content);
            $template_content = str_replace('[Loan Name]', $loan_name, $template_content);
            $template_content = str_replace('[Loan Number]', $loan_number, $template_content);

            $characters_to_remove = ['<br>', '&nbsp;'];
            $template_content = str_replace($characters_to_remove, '', $template_content);
            // Create a new PhpWord instance
            $phpWord = new PhpWord();

            // dd($template_content);
            // Add content to the document (agenda, summary, key points, sentiments)
            $section = $phpWord->addSection();

            // \PhpOffice\PhpWord\Shared\Html::addHtml($section, $template_content);
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $template_content, false, false);


            $current_year = date('Y');
            $path = public_path('LOAN_CONTRACT_FORMS/' . $current_year . '/DOCX');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $file_name = Str::random(40) . '.docx';

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($path . '/' . $file_name);
            $data['loan_agreement_file_path'] = 'LOAN_CONTRACT_FORMS/' . $current_year . '/DOCX' . '/' . $file_name;





            // Send SMS via BulkSMS


            // Define the JSON data
            $loan_cycle = 'months';
            $message = 'Hi ' . $borrower->first_name . ', ';
            $loan_amount = $loanDetails->principal_amount;
            $loan_duration = $loanDetails->loan_duration;
            $loan_release_date = $loanDetails->loan_release_date;
            $loan_repayment_amount = $loanDetails->total_repayment;
            $loan_interest_amount = $loanDetails->interest_amount;
            $loan_due_date = $loanDetails->loan_due_date ?? '';
            $loan_number = $loanDetails->loan_number ?? '';

            // Assuming $loanDetails->loan_status contains the current status
            $loanStatus = $loanDetails->loan_status;

            switch ($loanStatus) {
                case 'approved':
                    $message .= 'Congratulations! Your loan application of K' . $loan_amount . ' has been approved successfully. The total repayment amount is K' . $loan_repayment_amount . ' to be repaid in ' . $loan_duration . ' ' . $loan_cycle;
                    break;

                case 'processing':
                    $message .= 'Your loan application is currently under review. We will notify you once the review process is complete.';
                    break;

                case 'denied':
                    $message .= 'We regret to inform you that your loan application has been rejected.';
                    break;

                case 'defaulted':
                    $message .= 'Unfortunately, your loan is in default status. Please contact us as soon as possible to discuss the situation.';
                    break;

                default:
                    $message .= 'Your loan application is in progress. Current status: ' . $loanStatus;
                    break;
            }





            $this->sendSms($message,$borrower_phone);


            $message = 'Hi ' . $borrower->first_name . ', ';
            $loan_amount = $loanDetails->principal_amount;
            $loan_duration = $loanDetails->loan_duration;
            $loan_release_date = $loanDetails->loan_release_date;
            $loan_repayment_amount = $loanDetails->total_repayment;
            $loan_interest_amount = $loanDetails->interest_amount;
            $loan_number = $loanDetails->loan_number;

            // Assuming $loanDetails->loan_status contains the current status
            $loanStatus = $loanDetails->loan_status;

            switch ($loanStatus) {
                case 'approved':
                    $message .= 'Congratulations! Your loan application of K' . $loan_amount . ' has been approved successfully. The total repayment amount is K' . $loan_repayment_amount . ' to be repaid in ' . $loan_duration . ' ' . $loan_cycle;
                    break;

                case 'processing':
                    $message .= 'Your loan application of K' . $loan_amount . ' is currently under review. We will notify you once the review process is complete.';
                    break;

                case 'denied':
                    $message .= 'We regret to inform you that your loan application of K' . $loan_amount . ' has been rejected.';
                    break;

                case 'defaulted':
                    $message .= 'Unfortunately, your loan is in default status. Please contact us as soon as possible to discuss the situation.';
                    break;



                default:
                    $message .= 'Your loan application of K' . $loan_amount . ' is in progress. Current status: ' . $loanStatus;
                    break;
            }

           // $borrower->notify(new LoanStatusNotification($message));




           $record->update($data);

            return $record;
        }
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


    public function sendSms($message, $phoneNumber)
    {


        $base_uri = config('services.swiftsms.baseUri');
        $endpoint = config('services.swiftsms.endpoint');
        $senderId = config('services.swiftsms.senderId');
        $token = config('services.swiftsms.token');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get($base_uri . $endpoint, [
            'sender_id' => $senderId,
            'numbers' => $phoneNumber,
            'message' => $message,
        ]);

        if ($response->successful()) {
            return true;
        }

        Log::error('Swift SMS send failed', ['response' => $response->body()]);
        return false;
    }
}
