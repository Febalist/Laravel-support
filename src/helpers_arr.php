<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

if (!function_exists('keystoupper')) {
    /** @return array */
    function keystoupper(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $key = strtoupper($key);
            $result[$key] = $value;
        }

        return $result;
    }
}

if (!function_exists('array_list')) {
    function array_list($array, $callback = null, $arguments = [])
    {
        $array = array_value($array);

        if ($callback) {
            $array = array_map_args($array, $callback, $arguments);
        }
        $array = array_filter($array);
        $array = array_unique($array);

        return array_values($array);
    }
}

if (!function_exists('array_map_args')) {
    function array_map_args(array $array, $callback, $arguments = [])
    {
        array_unshift($arguments, null);
        foreach ($array as &$element) {
            $arguments[0] = $element;
            $element = call_user_func_array($callback, $arguments);
        }

        return $array;
    }
}

if (!function_exists('array_avg')) {
    function array_avg(array $array)
    {
        return div(array_sum($array), count($array));
    }
}

if (!function_exists('array_flip_multiple')) {
    function array_flip_multiple(array $array)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[$value][] = $key;
        }

        return $result;
    }
}

if (!function_exists('array_wrap_flatten')) {
    function array_wrap_flatten($value)
    {
        return array_wrap(is_array($value) ? array_flatten($value) : $value);
    }
}

if (!function_exists('array_increment')) {
    function array_increment(array &$array, $key, $value = 1)
    {
        $value = array_get($array, $key, 0) + $value;
        array_set($array, $key, $value);

        return $value;
    }
}

if (!function_exists('to_array')) {
    function to_array($value)
    {
        return (array) json_parse(json_stringify($value));
    }
}

if (!function_exists('array_value')) {
    function array_value($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof Collection) {
            return $value->all();
        }

        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        throw new InvalidArgumentException("Value cannot be converted to array.");
    }
}

if (!function_exists('array_combine_values')) {
    function array_combine_values(array $array)
    {
        $array = array_values($array);
        $array = array_combine($array, $array);

        return $array;
    }
}

if (!function_exists('array_init')) {
    function array_init(array &$array, $key, $value = null)
    {
        if (array_get($array, $key) === null) {
            array_set($array, $key, value($value));
        }
    }
}

if (!function_exists('is_assoc')) {
    function is_assoc($array)
    {
        return Arr::isAssoc($array);
    }
}

if (!function_exists('array_without')) {
    /**
     * Get the array without the given values.
     *
     * @param array        $array
     * @param array|string $values
     *
     * @return array
     */
    function array_without(array $array, $values): array
    {
        $values = array_wrap($values);

        return array_values(array_diff($array, $values));
    }
}

if (!function_exists('array_pull_values')) {
    /**
     * Pull an array of values from a given array.
     *
     * Returns the found values that were removed from the source array.
     *
     * @param array $array
     * @param array $values
     *
     * @return array
     */
    function array_pull_values(array &$array, array $values): array
    {
        $matches = array_values(array_intersect($array, $values));

        $array = array_without($array, $values);

        return $matches;
    }
}

if (!function_exists('array_pull_value')) {
    /**
     * Pull a value from a given array.
     *
     * Returns the given value if it was successfully removed from the source array.
     *
     * @param array $array
     * @param mixed $value
     *
     * @return mixed
     */
    function array_pull_value(array &$array, $value)
    {
        $value = array_pull_values($array, [$value]);

        return array_shift($value);
    }
}
