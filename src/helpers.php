<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Litipk\BigNumbers\Decimal;

require 'helpers_arr.php';
require 'helpers_str.php';
require 'helpers_url.php';
require 'helpers_xml.php';

if (!function_exists('json_parse')) {
    function json_parse($data = null, $default = null, $asObject = false)
    {
        if (!$asObject && is_array($data)) {
            return $data;
        }

        return json_decode($data, !$asObject) ?? $default;
    }
}

if (!function_exists('json_stringify')) {
    function json_stringify($data, $pretty = false): string
    {
        $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR;
        if ($pretty) {
            $options |= JSON_PRETTY_PRINT;
        }

        return json_encode($data, $options);
    }
}

if (!function_exists('chance')) {
    function chance($percent = 50, $max = 100, $min = 1)
    {
        return random_int($min, $max) <= $percent;
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

if (!function_exists('div')) {
    function div($divisible, $divisor, $error = null)
    {
        return $divisor ? $divisible / $divisor : $error;
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
        $number = preg_replace('/\s/', '', $number);

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
        return preg_replace('/[\D]/', '', $string);
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
                if (($number - $number % 10) % 100 !== 10) {
                    if ($number % 10 === 1) {
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

if (!function_exists('html_string')) {
    function html_string($html): HtmlString
    {
        if ($html instanceof HtmlString) {
            return $html;
        }

        return new HtmlString($html);
    }
}

if (!function_exists('markdown')) {
    function markdown(
        $content,
        $text_mode = false,
        $disable_breaks = false,
        $ignore_links = false,
        $escape_html = false
    ): HtmlString {
        $markdown = Parsedown::instance()
            ->setBreaksEnabled(!$disable_breaks)
            ->setUrlsLinked(!$ignore_links)
            ->setMarkupEscaped($escape_html);
        $markdown = $text_mode ? $markdown->text($content) : $markdown->line($content);

        return html_string($markdown);
    }
}

if (!function_exists('lock')) {
    function lock($name, $seconds = 900, $owner = null): Lock
    {
        return Cache::lock($name, $seconds, $owner);
    }
}

if (!function_exists('sync')) {
    function sync($name, callable $callback, $seconds = 900, $timeout = 900, callable $timeoutCallback = null)
    {
        $lock = lock($name, $seconds);

        try {
            return $lock->block($timeout, $callback);
        } catch (LockTimeoutException $exception) {
            if ($timeoutCallback) {
                return $timeoutCallback($lock, $exception);
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

        for ($i = 0; $i < $argc; $i += 2) {
            if ($i === $argc - 1) {
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

        for ($i = 0; $i < $argc; $i += 2) {
            if ($i === $argc - 1) {
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

        for ($i = 0; $i < $argc; $i += 2) {
            if ($i === $argc - 1) {
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

if (!function_exists('route_signed')) {
    function route_signed($name, $parameters, $expiration = null, $absolute = true)
    {
        return URL::signedRoute($name,
            array_filter(array_wrap($parameters)),
            $expiration,
            $absolute);
    }
}

if (!function_exists('model_key')) {
    function model_key($model)
    {
        if ($model instanceof Model) {
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
    function decimal($value, $scale = null): Decimal
    {
        return Decimal::create($value, $scale);
    }
}

if (!function_exists('locale')) {
    function locale(): string
    {
        return config('app.locale') ?? config('app.fallback_locale');
    }
}

if (!function_exists('user')) {
    function user(): Authenticatable
    {
        return auth()->user();
    }
}

if (!function_exists('rand_bool')) {
    function random_bool(): bool
    {
        return (bool) random_int(0, 1);
    }
}
