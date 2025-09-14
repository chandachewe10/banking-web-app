<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeadCreditEvaluation extends Model
{
    //


     public function loan_type()
    {

        return $this->belongsTo(LoanType::class, 'loan_type_id', 'id');
    }

    public function borrower()
    {

        return $this->belongsTo(Borrower::class, 'borrower_id', 'id');
    }


    public function verifiedBy()
    {

        return $this->belongsTo(User::class, 'verified_by', 'id');
    }


     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'borrower_id',
        'loan_type_id',
        'loan_status',
        'loan_release_date',
        'principal_amount',
        'loan_purpose',
        'interest_rate',
        'interest_amount',
        'processing_fee',
        'arrangement_fee',
        'insurance_fee',
        'total_repayment',
        'case_number',
        'loan_duration',
        'duration_period',
        'email',
        'crb_scoring',
        'employer_verification',
        'due_diligence',
        'comments',
        'credit_appraisal_report',
        'verified_by',
        'is_approved_on_step_one',
        'is_approved_on_step_two',
        'is_approved_on_step_three',
        'is_approved_on_step_four',
        'loan_number',
        'physical_verification',
        'loan_agreement_file_path'



    ];
}
