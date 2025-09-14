<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Gate::policy(\App\Models\Borrower::class, \App\Policies\BorrowerPolicy::class);
        Gate::policy(\App\Models\Branches::class, \App\Policies\BranchesPolicy::class);
        Gate::policy(\App\Models\Disbursements::class, \App\Policies\DisbursementsPolicy::class);
        Gate::policy(\App\Models\LoanAgreementForms::class, \App\Policies\LoanAgreementFormsPolicy::class);
        Gate::policy(\App\Models\Loans::class, \App\Policies\LoansPolicy::class);
        Gate::policy(\App\Models\LoanType::class, \App\Policies\LoanTypePolicy::class);
        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
        Gate::policy(\Spatie\Activitylog\Models\Activity::class, \App\Policies\ActivityPolicy::class);


        Filament::registerNavigationGroups([
            'Credit Module',
            'Finance Module',
            'Branch Operations',

        ]);
    }
}
