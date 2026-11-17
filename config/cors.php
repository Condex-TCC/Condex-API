<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Options
    |--------------------------------------------------------------------------
    |
    | The allowed_origins deve ser ['*'] para permitir qualquer origem.
    | O supports_credentials deve ser false quando allowed_origins for '*',
    | o que é ideal para APIs baseadas em Bearer Token.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // Permite qualquer domínio/origem

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // Obrigatório ser false com allowed_origins '*'

];