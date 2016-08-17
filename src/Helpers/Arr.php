<?php

namespace Spatie\Regex\Helpers;

class Arr
{
    public static function map(array $array, $callback): array
    {
        return array_map($callback, $array);
    }

    public static function transpose(array $array): array
    {
        if (count($array) === 1) {
            $array = static::first($array);

            return array_map(function ($element) {
                return [$element];
            }, $array);
        }

        return array_map(null, ...$array);
    }

    public static function first(array $array)
    {
        return reset($array);
    }
}
