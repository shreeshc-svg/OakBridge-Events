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
        // if (config('app.env') === 'production') {
        //     URL::forceScheme('https');
        // }

        Schema::defaultStringLength(191);
        Paginator::defaultView('pagination::bootstrap-5');

        $capdata = Setting::where('id',1)->firstOrFail();

        $secret = $capdata->captch_secret_key;
        $site = $capdata->captcha_site_key;
        $bname = $capdata->bname;
        $contact_form = $capdata->captcha_contact_form;
        $login_form = $capdata->captcha_login_form;

        config(['nocaptcha.secret' => $secret]);
        config(['nocaptcha.sitekey' => $site]);
        config(['captcha.contact' => $contact_form]);
        config(['app.name' => $bname]);
        config(['login.form' => $login_form]);


        $footerServices = Service::where('featured', '1')->take(6)->get();
        View::share('footerServices', $footerServices);
    }
}
