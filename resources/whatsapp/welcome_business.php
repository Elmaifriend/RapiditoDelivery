<?php

return [

    // Nombre de la plantilla registrada en la API de Meta WhatsApp Cloud
    'meta_name' => 'welcome_business',

    // Variables esperadas en orden de aparición
    'variables' => [
        'user_name',
        'business_name',
        'catalog_url',
        'orders_url',
    ],

    // Texto de respaldo (fallback) para drivers de texto plano o logs
    'text' => "
👋 *¡BIENVENIDO AL EQUIPO DE RAPIDITO!* 🚀

Hola {{user_name}}, ¡estamos muy felices de darte la bienvenida a ti y a *{{business_name}}*! Vamos a hacer todo lo posible por ayudarte a incrementar tus ventas y hacer crecer tu negocio.

🛒 *Puedes ver tu catálogo en línea aquí:*
👉 {{catalog_url}}

📲 *Y puedes gestionar tus órdenes en tiempo real desde aquí:*
👉 {{orders_url}}

¡Mucho éxito, estamos listos para empezar! 🛵💨
"

];