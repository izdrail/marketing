<?php

namespace Cornatul\Marketing\Base\Services;

use Carbon\Carbon;

/**
 * Class Helper
 * @package Cornatul\Marketing\Base\Services
 */
class Helper
{

    /**
     * Display a given date in the active user's timezone.
     *
     * @param mixed $date
     * @param string $timezone
     * @return mixed
     */
    public function displayDate($date, string $timezone = null)
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->copy()->setTimezone($timezone);
    }

    public function isPro(): bool
    {
        return false;
    }
}
