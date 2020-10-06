<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Cache\LockTimeoutException;
use SebastiaanLuca\PhpHelpers\Classes\MethodHelper;
use SebastiaanLuca\PhpHelpers\InternalHelpers;

require 'xml.php';

if (!function_exists('json_parse')) {
    /** @return array */
    function json_parse($data = null, $default = null, $asObject = false)
    {
        if (!$asObject && is_array($data)) {
            return $data;
        }

        try {
            return json_decode($data, !$asObject);
        } catch (Exception $e) {
            return $default;
        }
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

if (!function_exists('chance')) {
    function chance($percent = 50, $max = 100, $min = 1)
    {
        return mt_rand($min, $max) <= $percent;
    }
}

if (!function_exists('replace_newlines')) {
    function replace_newlines($string, $symbol = "\n")
    {
        return str_replace(["\r\n", "\r", "\n"], $symbol, $string);
    }
}

if (!function_exists('str_between')) {
    function str_between($subject, $after, $before, $reverse = false, $after_last = false, $before_last = false)
    {
        if ($reverse) {
            if ($before_last) {
                $subject = str_before_last($subject, $before);
            } else {
                $subject = str_before($subject, $before);
            }
            if ($after_last) {
                $subject = str_after_last($subject, $after);
            } else {
                $subject = str_after($subject, $after);
            }
        } else {
            if ($after_last) {
                $subject = str_after_last($subject, $after);
            } else {
                $subject = str_after($subject, $after);
            }
            if ($before_last) {
                $subject = str_before_last($subject, $before);
            } else {
                $subject = str_before($subject, $before);
            }
        }

        return $subject;
    }
}

if (!function_exists('str_between_last')) {
    function str_between_last($subject, $after, $before)
    {
        return str_between($subject, $after, $before, true, true, true);
    }
}

if (!function_exists('str_between_greedy')) {
    function str_between_greedy($subject, $after, $before)
    {
        return str_between($subject, $after, $before, false, false, true);
    }
}

if (!function_exists('str_after_last')) {
    function str_after_last($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        return last(explode($search, $subject));
    }
}

if (!function_exists('str_before_last')) {
    function str_before_last($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        return implode($search, array_slice(explode($search, $subject), 0, -1));
    }
}

if (!function_exists('mb_strrev')) {
    function mb_strrev($string)
    {
        preg_match_all('/./us', $string, $matches);

        return join('', array_reverse($matches[0]));
    }
}

if (!function_exists('str_uuid')) {
    function str_uuid($ordered = false)
    {
        $uuid = $ordered ? Illuminate\Support\Str::orderedUuid() : Illuminate\Support\Str::uuid();

        return (string) $uuid;
    }
}

if (!function_exists('list_cleanup')) {
    function list_cleanup($array, $callback = null, $arguments = [])
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
            $url = urldecode($url);
        }

        return $url;
    }
}

if (!function_exists('url_parse')) {
    function url_normalize($url, $withoutFragment = false, $withoutQuery = false)
    {
        if (!str_contains($url, '//')) {
            $url = '//'.$url;
        }
        $parts = parse_url($url);
        $host = array_get($parts, 'host');
        if (!$host) {
            return;
        }
        if (extension_loaded('intl')) {
            $host = idn_to_utf8($host);
        }
        $scheme = array_get($parts, 'scheme', 'http').'://';
        $path = array_get($parts, 'path', '/');
        $query = $withoutQuery ? null : array_get($parts, 'query');
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

            return $url['host'] ?? '';
        }

        return $url;
    }
}

