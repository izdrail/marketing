<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Cornatul\Marketing\Base\Models\Subscriber;

class SubscriberAddedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Subscriber */
    public Subscriber $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }
}
