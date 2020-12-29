<?php

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
        while ($before !== $url) {
            $before = $url;
            $url = urldecode($url);
        }

        return $url;
    }
}

if (!function_exists('url_normalize')) {
    function url_normalize($url, $withoutFragment = false, $withoutQuery = false)
    {
        if (!Str::contains($url, '//')) {
            $url = '//'.$url;
        }
        $parts = parse_url($url);
        $host = $parts['host'] ?? null;
        if (!$host) {
            return;
        }
        if (extension_loaded('intl')) {
            $host = idn_to_utf8($host);
        }
        $host = mb_strtolower($host);
        $scheme = ($parts['scheme'] ?? 'http').'://';
        $scheme = mb_strtolower($scheme);
        $path = $parts['path'] ?? '/';
        $query = $withoutQuery ? null : ($parts['query'] ?? null);
        if ($query) {
            parse_str($query, $query);
            $query = '?'.http_build_query($query);
        }
        $fragment = $withoutFragment ? null : ($parts['fragment'] ?? null);
        if ($fragment) {
            $fragment = '#'.$fragment;
        }

        return implode('', [$scheme, $host, $path, $query, $fragment]);
    }
}

if (!function_exists('url_domain')) {
    function url_domain($url)
    {
        return parse_url($url, PHP_URL_HOST) ?? $url;
    }
}

if (!function_exists('build_url')) {
    function build_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'].'://' : '';
        $host = $parsed_url['host'] ?? '';
        $port = isset($parsed_url['port']) ? ':'.$parsed_url['port'] : '';
        $user = $parsed_url['user'] ?? '';
        $pass = isset($parsed_url['pass']) ? ':'.$parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = $parsed_url['path'] ?? '';
        $query = isset($parsed_url['query']) ? '?'.$parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#'.$parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}

if (!function_exists('query')) {
    function query($params, $url = null)
    {
        return url_query($url ?: request()->fullUrl(), $params);
    }
}

if (!function_exists('url_query')) {
    function url_query($url, $params)
    {
        $url = parse_url($url);
        parse_str($url['query'] ?? '', $query);
        $params = array_merge($query, array_value($params));
        if ($params) {
            $url['query'] = http_build_query($params);
        }

        return build_url($url);
    }
}
