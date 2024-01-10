<?php

namespace Iqbalatma\AuditElasticsearch\Commands;

use Carbon\Carbon;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Iqbalatma\AuditElasticsearch\Models\Audit;

class ReindexingElasticsearchAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:reindex {--force : Use to force indexing on sync true data}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use to populate new data into elasticsearch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isForce = $this->option("force");

        $this->info("Indexing all data audit into elasticsearch");
        if ($isForce) {
            foreach (audit_model()::cursor() as $audit) {
                $this->indexing($audit);
            }
        } else {
            foreach (audit_model()::where("is_elastic_sync", false)->cursor() as $audit) {
                $this->indexing($audit);
            }
        }
        $this->info("Indexing successfully");
    }


    /**
     * @param Model $audit
     * @return void
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    private function indexing(Model $audit): void
    {
        try {
            $audit->refresh();
            es()->index([
                "index" => config("auditelasticsearch.elasticsearch.prefix") . "_" . $audit->app_name . "_" . $audit->created_at->format("Ymd"),
                'body' => $audit,
                'id' => $audit->id
            ]);
            $audit->is_elastic_sync = true;
            $audit->save();
            $this->info("Indexing audit to $audit->app_name data with id: $audit->id successfully");
        } catch (Exception $e) {
            $this->info("Indexing audit to $audit->app_name data with id: $audit->id failed. Error : " . $e->getMessage());
        }
    }
}
