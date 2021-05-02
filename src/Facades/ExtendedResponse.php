<?php

namespace FourelloDevs\MagicController\Facades;

use Illuminate\Support\Facades\Facade;

class ExtendedResponse extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'extended-response';
    }
}
