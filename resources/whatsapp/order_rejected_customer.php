<?php

return [

    'meta_name' => 'order_rejected_customer',

    'variables' => [
        'name',
        'restaurant_name',
    ],

    'text' => "
Hola {{name}} 😔

Lamentamos informarte que el restaurante *{{restaurant_name}}* no pudo aceptar tu pedido en este momento.
"

];