<?php

return [

    // Nombre de la plantilla registrada en la API de Meta WhatsApp Cloud
    'meta_name' => 'driver_shift_start_reminder',

    // Variables esperadas en orden de aparición
    'variables' => [
        'driver_name',
        'start_time',
        'shift_url',
    ],

    // Texto de respaldo (fallback) para drivers de texto plano o logs
    'text' => "
⏰ *¡TU TURNO ESTÁ POR INICIAR!* ⏰

Hola {{driver_name}}, tu turno de trabajo comienza en 10 minutos (a las *{{start_time}}*).

Por favor, presiona el siguiente enlace para confirmar tu disponibilidad e ingresar a la app:
👉 {{shift_url}}

¡Que tengas una excelente jornada! 🛵💨
"

];