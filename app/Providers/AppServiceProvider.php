<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->routes(function () {
            // API route'ları
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Web route'ları
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
