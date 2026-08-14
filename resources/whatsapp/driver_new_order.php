<?php

return [

    'meta_name' => 'driver_new_order',

    'variables' => [
        'driver_name',
        'order_id',
        'restaurant_name',
        'url' // 👈 Agregar la variable
    ],

    'text' => "
¡Hola {{driver_name}}! 🛵

Tienes una nueva orden asignada (#{{order_id}}) de *{{restaurant_name}}*. 

Acepta o gestiona tu pedido en el siguiente enlace:
{{url}}
"

];