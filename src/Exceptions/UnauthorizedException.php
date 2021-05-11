<?php

namespace FourelloDevs\MagicController\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UnauthorizedException extends Exception
{
    public function render($request): JsonResponse
    {
        return customResponse()
            ->data([])
            ->message($this->getMessage() ?? 'You do not have the necessary permission to access this resource')
            ->failed(403)
            ->generate();
    }
}
