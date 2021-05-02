<?php

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/24
 */

use FourelloDevs\MagicController\ExtendedResponse;
use Illuminate\Support\Facades\Log;

if (! function_exists('customResponse')) {
    function customResponse(?array $data = NULL, ?string $message = NULL): ExtendedResponse
    {
        Log::info('I was here so please work. :(', [class_exists('\FourelloDevs\MagicController\ExtendedResponse', true)]);
        return new ExtendedResponse($data, $message);
    }
}
