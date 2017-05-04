<?php

if (!defined('ABSPATH')) {
    exit;
}

abstract class Singleton
{
    protected static $instance = null;

    public static function initialize()
    {
        if (is_null(self::$instance)) {
            $klass = get_called_class();
            self::$instance = new $klass();
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}