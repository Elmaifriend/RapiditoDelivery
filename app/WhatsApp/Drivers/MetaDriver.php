<?php

namespace App\WhatsApp\Drivers;

use App\WhatsApp\Contracts\WhatsAppDriver;

class MetaDriver implements WhatsAppDriver
{
    public function sendText(string $to, string $message): bool
    {
        // TODO
        return false;
    }

    public function sendTemplate(string $to, string $template, array $params = []): bool
    {
        // TODO
        return false;
    }

    public function sendMedia(string $to, string $url, ?string $caption = null): bool
    {
        // TODO
        return false;
    }

    public function supportsTemplates(): bool
    {
        return true;
    }
}