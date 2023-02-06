<?php

namespace Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Cornatul\Marketing\Base\Facades\MarketingPortal;

//@todo rename this to marketing provider test
class MarketingPortalTestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        MarketingPortal::setCurrentWorkspaceIdResolver(function () {
            return 1;
        });

        Route::group(['prefix' => 'marketing'], function () {
            MarketingPortal::webRoutes();
            MarketingPortal::publicWebRoutes();
            MarketingPortal::apiRoutes();
            MarketingPortal::publicApiRoutes();
        });
    }
}
