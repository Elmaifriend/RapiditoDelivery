<?php

return [

    'meta_name' => 'order_accepted_customer',

    'variables' => [
        'name',
        'restaurant_name',
    ],

    'text' => "
Hola {{name}} 👋

El restaurante *{{restaurant_name}}* aceptó tu pedido y ya lo están cocinando 🍳. Te avisaremos cuando el repartidor esté en camino.
"

];