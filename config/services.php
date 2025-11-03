<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization_id' => env('OPENAI_ORGANIZATION_ID'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
        'timeout' => env('OPENAI_TIMEOUT', 60),
    ],

    'qwen' => [
        'api_key' => env('QWEN_API_KEY'),
        'base_url' => env('QWEN_BASE_URL', 'https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation'),
        'model' => env('QWEN_MODEL', 'qwen-plus'),
        'max_tokens' => env('QWEN_MAX_TOKENS', 1000),
        'timeout' => env('QWEN_TIMEOUT', 30),
    ],

    'alphavantage' => [
        'key' => env('ALPHA_VANTAGE_API_KEY'),
        'timeout' => env('ALPHA_VANTAGE_TIMEOUT', 10),
        'enabled' => env('ALPHA_VANTAGE_ENABLED', true),
    ],

    'yahoo_finance' => [
        'timeout' => env('YAHOO_FINANCE_TIMEOUT', 5),
        'enabled' => env('YAHOO_FINANCE_ENABLED', true),
        'base_url' => env('YAHOO_FINANCE_BASE_URL', 'https://query1.finance.yahoo.com'),
    ],

    'stock_search' => [
        'cache_ttl' => env('STOCK_SEARCH_CACHE_TTL', 300), // 5 minutes
        'max_results' => env('STOCK_SEARCH_MAX_RESULTS', 20),
        'debounce_time' => env('STOCK_SEARCH_DEBOUNCE_TIME', 300), // milliseconds
    ],

];
