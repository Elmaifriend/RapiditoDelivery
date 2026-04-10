<?php

return [

    'meta_name' => 'order_ready',

    'variables' => [
        'name',
        'order_id'
    ],

    'text' => "
Hola {{name}} 👋

Tu pedido #{{order_id}} está listo.
"

];