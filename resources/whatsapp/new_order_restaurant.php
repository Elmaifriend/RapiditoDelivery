<?php

return [

    // Nombre de la plantilla registrada en la API de Meta WhatsApp Cloud
    'meta_name' => 'new_order_restaurant',

    // Variables esperadas en orden de aparición
    'variables' => [
        'restaurant_name',
        'order_id',
        'items_summary',
        'special_instructions',
        'dashboard_url' // Añadimos la nueva variable aquí al final
    ],

    // Texto de respaldo (fallback) para drivers de texto plano
    'text' => "
🚨 *¡NUEVO PEDIDO RECIBIDO!* 🚨

Hola {{restaurant_name}}, tienes una nueva orden para preparar: *#{{order_id}}*.

*Detalle del Pedido:*
{{items_summary}}

*Indicaciones especiales:*
{{special_instructions}}

Toca el siguiente enlace para abrir la pantalla de tu cocina:
👉 {{dashboard_url}} 👩‍🍳
"

];