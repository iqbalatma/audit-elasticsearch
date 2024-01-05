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
