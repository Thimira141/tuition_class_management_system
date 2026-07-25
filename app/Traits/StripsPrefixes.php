<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait StripsPrefixes
{
    /**
     * Strip a given prefix from all keys in the array.
     *
     * @param  array   $data
     * @param  string  $prefix
     * @return array
     */
    public static function stripPrefix(array $data, string $prefix): array
    {
        return collect($data)->mapWithKeys(function ($value, $key) use ($prefix) {
            if (Str::startsWith($key, $prefix)) {
                return [Str::replaceFirst($prefix, '', $key) => $value];
            }
            return [$key => $value];
        })->toArray();
    }
}

