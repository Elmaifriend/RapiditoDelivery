<?php

return [

    // Nombre de la plantilla registrada en la API de Meta WhatsApp Cloud
    'meta_name' => 'customer_order_ready',

    // Variables esperadas en la plantilla
    'variables' => [
        'customer_name',
        'business_name',
    ],

    // Texto de respaldo para la plantilla
    'text' => "
🙌 *¡TU PEDIDO ESTÁ LISTO!* 🍽️

Hola {{customer_name}}, te informamos que tu pedido en *{{business_name}}* ya está listo y el repartidor esta en camino.

¡Muchas gracias por tu compra! 🛵💨
"

];