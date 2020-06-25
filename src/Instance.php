<?php

namespace Febalist\Laravel\Support;

class Instance
{
    /** @var static */
    protected static $instance;

    /** @return static */
    public static function instance()
    {
        return static::$instance = static::$instance ?? new static();
    }
}
