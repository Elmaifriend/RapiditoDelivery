<?php

return [

    // Nombre de la plantilla registrada en la API de Meta WhatsApp Cloud
    'meta_name' => 'order_processed_customer',

    // Variables esperadas en orden de aparición estricto
    'variables' => [
        'name',
        'order_id',
        'items_list',
        'subtotal',
        'delivery_fee',
        'total',
    ],

    // Texto de respaldo (fallback) para drivers de texto plano
    'text' => "
¡Hola, {{name}}! 👋

Solo para avisarte que tu pedido se hizo correctamente. Actualmente estamos esperando a que lo confirme el restaurante. 🕒

*Tu número de orden:* #{{order_id}}

*Lo que pediste:*
{{items_list}}
---
*Subtotal:* {{subtotal}}
*Costo de envío:* {{delivery_fee}}
*Total a pagar:* *{{total}}*

Te avisaremos en cuanto el restaurante acepte tu pedido y se prepare el envío. ¡Muchas gracias! 🍔
"

];