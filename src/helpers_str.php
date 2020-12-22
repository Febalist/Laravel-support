<?php

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

        return implode('', array_reverse($matches[0]));
    }
}

if (!function_exists('str_uuid')) {
    function str_uuid($ordered = false)
    {
        $uuid = $ordered ? Illuminate\Support\Str::orderedUuid() : Illuminate\Support\Str::uuid();

        return (string) $uuid;
    }
}

if (!function_exists('replace_newlines')) {
    function replace_newlines($string, $symbol = "\n")
    {
        return str_replace(["\r\n", "\r", "\n"], $symbol, $string);
    }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));

        return $fc.mb_substr($str, 1);
    }
}

if (!function_exists('multiexplode')) {
    function multiexplode($delimiters, $string)
    {
        if (is_string($delimiters)) {
            $delimiters = str_split($delimiters);
        }
        $ready = str_replace($delimiters, $delimiters[0], $string);
        return explode($delimiters[0], $ready);
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
        $k1 = ord($k[0]);
        $k2 = ord($k[1]);

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

if (!function_exists('mb_strrev')) {
    function mb_strrev($string)
    {
        preg_match_all('/./us', $string, $matches);

        return implode('', array_reverse($matches[0]));
    }
}
