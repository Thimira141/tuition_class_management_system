<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait PrefixKeys
{
    /**
     * Prefix all keys in an array/collection with a given string.
     * If the key already starts with the prefix, it will be left unchanged.
     *
     * @param  array|Collection  $data
     * @param  string  $prefix
     * @return Collection
     */
    public static function prefixKeys(array|Collection $data, string $prefix): Collection
    {
        $collection = collect($data);

        return $collection->mapWithKeys(function ($value, $key) use ($prefix) {
            // If key already starts with prefix__, skip re-applying
            if (str_starts_with($key, $prefix)) {
                return [$key => $value];
            }

            return [$prefix . '__' . $key => $value];
        });
    }
}
