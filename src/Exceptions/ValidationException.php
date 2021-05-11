<?php

namespace FourelloDevs\MagicController\Exceptions;

use Illuminate\Validation\ValidationException as BaseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ValidationException extends BaseException {

    public function render($request): JsonResponse
    {
        $errors = ($this->validator->errors())->toArray();
        $message = array_values($errors);
        $slug = Str::slug($message[0], '_');

        return customResponse()
            ->data([])
            ->slug($slug)
            ->message($message)
            ->failed(422)
            ->generate();
    }
}
