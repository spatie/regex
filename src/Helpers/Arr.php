<?php

namespace Spatie\Regex\Helpers;

class Arr
{
    public static function map(array $array, callable $callback): array
    {
        return array_map($callback, $array);
    }

    public static function transpose(array $array): array
    {
        if (count($array) === 1) {
            $array = static::first($array);

            return array_map(fn ($element) => [$element], $array);
        }

        $numHits = count($array[0]);
        $groups = array_keys($array);
        $result = [];
        for ($hit = 0; $hit < $numHits; $hit++) {
            $group = [];
            foreach ($groups as $groupName) {
                $group[$groupName] = $array[$groupName][$hit];
            }
            $result[] = $group;
        }

        return $result;
    }

    public static function first(array $array): mixed
    {
        return reset($array);
    }
}
