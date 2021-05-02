<?php

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/24
 */

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

if (! function_exists('customResponse')) {
    /**
     * @param array|AnonymousResourceCollection|Paginator|LengthAwarePaginator|null $data
     * @param string|array|null $message
     * @return Application|mixed
     */
    function customResponse($data = NULL, $message = NULL)
    {
        Log::info('before app make', [$data, $message]);
        return app('extended-response', ['data' => $data, 'message' => $message]);
    }
}