if (!function_exists('build_url')) {
    function build_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'].'://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':'.$parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':'.$parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
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

if (!function_exists('dj')) {
    function dj($data)
    {
        header('Content-Type: application/json');
        echo json_stringify($data, true);

        die(1);
    }
}

if (!function_exists('css')) {
    function css($name)
    {
        return asset_mix("css/$name.css");
    }
}

if (!function_exists('js')) {
    function js($name)
    {
        return asset_mix("js/$name.js");
    }
}

if (!function_exists('asset_mix')) {
    function asset_mix($file)
    {
        try {
            return url(mix($file));
        } catch (Exception $exception) {
            $messages = [
                'The Mix manifest does not exist',
                'Unable to locate Mix file',
            ];
            if (starts_with($exception->getMessage(), $messages)) {
                return asset($file);
            }

            throw $exception;
        }
    }
}

if (!function_exists('img')) {
    function img($file)
    {
        return asset("img/$file");
    }
}

if (!function_exists('array_avg')) {
    function array_avg(array $array)
    {
        $sum = array_sum($array);
        $count = count($array);

        return div($sum, $count);
    }
}

if (!function_exists('div')) {
    function div($divisible, $divisor, $number = false)
    {
        $null = $number ? 0 : null;

        return $divisor ? $divisible / $divisor : $null;
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

if (!function_exists('is_assoc')) {
    function is_assoc($array)
    {
        $array = array_value($array);

        if ([] === $array) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}

if (!function_exists('microsleep')) {
    function microsleep($micro_seconds)
    {
        usleep($micro_seconds * 1000000);
    }
}

if (!function_exists('filename_normalize')) {
    function filename_normalize($name, $spaces = ' ')
    {
        $name = mb_ereg_replace('\s+', $spaces, $name);
        $name = mb_ereg_replace('[\\\\\/?%*:|\"<>]', '', $name);
        $name = mb_ereg_replace('\.+', '.', trim($name));
        $name = mb_ereg_replace('\.$', '', trim($name));

        return trim($name);
    }
}

if (!function_exists('float')) {
    function float($number)
    {
        $number = str_replace(',', '.', $number);

        //$number = preg_replace('/\s+/', '', $number);
        return (float) $number;
    }
}

if (!function_exists('checkbox')) {
    function checkbox($value, $default = false)
    {
        if (is_bool($value)) {
            return $value;
        }
        if (in_array($value, ['true', 'on', 'yes', '1', 1], true)) {
            return true;
        }
        if (in_array($value, ['false', 'off', 'no', '0', 0], true)) {
            return false;
        }

        return $default;
    }
}

if (!function_exists('digits')) {
    function digits($string)
    {
        return preg_replace('/[^\d]/', '', $string);
    }
}

if (!function_exists('email_normalize')) {
    function email_normalize($address)
    {
        if (!str_contains($address, '@')) {
            return;
        }

        $address = implode('@', [
            str_between($address, '<', '@', true, true, false),
            str_between($address, '@', '>', false, false, false),
        ]);

        $address = trim($address);

        if (mb_strlen($address) < 7) {
            return null;
        }

        $address = mb_strtolower($address);

        return $address;
    }
}

if (!function_exists('paginate')) {
    function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Illuminate\Support\Collection ? $items : collect($items);

        return new Illuminate\Pagination\LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}

if (!function_exists('number')) {
    function number($number, $decimals = 0, $units = null, $separator = null, $plus = false)
    {
        $negative = $number < 0;
        $number = abs($number);
        $separator = $separator ?? uchr(160);
        $result = number_format($number, $decimals, ',', $separator);

        if ($units) {
            if (str_contains($units, '|')) {
                $units = explode('|', $units);
                if (($number - $number % 10) % 100 != 10) {
                    if ($number % 10 == 1) {
                        $units = $units[0].$units[2];
                    } elseif ($number % 10 >= 2 && $number % 10 <= 4) {
                        $units = $units[0].$units[3];
                    } else {
                        $units = $units[0].$units[1];
                    }
                } else {
                    $units = $units[0].$units[1];
                }
            }
            $result .= $separator.$units;
        }

        if ($negative) {
            $result = '−'.$separator.$result;
        } elseif ($plus) {
            $result = '+'.$separator.$result;
        }

        return $result;
    }
}

if (!function_exists('name_initials')) {
    function name_initials($fullname, $separator = null, $short = false)
    {
        $fullname = trim(whitespaces($fullname));
        $separator = $separator ?? uchr(160);
        $parts = explode(' ', $fullname);
        $result = [];
        $result[] = array_shift($parts);
        foreach ($parts as $part) {
            $result[] = mb_substr($part, 0, 1).'.'.($short ? '' : ' ');
        }
        $result = implode(' ', $result);
        $result = trim(whitespaces($result));
        $result = str_replace(' ', $separator, $result);

        return $result;
    }
}

if (!function_exists('markdown')) {
    function markdown(
        $content,
        $text_mode = false,
        $disable_breaks = false,
        $ignore_links = false,
        $escape_html = false
    ) {
        $markdown = Parsedown::instance()
            ->setBreaksEnabled(!$disable_breaks)
            ->setUrlsLinked(!$ignore_links)
            ->setMarkupEscaped($escape_html);
        $markdown = $text_mode ? $markdown->text($content) : $markdown->line($content);

        return new Illuminate\Support\HtmlString($markdown);
    }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));

        return $fc.mb_substr($str, 1);
    }
}

if (!function_exists('str_limit_hard')) {
    function str_limit_hard($value, $limit = 100, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        $limit -= mb_strwidth($end, 'UTF-8');
        if ($limit < 0) {
            return '';
        }

        return str_limit($value, $limit, $end);
    }
}

if (!function_exists('whitespaces')) {
    function whitespaces($string, $newlines = false, $multilines = false)
    {
        if ($newlines) {
            $string = replace_newlines($string);
            $string = preg_replace('/[^\S\n]+/', ' ', $string);
            if (!$multilines) {
                $string = preg_replace('/\n+/', "\n", $string);
            }
        } else {
            $string = preg_replace('/\s+/', ' ', $string);
        }

        return $string;
    }
}

if (!function_exists('multiexplode')) {
    function multiexplode($delimiters, $string)
    {
        if (is_string($delimiters)) {
            $delimiters = str_split($delimiters);
        }
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);

        return $launch;
    }
}

