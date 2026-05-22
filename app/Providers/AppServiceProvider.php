<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\Job;
use App\Models\Package;
use App\Models\Pickuptask;
use App\Models\ReturnTask;
use App\Observers\JobObserver;
use App\Observers\PackageObserver;
use App\Observers\PickupTaskObserver;
use App\Observers\ReturnTaskObserver;

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
      URL::forceScheme('https');

      Job::observe(JobObserver::class);
      Pickuptask::observe(PickupTaskObserver::class);
      Package::observe(PackageObserver::class);
      ReturnTask::observe(ReturnTaskObserver::class);
    }
}
