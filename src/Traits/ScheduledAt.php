<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Traits;

use Carbon\Carbon;

trait ScheduledAt
{
    protected function calculateNextScheduledAt(Carbon $timestamp, int $delayInSeconds): Carbon
    {
        return Carbon::parse($timestamp)->addSeconds($delayInSeconds);
    }
}
