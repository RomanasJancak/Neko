<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Job;
use App\Models\Pickuptask;
use App\Observers\JobObserver;
use App\Observers\PickupTaskObserver;

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
      Job::observe(JobObserver::class);
      Pickuptask::observe(PickupTaskObserver::class);
    }
}
