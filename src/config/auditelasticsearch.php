<?php

use Iqbalatma\AuditElasticsearch\Models\Audit;

return [
    'elasticsearch' => [
        'enable' => env('ELASTICSEARCH_ENABLE', false),
        'app_name' => env('ELASTICSEARCH_APP_NAME', ""),
        'host' => env('ELASTICSEARCH_HOST', ''),
        'username' => env('ELASTICSEARCH_USERNAME', ''),
        'password' => env('ELASTICSEARCH_PASSWORD', ''),
        'prefix' => env('ELASTICSEARCH_PREFIX', ''),
    ],
    'audit_log_retention' => (int)env('AUDIT_LOG_RETENTION', 1),
    "audit_model" => Audit::class,
    "audit_model_connection" => env('AUDIT_CONNECTION', 'pgsql'),
    "is_role_from_spatie" => env('ROLE_FROM_SPATIE', false),
];
