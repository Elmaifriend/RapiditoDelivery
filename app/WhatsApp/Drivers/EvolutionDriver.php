<?php

namespace App\WhatsApp\Drivers;

use Illuminate\Support\Facades\Http;
use App\WhatsApp\Contracts\WhatsAppDriver;
use App\WhatsApp\Templates\TemplateRenderer;

class EvolutionDriver implements WhatsAppDriver
{
    protected string $baseUrl;
    protected string $token;
    protected string $instance;

    public function __construct()
    {
        $this->baseUrl = config('whatsapp.evolution.base_url');
        $this->token = config('whatsapp.evolution.token');
        $this->instance = config('whatsapp.evolution.instance');
    }

    public function sendText(string $to, string $message): bool
    {
        $url = "{$this->baseUrl}/message/sendText/{$this->instance}";

        $response = Http::withHeaders([
            'apikey' => $this->token,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'number' => $to,
            'text' => $message,
        ]);

        return $response->successful();
    }

    public function sendTemplate(string $to, string $template, array $params = []): bool
    {
        // Evolution NO soporta templates → renderizamos a texto
        $text = TemplateRenderer::render($template, $params);

        return $this->sendText($to, $text);
    }

    public function sendMedia(string $to, string $url, ?string $caption = null): bool
    {
        // Por ahora no implementado
        return false;
    }

    public function supportsTemplates(): bool
    {
        return false;
    }
}