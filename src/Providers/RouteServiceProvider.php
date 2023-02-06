<?php

namespace Cornatul\Marketing\Base\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Cornatul\Marketing\Base\Routes\ApiRoutes;
use Cornatul\Marketing\Base\Routes\WebRoutes;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::mixin(new ApiRoutes());
        Route::mixin(new WebRoutes());
    }
}
