<?php

return [

    'driver' => env('WA_DRIVER', 'evolution'),

    'evolution' => [
        'base_url' => env('WA_EVOLUTION_BASE_URL'),
        'token' => env('WA_EVOLUTION_TOKEN'),
        'instance' => env('WA_EVOLUTION_INSTANCE'),
    ],

];