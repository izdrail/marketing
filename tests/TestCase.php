<?php

declare(strict_types=1);

namespace Tests;

use Cornatul\Marketing\Base\MarketingPortalBaseServiceProvider;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Cornatul\Marketing\Base\Services\Messages\RelayMessage;

abstract class TestCase extends BaseTestCase
{
    use MarketingTestSupportTrait;

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            MarketingPortalBaseServiceProvider::class,
            MarketingPortalTestServiceProvider::class,
        ];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMix();
        $this->withExceptionHandling();
        $this->mockRelayMessageService();

        $this->artisan('migrate')->run();
    }

    /**
     * @return void
     */
    protected function mockRelayMessageService(): void
    {
        $service = $this->getMockBuilder(RelayMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service->method('handle')->willReturn(Str::random());

        app()->instance(RelayMessage::class, $service);
    }
}
