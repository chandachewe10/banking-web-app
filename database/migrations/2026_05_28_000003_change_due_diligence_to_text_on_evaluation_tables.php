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
            if (! Schema::hasColumn($table, 'due_diligence')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->text('due_diligence')->nullable()->change();
            });

            DB::table($table)
                ->whereIn('due_diligence', ['0', '1', 0, 1])
                ->update(['due_diligence' => null]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'due_diligence')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->boolean('due_diligence')->default(false)->change();
            });
        }
    }
};
