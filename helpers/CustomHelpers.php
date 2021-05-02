<?php

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/24
 */

use FourelloDevs\MagicController\ExtendedResponse;

if (! function_exists('customResponse')) {
    /**
     * @param array|null $data
     * @param string|null $message
     * @return FourelloDevs\MagicController\ExtendedResponse
     */
    function customResponse(?array $data = NULL, ?string $message = NULL): ExtendedResponse
    {
        return new ExtendedResponse($data, $message);
    }
}
