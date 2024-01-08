<?php

namespace Iqbalatma\AuditElasticsearch\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Iqbalatma\AuditElasticsearch\Models\Audit;

class ReindexingElasticsearchAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:reindex';

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
        $this->info("Indexing all data audit into elasticsearch");
        foreach (audit_model()::where("is_elastic_sync", false)->cursor() as $audit) {
            es()->index([
                "index" => config("auditelasticsearch.elasticsearch.prefix") . "_" . config("auditelasticsearch.elasticsearch.app_name") . "_" . $audit->created_at->format("Ymd"),
                'body' => $audit,
                'id' => $audit->id
            ]);
        }
        $this->info("Indexing successfully");
    }
}
