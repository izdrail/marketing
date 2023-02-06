<?php

namespace Cornatul\Marketing\Base\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Cornatul\Marketing\Base\Events\MessageDispatchEvent;
use Cornatul\Marketing\Base\Events\SubscriberAddedEvent;
use Cornatul\Marketing\Base\Events\Webhooks\MailgunWebhookReceived;
use Cornatul\Marketing\Base\Events\Webhooks\MailjetWebhookReceived;
use Cornatul\Marketing\Base\Events\Webhooks\PostmarkWebhookReceived;
use Cornatul\Marketing\Base\Events\Webhooks\SendgridWebhookReceived;
use Cornatul\Marketing\Base\Events\Webhooks\SesWebhookReceived;
use Cornatul\Marketing\Base\Events\Webhooks\PostalWebhookReceived;
use Cornatul\Marketing\Base\Listeners\MessageDispatchHandler;
use Cornatul\Marketing\Base\Listeners\Webhooks\HandleMailgunWebhook;
use Cornatul\Marketing\Base\Listeners\Webhooks\HandleMailjetWebhook;
use Cornatul\Marketing\Base\Listeners\Webhooks\HandlePostmarkWebhook;
use Cornatul\Marketing\Base\Listeners\Webhooks\HandleSendgridWebhook;
use Cornatul\Marketing\Base\Listeners\Webhooks\HandleSesWebhook;
use Cornatul\Marketing\Base\Listeners\Webhooks\HandlePostalWebhook;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MailgunWebhookReceived::class => [
            HandleMailgunWebhook::class,
        ],
        MessageDispatchEvent::class => [
            MessageDispatchHandler::class,
        ],
        PostmarkWebhookReceived::class => [
            HandlePostmarkWebhook::class,
        ],
        SendgridWebhookReceived::class => [
            HandleSendgridWebhook::class,
        ],
        SesWebhookReceived::class => [
            HandleSesWebhook::class
        ],
        MailjetWebhookReceived::class => [
            HandleMailjetWebhook::class
        ],
        PostalWebhookReceived::class => [
            HandlePostalWebhook::class
        ],
        SubscriberAddedEvent::class => [
            // ...
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
