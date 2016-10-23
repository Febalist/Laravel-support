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
    function list_cleanup($array, $callback = null, $arguments = [])
    {
        $array = (array)$array;
        if ($callback) {
            $array = array_map_args($array, $callback, $arguments);
        }
        $array = array_filter($array);
        $array = array_unique($array);
        return $array;
    }
}

if (!function_exists('array_map_args')) {
    function array_map_args(array $array, $callback, $arguments = [])
    {
        array_unshift($arguments, null);
        foreach ($array as &$element) {
            $arguments[0] = $element;
            $element      = call_user_func_array($callback, $arguments);
        }
        return $array;
    }
}

if (!function_exists('url_encode')) {
    function url_encode($url)
    {
        $url = url_decode($url);
        return urlencode($url);
    }
}

if (!function_exists('url_decode')) {
    function url_decode($url)
    {
        $before = null;
        while ($before != $url) {
            $before = $url;
            $url    = urldecode($url);
        }
        return $url;
    }
}

if (!function_exists('url_parse')) {
    function url_parse($url, $withoutFragment = false, $withoutQuery = false)
    {
        if (!str_contains($url, '//')) {
            $url = '//'.$url;
        }
        $parts = parse_url($url);
        $host  = array_get($parts, 'host');
        if (!$host) {
            return null;
        }
        if (extension_loaded('intl')) {
            $host = idn_to_utf8($host);
        }
        $scheme = array_get($parts, 'scheme', 'http').'://';
        $path   = array_get($parts, 'path', '/');
        $query  = $withoutQuery ? null : array_get($parts, 'query');
        if ($query) {
            parse_str($query, $query);
            $query = '?'.http_build_query($query);
        }
        $fragment = $withoutFragment ? null : array_get($parts, 'fragment');
        if ($fragment) {
            $fragment = '#'.$fragment;
        }
        return implode('', [$scheme, $host, $path, $query, $fragment]);
    }
}

if (!function_exists('url_domain')) {
    function url_domain($url)
    {
        if (str_contains($url, '/')) {
            $url = parse_url($url);
            return $url['host'];
        }
        return $url;
    }
}

if (!function_exists('faker')) {
    /**
     * @param             $field
     * @param mixed       $params
     * @param bool        $unique
     *
     * @return string
     */
    function faker($field, $params = [], $unique = false)
    {
        static $faker = null;
        if (is_null($faker)) {
            $locale = config('services.faker.locale', \Faker\Factory::DEFAULT_LOCALE);
            $faker  = Faker\Factory::create($locale);
        }
        if (!is_array($params)) {
            $params = [$params];
        }
        if ($unique) {
            $faker = $faker->unique();
        }
        return call_user_func_array([$faker, $field], $params);
    }
}

if (!function_exists('dj')) {
    function dj($data)
    {
        header('Content-Type: application/json');
        echo json_stringify($data, true);

        die(1);
    }
}
