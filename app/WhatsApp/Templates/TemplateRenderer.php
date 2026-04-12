<?php

namespace App\WhatsApp\Templates;

class TemplateRenderer
{
    public static function render(string $template, array $params): string
    {
        $data = TemplateRepository::get($template);

        $text = $data['text'];

        foreach ($params as $key => $value) {
            $text = str_replace('{{'.$key.'}}', $value, $text);
        }

        return trim($text);
    }
}