<?php

namespace App\Providers;

use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
        Sanctum::ignoreMigrations();
        if ($this->app->environment('local') || $this->app->environment('staging') || $this->app->environment('acceptance') || $this->app->environment('production')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        /* assigning all permissions to super admin */
        Gate::after(function ($user, $ability) {
            return $user->hasRole('admin');
         });
         if(App::environment('production') || App::environment('staging') || App::environment('acceptance')){
             $this->app['request']->server->set('HTTPS', true);
             URL::forceScheme('https');
         }

         // Explicitly listening for Login/Logout events to bypass EventServiceProvider cache
         \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, [\App\Listeners\LogUserLogin::class, 'handle']);
         \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, [\App\Listeners\LogUserLogout::class, 'handle']);
    }
}
