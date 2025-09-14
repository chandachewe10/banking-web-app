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

 // Register policies based on Resource classes instead of Model classes
    Gate::policy(\App\Filament\Resources\PhysicalSigningEvaluationResource::class, \App\Policies\PhysicalSigningEvaluationPolicy::class);
    Gate::policy(\App\Filament\Resources\BranchManagerReviewResource::class, \App\Policies\BranchManagerReviewPolicy::class);
    Gate::policy(\App\Filament\Resources\HeadCreditReviewResource::class, \App\Policies\HeadCreditReviewPolicy::class);
    Gate::policy(\App\Filament\Resources\DisbursementsResource::class, \App\Policies\DisbursementsPolicy::class);
    Gate::policy(\Spatie\Activitylog\Models\Activity::class, \App\Policies\ActivityPolicy::class);
    // Keep the default policy for general Loans access

        Filament::registerNavigationGroups([
            'Credit Module',
            'Finance Module',
            'Branch Operations',

        ]);
    }
}
