<?php

if (!function_exists('user')) {
    /** @return App\User */
    function user()
    {
        return \Auth::user();
    }
}

if (!function_exists('json_parse')) {
    /** @return array */
    function json_parse($data = null, $default = null, $asObject = false)
    {
        try {
            $data = json_decode($data, !$asObject);
        } catch (Exception $e) {
            $data = $default;
        }
        return $data;
    }
}
if (!function_exists('json_stringify')) {
    /** @return string */
    function json_stringify($data, $pretty = false)
    {
        $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        if ($pretty) {
            $options = $options | JSON_PRETTY_PRINT;
        }
        return json_encode($data, $options);
    }
}

if (!function_exists('json_stringify')) {
    /** @return array */
    function keystoupper(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $key          = strtoupper($key);
            $result[$key] = $value;
        }
        return $result;
    }
}

if (!function_exists('chance')) {
    function chance($percent = 50, $max = 100)
    {
        return mt_rand(1, $max) <= $percent;
    }
}

if (!function_exists('fixNewLines')) {
    function fixNewLines($string, $symbol = "\n")
    {
        return str_replace(["\r\n", "\r", "\n"], $symbol, $string);
    }
}

if (!function_exists('list_cleanup')) {
    function list_cleanup($array, $transform = null, $arguments = null)
    {
        $array = (array)$array;
        if ($transform) {
            $arguments = func_get_args();
            $arguments = array_slice($arguments, 1);
            foreach ($array as &$element) {
                $arguments[0] = $element;
                $element      = call_user_func_array($transform, $arguments);
            }
        }
        $array = array_filter($array);
        $array = array_unique($array);
        return $array;
    }
}
