<?php

namespace FourelloDevs\MagicController\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Str;
use App\Models\ErrorMessage;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends Exception {

    protected $validator;

    /**
     * Create a new exception instance.
     *
     * @param Validator $validator
     * @param Response|null $response
     * @param string $errorBag
     * @param $message
     * @param $code
     * @param $previous
     */
    public function __construct($validator, ?Response $response, string $errorBag, $message, $code, $previous)
    {
        $this->validator = $validator;
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        $errors = ($this->validator->errors())->toArray();
        $key = array_key_first($errors);
        $message = $errors[$key][0];
        $slug = Str::slug($message, '_');

        return customResponse()
            ->data([])
            ->message($message)
            ->failed(422)
            ->generate();
    }
}
