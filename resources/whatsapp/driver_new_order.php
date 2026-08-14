<?php

return [

    'meta_name' => 'driver_new_order',

    'variables' => [
        'driver_name',
        'order_id',
        'restaurant_name'
    ],

    'text' => "
¡Hola {{driver_name}}! 🛵

Tienes una nueva orden asignada de *{{restaurant_name}}*. Por favor revisa tu aplicación para ver los detalles de recolección.
"

];