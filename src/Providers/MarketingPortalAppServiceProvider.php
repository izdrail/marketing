<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Cornatul\Marketing\Base\Interfaces\QuotaServiceInterface;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Cornatul\Marketing\Base\Repositories\Campaigns\MySqlCampaignTenantRepository;
use Cornatul\Marketing\Base\Repositories\Campaigns\PostgresCampaignTenantRepository;
use Cornatul\Marketing\Base\Repositories\Messages\MessageTenantRepositoryInterface;
use Cornatul\Marketing\Base\Repositories\Messages\MySqlMessageTenantRepository;
use Cornatul\Marketing\Base\Repositories\Messages\PostgresMessageTenantRepository;
use Cornatul\Marketing\Base\Repositories\Subscribers\MySqlSubscriberTenantRepository;
use Cornatul\Marketing\Base\Repositories\Subscribers\PostgresSubscriberTenantRepository;
use Cornatul\Marketing\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Cornatul\Marketing\Base\Services\Helper;
use Cornatul\Marketing\Base\Services\QuotaService;
use Cornatul\Marketing\Base\Traits\ResolvesDatabaseDriver;

class MarketingPortalAppServiceProvider extends ServiceProvider
{
    use ResolvesDatabaseDriver;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register():void
    {
        // Campaign repository.
        $this->app->bind(CampaignTenantRepositoryInterface::class, function (Application $app) {
            if ($this->usingPostgres()) {
                return $app->make(PostgresCampaignTenantRepository::class);
            }

            return $app->make(MySqlCampaignTenantRepository::class);
        });

        // Message repository.
        $this->app->bind(MessageTenantRepositoryInterface::class, function (Application $app) {
            if ($this->usingPostgres()) {
                return $app->make(PostgresMessageTenantRepository::class);
            }

            return $app->make(MySqlMessageTenantRepository::class);
        });

        // Subscriber repository.
        $this->app->bind(SubscriberTenantRepositoryInterface::class, function (Application $app) {
            if ($this->usingPostgres()) {
                return $app->make(PostgresSubscriberTenantRepository::class);
            }

            return $app->make(MySqlSubscriberTenantRepository::class);
        });

        $this->app->bind(QuotaServiceInterface::class, QuotaService::class);

        $this->app->singleton('marketing.helper', function () {
            return new Helper();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
    }
}