if (!function_exists('uchr')) {
    function uchr($codes)
    {
        if (is_scalar($codes)) {
            $codes = func_get_args();
        }

        $str = '';
        foreach ($codes as $code) {
            $str .= html_entity_decode('&#'.$code.';', ENT_NOQUOTES, 'UTF-8');
        }

        return $str;
    }
}

if (!function_exists('uord')) {
    function uord($symbol)
    {
        $k = mb_convert_encoding($symbol, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));

        return $k2 * 256 + $k1;
    }
}

if (!function_exists('string2binary')) {
    function string2binary($string)
    {
        $chars = str_split($string);
        foreach ($chars as &$char) {
            $char = decbin(ord($char));
            $char = str_pad($char, 8, 0, STR_PAD_LEFT);
        }

        return implode('', $chars);
    }
}

if (!function_exists('binary2string')) {
    function binary2string($binary)
    {
        $chars = str_split($binary, 8);
        foreach ($chars as &$char) {
            $char = chr(bindec($char));
        }

        return implode('', $chars);
    }
}

if (!function_exists('lock')) {
    /**
     * Get a lock instance.
     * @see https://laravel.com/docs/cache#atomic-locks
     *
     * @param string $name
     * @param int    $seconds
     * @return Illuminate\Contracts\Cache\Lock
     */
    function lock($name, $seconds = 900, $owner = null)
    {
        return Cache::lock($name, $seconds, $owner);
    }
}

if (!function_exists('sync')) {
    function sync($name, callable $callback, $seconds = 900, $timeout = 900, callable $timeoutCallback = null)
    {
        $lock = lock($name, $seconds);

        try {
            $lock->block($timeout, $callback);
        } catch (LockTimeoutException $exception) {
            if ($timeoutCallback) {
                $timeoutCallback();
            } else {
                throw $exception;
            }
        }
    }
}

if (!function_exists('if_then')) {
    function if_then(...$args)
    {
        $argc = func_num_args();

        for ($i = 0; $i < $argc; $i = $i + 2) {
            if ($i == $argc - 1) {
                return $args[$i];
            }
            if ($args[$i]) {
                return $args[$i + 1];
            }
        }

        return null;
    }
}

if (!function_exists('switch_case')) {
    function switch_case($value, ...$args)
    {
        $argc = func_num_args() - 1;

        for ($i = 0; $i < $argc; $i = $i + 2) {
            if ($i == $argc - 1) {
                return $args[$i];
            }
            if ($args[$i] == $value) {
                return $args[$i + 1];
            }
        }

        return null;
    }
}

if (!function_exists('switch_case_strict')) {
    function switch_case_strict($value, ...$args)
    {
        $argc = func_num_args() - 1;

        for ($i = 0; $i < $argc; $i = $i + 2) {
            if ($i == $argc - 1) {
                return $args[$i];
            }
            if ($args[$i] === $value) {
                return $args[$i + 1];
            }
        }

        return null;
    }
}

if (!function_exists('try_continue')) {
    function try_continue($callback, ...$arguments)
    {
        try {
            return $callback(...$arguments);
        } catch (\Exception $exception) {
            report($exception);
        }
    }
}

if (!function_exists('number_compare')) {
    function number_compare($number, $mt, $eq, $lt, $relative = 0)
    {
        if ($number > $relative) {
            return value($mt);
        } elseif ($number < $relative) {
            return value($lt);
        } else {
            return value($eq);
        }
    }
}

