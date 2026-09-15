<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Models\Setting;
use App\Models\Service;
use Illuminate\Support\Facades\View; // Import the View facade
use Illuminate\Support\Facades\URL;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
           $setting = Setting::where('id', 1)->first();
        view::share('setting', $setting);
        View::share(\App\Http\Controllers\RegistrationController::viewData($setting));

        
      
    }
}