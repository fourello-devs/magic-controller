<?php

namespace FourelloDevs\MagicController\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/12/05
 *
 * Purpose
 *  This cast will allow you a hassle free way to declare passwords (encrypted with bcrypt)
 */
class BCryptable implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param string $key
     * @param  mixed  $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param string $key
     * @param  mixed  $value
     * @param array $attributes
     * @return string|null
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return NULL;
        }

        return bcrypt($value);
    }
}
