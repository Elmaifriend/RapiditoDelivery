<?php

namespace App\WhatsApp\Contracts;

interface WhatsAppDriver
{
    public function sendText(string $to, string $message): bool;

    public function sendTemplate(string $to, string $template, array $params = []): bool;

    public function sendMedia(string $to, string $url, ?string $caption = null): bool;

    public function supportsTemplates(): bool;
}