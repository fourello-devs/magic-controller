<?php

namespace FourelloDevs\MagicController;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/29
 *
 * Usage
 *  This is a simple Response Class that allows you to method-chain
 *  The creation of response as well as creating a unified response format
 *  The end of your chain must always end with the generate() function
 */
class ExtendedResponse
{
    protected $data = [];

    protected $code = 200;

    protected $success = TRUE;

    protected $message = '';

    protected $slug = '';

    protected $pagination = [];


    public function __construct($data = NULL, $message = NULL)
    {

        if (empty($data) === FALSE) {
            $this->data($data);
        }

        if (empty($message) === FALSE) {
            $this->message($message);
        }
    }

    public function code(int $code): ExtendedResponse
    {
        $this->code = $code;

        return $this;
    }

    // generic success code
    public function success($code = 200): ExtendedResponse
    {
        $this->code = $code;
        $this->success = TRUE;

        return $this;
    }

    // generic failure code
    public function failed($code = 400): ExtendedResponse
    {
        $this->code = $code;
        $this->success = FALSE;

        return $this;
    }

    // lacks authentication method
    // if auth middleware is not activated by default
    public function unauthorized(): ExtendedResponse
    {
        $this->code = 401;
        $this->success = FALSE;

        return $this;
    }

    // user permission specific errors
    public function forbidden(): ExtendedResponse
    {
        $this->code = 403;
        $this->success = FALSE;

        return $this;
    }

    // model search related errors
    public function notFound(): ExtendedResponse
    {
        $this->code = 404;
        $this->success = FALSE;

        return $this;
    }

    // set a custom slug
    public function slug(string $value): ExtendedResponse
    {
        $this->slug = $value;

        return $this;
    }

    public function message(string $value): ExtendedResponse
    {
        if ($this->slug === '') {
            // set slug too
            $this->slug = Str::slug($value, '_');
        }

        $this->message = $this->translateMessage($value);

        return $this;
    }

    // implement a message translator based on slug given
    protected function translateMessage($fallback)
    {

        return $fallback;
    }

    public function data($value): ExtendedResponse
    {

        if($value instanceof AnonymousResourceCollection){
            $value = $value->resource;
        }

        if ($value instanceof Paginator || $value instanceof LengthAwarePaginator) {
            // convert pagination to array
            $pagination = $value->toArray();
            $data = $pagination['data'];
            unset($pagination['data']);

            // separate them on two different array keys to create uniformity
            $this->pagination = $pagination;
            $this->data = $data;
        } else {
            $this->data = $value;
        }

        return $this;
    }

    public function generate(): JsonResponse
    {
        return $this->generateResponse();
    }

    protected function generateResponse(): JsonResponse
    {
        return response()->json([
            'success'     => $this->success,
            'code'        => $this->code,
            'slug'        => $this->slug,
            'message'     => $this->message,
            'data'        => $this->data,
            'pagination'  => $this->pagination,
        ], $this->code);
    }
}
