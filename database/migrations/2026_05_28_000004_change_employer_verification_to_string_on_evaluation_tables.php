<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'credit_evaluations',
        'head_credit_evaluations',
        'branch_manager_evaluations',
        'physical_signing_evaluations',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'employer_verification')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->string('employer_verification', 50)->nullable()->change();
            });

            DB::table($table)
                ->whereIn('employer_verification', ['0', '1', 0, 1])
                ->update(['employer_verification' => null]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'employer_verification')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->boolean('employer_verification')->default(false)->change();
            });
        }
    }
};
