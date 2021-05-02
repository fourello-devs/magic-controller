<?php

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/24
 */

if (! function_exists('customResponse')) {
    function customResponse(?array $data = NULL, ?string $message = NULL): \FourelloDevs\MagicController\ExtendedResponse
    {
        return new \FourelloDevs\MagicController\ExtendedResponse($data, $message);
    }
}
