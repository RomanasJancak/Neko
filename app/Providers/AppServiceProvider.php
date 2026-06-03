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
use Illuminate\Support\Facades\Vite;

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
      if (app()->environment('test')) {
        
        // 1. Force URLs to use the subfolder
        if (!app()->runningInConsole()) {
            URL::forceRootUrl(config('app.url'));
        }

        // 2. Tell Vite where the manifest file actually lives now
        Vite::useManifestFilename(base_path('../public_html/TEST/build/manifest.json'));
        
        // 3. Tell Vite where the hotfile lives (stops it from breaking during local development checks)
        Vite::useHotFile(base_path('../public_html/TEST/hot'));
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
