<?php

use Elastic\Elasticsearch\Client;

if (! function_exists('es')) {
    /**
     * Returning instance of elasticsearch client
     *
     * @return Client
     */
    function es(): Client
    {
        return app("elasticsearch");
    }
}


if (! function_exists('audit_model')) {
    /**
     * Returning class of audit model
     *
     * @return string
     */
    function audit_model(): string
    {
        return  config('auditelasticsearch.audit_model');
    }
}
