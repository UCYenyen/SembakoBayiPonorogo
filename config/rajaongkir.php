<?php

return [
    'api_key' => env('RAJAONGKIR_API_KEY'),
    'type' => env('RAJAONGKIR_TYPE', 'starter'),
    'base_url' => env('RAJAONGKIR_TYPE', 'starter') === 'pro' 
        ? 'https://pro.rajaongkir.com/api' 
        : 'https://api.rajaongkir.com/starter',
];