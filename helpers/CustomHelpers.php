<?php

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/24
 */

if (! function_exists('customResponse')) {
    /**
     * @param array|null $data
     * @param string|null $message
     * @return ExtendedResponse
     */
    function customResponse(?array $data = NULL, ?string $message = NULL): ExtendedResponse
    {
        return new \FourelloDevs\MagicController\ExtendedResponse\ExtendedResponse($data, $message);
    }
}
