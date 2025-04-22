<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración predeterminada para los servicios de IA
    |--------------------------------------------------------------------------
    */
    
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'gemini'),
    
    'cache_enabled' => env('AI_CACHE_ENABLED', true),
    
    'cache_ttl' => env('AI_CACHE_TTL', 1440), // minutos
    
    /*
    |--------------------------------------------------------------------------
    | Configuración específica para cada proveedor de IA
    |--------------------------------------------------------------------------
    */
    
    'providers' => [
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1'),
            'model_id' => env('GEMINI_MODEL_ID', 'gemini-pro'),
        ],
        
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'organization' => env('OPENAI_ORGANIZATION'),
            'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
        ],
    ],
];