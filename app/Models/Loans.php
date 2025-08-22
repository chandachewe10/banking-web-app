<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{

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
        'purpose',
        'interest_rate',
        'loan_duration',
        'duration_period',
        'email',
    ];



}
