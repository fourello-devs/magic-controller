<?php

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/24
 */

use FourelloDevs\MagicController\ExtendedResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

if (! function_exists('customResponse')) {
    /**
     * @param array|AnonymousResourceCollection|Paginator|LengthAwarePaginator|null $data
     * @param string|array|null $message
     * @return ExtendedResponse
     */
    function customResponse($data = NULL, $message = NULL): ExtendedResponse
    {
        return app('extended-response', ['data' => $data, 'message' => $message]);
    }
}
