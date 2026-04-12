<?php

namespace App\WhatsApp\Templates;

class TemplateRepository
{
    public static function get(string $template): array
    {
        $path = resource_path("whatsapp/{$template}.php");

        if (!file_exists($path)) {
            throw new \Exception("WhatsApp template '{$template}' not found.");
        }

        return include $path;
    }
}

