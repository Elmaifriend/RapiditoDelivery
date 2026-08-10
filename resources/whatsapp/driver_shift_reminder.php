<?php

return [

    // Nombre de la plantilla registrada en la API de Meta WhatsApp Cloud
    'meta_name' => 'driver_shift_reminder',

    // Variables esperadas en orden de aparición según la llamada desde WhatsAppNotifierService
    'variables' => [
        'driver_name',
        'start_time',
        'shift_url',
    ],

    // Texto de respaldo (fallback) para drivers de texto plano o logs
    'text' => "
⏰ *¡RECORDATORIO DE TURNO!* ⏰

Hola {{driver_name}}, tu turno de trabajo comienza pronto (a las *{{start_time}}*).

Por favor, presiona el siguiente enlace para confirmar tu asistencia e iniciar tu turno:
👉 {{shift_url}}

¡Que tengas una excelente jornada! 🛵💨
"

];