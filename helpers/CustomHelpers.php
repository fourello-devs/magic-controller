<?php

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/24
 */

if (! function_exists('customResponse')) {
    /**
     * @param array|null $data
     * @param string|null $message
     * @return \FourelloDevs\MagicController\ExtendedResponse
     */
    function customResponse(?array $data = NULL, ?string $message = NULL): \FourelloDevs\MagicController\ExtendedResponse
    {
        \Illuminate\Support\Facades\Log::info('I');
        return new \FourelloDevs\MagicController\ExtendedResponse($data, $message);
    }
}
