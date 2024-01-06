<?php

use Iqbalatma\AuditElasticsearch\Models\Audit;

return [
    'elasticsearch' => [
        'enable' => env('ELASTICSEARCH_ENABLE', false),
        'host' => env('ELASTICSEARCH_HOST', ''),
        'username' => env('ELASTICSEARCH_USERNAME', ''),
        'password' => env('ELASTICSEARCH_PASSWORD', ''),
        'prefix' => env('ELASTICSEARCH_PREFIX', ''),
    ],
    'audit_log_retention' => (int) env('AUDIT_LOG_RETENTION', 1),
    "audit_model" => Audit::class,
    "audit_log_es_sufix" => "audit_log"
];
