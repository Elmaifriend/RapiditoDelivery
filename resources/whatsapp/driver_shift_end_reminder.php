<?php

return [

    // Nombre de la plantilla registrada en la API de Meta WhatsApp Cloud
    'meta_name' => 'driver_shift_end_reminder',

    // Variables esperadas en orden de aparición
    'variables' => [
        'driver_name',
        'end_time',
        'shift_url',
    ],

    // Texto de respaldo (fallback) para drivers de texto plano o logs
    'text' => "
🏁 *¡TU TURNO ESTÁ POR FINALIZAR!* 🏁

Hola {{driver_name}}, tu turno de trabajo finaliza en 5 minutos (a las *{{end_time}}*).

Recuerda concluir la entrega de tus pedidos pendientes y actualizar tu disponibilidad desde la app:
👉 {{shift_url}}

¡Muchas gracias por tu esfuerzo hoy! 🛵💨
"

];