<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalSigningEvaluation extends Model
{
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class, 'borrower_id', 'id');
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
