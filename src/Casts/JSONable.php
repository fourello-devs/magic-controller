<?php

namespace FourelloDevs\MagicController\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/30
 *
 * Usage
 *  This Cast will simply encode the value
 *      if the given value can be encoded
 *
 *  This will also return the decoded (array) value
 *      of the the value
 */
class JSONable implements CastsAttributes
{
    /**
     * @throws \JsonException
     */
    public function get($model, $key, $value, $attributes)
    {
        json_decode($value, null, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() === JSON_ERROR_NONE) {
            return json_decode($value, TRUE, 512, JSON_THROW_ON_ERROR);
        }

        return $value;
    }

    /**
     * @throws \JsonException
     */
    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
