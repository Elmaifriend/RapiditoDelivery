<?php

namespace App\WhatsApp;

class WhatsApp
{
    protected static ?WhatsAppManager $instance = null;

    protected static function instance(): WhatsAppManager
    {
        if (!static::$instance) {
            static::$instance = new WhatsAppManager();
        }

        return static::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return static::instance()->$method(...$args);
    }
}