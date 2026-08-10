<?php

return [

    // Nombre de la plantilla registrada en Meta WhatsApp Cloud
    'meta_name' => 'business_status_change',

    // Variables esperadas en orden
    'variables' => [
        'restaurant_name',
        'status_label',   // Ej: "ABIERTO 🟢" o "CERRADO 🔴"
        'dashboard_url',
    ],

    // Texto de respaldo (fallback)
    'text' => "
🏪 *¡ACTUALIZACIÓN DE ESTADO!* 🏪

Hola *{{restaurant_name}}*, tu negocio ahora se encuentra: *{{status_label}}* según tu horario programado.

Puedes gestionar tus pedidos o ajustar tu estado desde tu panel:
👉 {{dashboard_url}}
"

];