if (!function_exists('escape_like')) {
    function escape_like($string)
    {
        return str_replace(['%', '_'], ['\%', '\_'], $string);
    }
}

if (!function_exists('like_starts')) {
    function like_starts($string)
    {
        return escape_like($string).'%';
    }
}

if (!function_exists('like_ends')) {
    function like_ends($string)
    {
        return '%'.escape_like($string);
    }
}

if (!function_exists('like_contains')) {
    function like_contains($string)
    {
        return '%'.escape_like($string).'%';
    }
}

if (!function_exists('to_array')) {
    function to_array($value)
    {
        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->toArray();
        }

        return json_parse(json_stringify($value), []);
    }
}

if (!function_exists('array_value')) {
    function array_value($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->all();
        }

        return (array) $value;
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

        return array_get($array, $key);
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

if (!function_exists('array_wrap_flatten')) {
    function array_wrap_flatten($value)
    {
        return array_wrap(is_array($value) ? array_flatten($value) : $value);
    }
}

if (!function_exists('route_signed')) {
    function route_signed($name, $parameters, $expiration = null, $absolute = true)
    {
        return URL::signedRoute($name, array_filter(array_wrap($parameters)), $expiration, $absolute);
    }
}

if (!function_exists('model_key')) {
    function model_key($model)
    {
        if ($model instanceof Illuminate\Database\Eloquent\Model) {
            return $model->getKey();
        }

        return $model;
    }
}

if (!function_exists('serialize_hash')) {
    function serialize_hash(...$arguments): string
    {
        return sha1(serialize($arguments));
    }
}

if (!function_exists('decimal')) {
    /** @return \Litipk\BigNumbers\Decimal */
    function decimal($value, $scale = null)
    {
        return Litipk\BigNumbers\Decimal::create($value, $scale);
    }
}

if (!function_exists('locale')) {
    /**
     * Get the active locale.
     *
     * @return string
     */
    function locale(): string
    {
        return config('app.locale') ?? config('app.fallback_locale');
    }
}

if (!function_exists('is_guest')) {
    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    function is_guest(): bool
    {
        return auth()->guest();
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    function is_logged_in(): bool
    {
        return auth()->check();
    }
}

if (!function_exists('user')) {
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|\App\User|null
     */
    function user()
    {
        return auth()->user();
    }
}

if (!function_exists('rand_bool')) {
    /**
     * Randomly return true or false.
     *
     * @return bool
     */
    function rand_bool(): bool
    {
        return random_int(0, 1) === 0;
    }
}

if (!function_exists('str_wrap')) {
    /**
     * Wrap a string with another string.
     *
     * @param string $string
     * @param string $wrapper
     *
     * @return string
     */
    function str_wrap($string, $wrapper): string
    {
        return $wrapper.$string.$wrapper;
    }
}

if (!function_exists('is_assoc_array')) {
    /**
     * Check if an array is associative.
     *
     * Performs a simple check to determine if the given array's keys are numeric, start at 0,
     * and count up to the amount of values it has.
     *
     * @param array $array
     *
     * @return bool
     */
    function is_assoc_array($array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}

if (!function_exists('array_expand')) {
    /**
     * Expand a flat dotted array to a multi-dimensional associative array.
     *
     * If a key is encountered that is already present and the existing value is an array, each
     * new value will be added to that array. If it's not an array, each new value will override
     * the existing one.
     *
     * @param array $array
     *
     * @return array
     */
    function array_expand(array $array): array
    {
        $expanded = [];

        foreach ($array as $key => $value) {
            InternalHelpers::arraySet($expanded, $key, $value);
        }

        return $expanded;
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
        $values = !is_array($values) ? [$values] : $values;

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

if (!function_exists('array_hash')) {
    /**
     * Create a unique identifier for a given array.
     *
     * @param array $array
     *
     * @return string
     */
    function array_hash(array $array): string
    {
        return md5(serialize($array));
    }
}

if (!function_exists('object_hash')) {
    /**
     * Create a unique identifier for a given object.
     *
     * @param $object
     *
     * @return string
     */
    function object_hash($object): string
    {
        return md5(serialize($object));
    }
}

if (!function_exists('has_public_method')) {
    /**
     * Check if a class has a certain public method.
     *
     * @param object $object
     * @param string $method
     *
     * @return bool
     */
    function has_public_method($object, $method): bool
    {
        return MethodHelper::hasPublicMethod($object, $method);
    }
}
