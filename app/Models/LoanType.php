<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class LoanType extends Model
{

        use LogsActivity;

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
    }
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_name',
    ];

    /**
     * Ensure a valid loan_types.id exists (evaluations often reference id 1 from mobile API).
     */
    public static function resolveId(?int $loanTypeId = null, ?string $loanPurpose = null): int
    {
        if ($loanTypeId && static::query()->whereKey($loanTypeId)->exists()) {
            return $loanTypeId;
        }

        $name = $loanPurpose ? trim($loanPurpose) : 'General Loan';

        return (int) static::firstOrCreate(['loan_name' => $name])->id;
    }
}
