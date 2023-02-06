<?php

namespace Cornatul\Marketing\Base\Facades;

use Illuminate\Support\Facades\Facade;

class Helper extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'marketing.helper';
    }
}
