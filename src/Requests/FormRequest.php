<?php
namespace FourelloDevs\MagicController\Requests;

use FourelloDevs\MagicController\Exceptions\UnauthorizedException;
use FourelloDevs\MagicController\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as BaseRequest;

/**
 * Class FormRequest
 * @package FourelloDevs\MagicController\Requests
 *
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @author James Carlo Luchavez <carlo.luchavez@fourello.com>
 */
class FormRequest extends BaseRequest
{
    /**
     * Override default failedAuthorization method.
     *
     * @throws UnauthorizedException
     */
    protected function failedAuthorization(): void
    {
        throw new UnauthorizedException();
    }

    /**
     * Override default failedValidation method.
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
