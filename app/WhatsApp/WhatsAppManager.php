<?php

namespace App\WhatsApp;

use App\WhatsApp\Drivers\MetaDriver;
use App\WhatsApp\Drivers\TwilioDriver;
use App\WhatsApp\Drivers\EvolutionDriver;
use App\WhatsApp\Contracts\WhatsAppDriver;

class WhatsAppManager
{
    protected function driver(): WhatsAppDriver
    {
        return match (config('whatsapp.driver')) {
            'meta' => new MetaDriver(),
            'twilio' => new TwilioDriver(),
            'evolution' => new EvolutionDriver(),
            default => throw new \Exception('Invalid WhatsApp driver'),
        };
    }

    public function sendText(string $to, string $message): bool
    {
        return $this->driver()->sendText($to, $message);
    }

    public function sendTemplate(string $to, string $template, array $params = []): bool
    {
        $driver = $this->driver();

        if ($driver->supportsTemplates()) {
            return $driver->sendTemplate($to, $template, $params);
        }

        // fallback automático
        return $driver->sendTemplate($to, $template, $params);
    }

    public function sendMedia(string $to, string $url, ?string $caption = null): bool
    {
        return $this->driver()->sendMedia($to, $url, $caption);
    }
}