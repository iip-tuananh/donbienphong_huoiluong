<?php

namespace App\Providers;
use App\Http\View\Composers\FooterComposer;
use App\Http\View\Composers\HeaderComposer;
use App\Http\View\Composers\MenuHomePageComposer;
use App\Model\Admin\Banner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema; //SoftDelete
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use DB;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') != 'local') {
            URL::forceScheme('https');
        }

        $config = \App\Model\Admin\Config::with(['image'])->where('id',1)->first();
        view()->share('config', $config);


        View::composer('*', function ($view) {
            $view->with('customer', Auth::guard('customer')->user());
        });


        View::composer(
            'site.partials.header',
            MenuHomePageComposer::class
        );


        View::composer(
            'site.partials.footer',
            FooterComposer::class
        );

        View::composer(
            'site.contact_us',
            FooterComposer::class
        );


        View::composer(
            'site.partials.header',
            HeaderComposer::class
        );

        View::composer(
            'site.layouts.master',
            HeaderComposer::class
        );
    }
}
