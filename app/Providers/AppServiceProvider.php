<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;
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
      if (!app()->environment('local')) {
        URL::forceScheme('https');
      }

      Blade::directive('displayDate', function ($expression) {
        return "<?php echo app(\\App\\Services\\DateFormatService::class)->formatForUser($expression); ?>";
      });

      Blade::directive('displayInvoiceDate', function ($expression) {
        return "<?php echo app(\\App\\Services\\DateFormatService::class)->formatForInvoicePdf($expression); ?>";
      });

      Job::observe(JobObserver::class);
      Pickuptask::observe(PickupTaskObserver::class);
      Package::observe(PackageObserver::class);
      ReturnTask::observe(ReturnTaskObserver::class);
    }
}
