<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Borrower extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'first_name',
    'last_name',
    'middle_name',
    'gender',
    'identification',
    'title',
    'dob',
    'mobile',
    'email',
    'address',
    'city',
    'province',
    'country',
    'marital_status',
    'zipcode',
    'occupation',
    'employer',
    'employee_number',
    'employer_number',
    'employer_address',
    'employee_start_date',
    'employer_email',
    'monthly_income',
    'bank_name',
    'bank_branch',
    'bank_sort_code',
    'bank_account_number',
    'bank_account_name',
    'case_number'
];

}
