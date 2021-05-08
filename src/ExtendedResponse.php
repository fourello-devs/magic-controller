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
    /**
     * @var array|AnonymousResourceCollection|Paginator|LengthAwarePaginator|null
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $code = 200;

    /**
     * @var bool
     */
    protected $success = TRUE;

    /**
     * @var string|array|null
     */
    protected $message = '';

    /**
     * @var string
     */
    protected $slug = '';

    /**
     * @var array
     */
    protected $pagination = [];

    /**
     * ExtendedResponse constructor.
     * @param array|AnonymousResourceCollection|Paginator|LengthAwarePaginator|null $data
     * @param string|array|null $message
     */
    public function __construct($data = NULL, $message = NULL)
    {
        if (empty($data) === FALSE) {
            $this->data($data);
        }

        if (empty($message) === FALSE) {
            $this->message($message);
        }
    }

    /**
     * Set status code
     *
     * @param int $code
     * @return $this
     */
    public function code(int $code): ExtendedResponse
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Generic success code
     *
     * @param int $code
     * @return $this
     */
    public function success(int $code = 200): ExtendedResponse
    {
        $this->code = $code;
        $this->success = TRUE;

        return $this;
    }

    /**
     * Generic failure code
     *
     * @param int $code
     * @return $this
     */
    public function failed(int $code = 400): ExtendedResponse
    {
        $this->code = $code;
        $this->success = FALSE;

        return $this;
    }


    /**
     * Lacks authentication method
     * If auth middleware is not activated by default
     *
     * @return $this
     */
    public function unauthorized(): ExtendedResponse
    {
        $this->code = 401;
        $this->success = FALSE;

        return $this;
    }

    /**
     * User permission specific errors
     *
     * @return $this
     */
    public function forbidden(): ExtendedResponse
    {
        $this->code = 403;
        $this->success = FALSE;

        return $this;
    }

    /**
     * Model search related errors
     *
     * @return $this
     */
    public function notFound(): ExtendedResponse
    {
        $this->code = 404;
        $this->success = FALSE;

        return $this;
    }

    /**
     * Set a custom slug
     *
     * @param string $value
     * @return $this
     */
    public function slug(string $value): ExtendedResponse
    {
        $this->slug = $value;

        return $this;
    }

    /**
     * Set message
     *
     * @param string|array|null $value
     * @return $this
     */
    public function message($value): ExtendedResponse
    {
        if(is_array($value)) {
            $value = implode(' ', $value);
        }
        if (empty($this->slug)) {
            // set slug too
            $this->slug = Str::slug($value, '_');
        }

        $this->message = $this->translateMessage($value);

        return $this;
    }

    /**
     * Implement a message translator based on slug given
     *
     * @param $fallback
     * @return mixed
     */
    protected function translateMessage($fallback)
    {
        return $fallback;
    }

    /**
     * Set data
     *
     * @param $value
     * @return $this
     */
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

    /**
     * Generate response
     *
     * @return JsonResponse
     */
    public function generate(): JsonResponse
    {
        return $this->generateResponse();
    }

    /**
     * Generate response
     *
     * @return JsonResponse
     */
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
