<?php

namespace FourelloDevs\MagicController\Facades;

use Illuminate\Support\Facades\Facade;

class MagicController extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'magic-controller';
    }
}
