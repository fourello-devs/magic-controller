<?php

namespace FourelloDevs\MagicController\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ExtendedResponse
 * @package FourelloDevs\MagicController\Facades
 *
 * @author James Carlo Luchavez <carlo.luchavez@fourello.com>
 * @since 2021-05-11
 */
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
