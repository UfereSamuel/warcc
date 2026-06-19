<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Provider (OpenAI-compatible API)
    |--------------------------------------------------------------------------
    |
    | Works with OpenAI, Azure OpenAI, OpenRouter, Groq, Ollama (OpenAI mode),
    | and other providers that expose /chat/completions.
    |
    */

    'enabled' => env('AI_ENABLED', true),

    'api_key' => env('AI_API_KEY'),

    'base_url' => rtrim(env('AI_BASE_URL', 'https://api.openai.com/v1'), '/'),

    'model' => env('AI_MODEL', 'gpt-4o-mini'),

    'timeout' => (int) env('AI_TIMEOUT', 120),

    'max_reports_per_merge' => (int) env('AI_MAX_REPORTS_PER_MERGE', 10),

];
