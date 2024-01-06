<?php

namespace Iqbalatma\AuditElasticsearch;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Iqbalatma\AuditElasticsearch\Commands\PruningAuditCommand;
use Iqbalatma\AuditElasticsearch\Commands\ReindexingElasticsearchAudit;

class AuditElasticsearchProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/auditelasticsearch.php', 'auditelasticsearch');

        $this->publishes([
            __DIR__.'/config/auditelasticsearch.php' => config_path('auditelasticsearch.php'),
        ], "config");

        $this->app->singleton("elasticsearch", function ($app) {
            if (config("auditelasticsearch.elasticsearch.enable")) {
                return ClientBuilder::create()
                    ->setHosts([config('auditelasticsearch.elasticsearch.host')])
                    ->setBasicAuthentication(config('auditelasticsearch.elasticsearch.username'), config('auditelasticsearch.elasticsearch.password'))
                    ->build();
            }

            return null;
        });


    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PruningAuditCommand::class,
                ReindexingElasticsearchAudit::class
            ]);
        }
    }
}
