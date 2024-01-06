<?php

namespace Iqbalatma\AuditElasticsearch\Commands;

use Illuminate\Console\Command;
use Iqbalatma\AuditElasticsearch\Models\Audit;

class ReindexingElasticsearchAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:reindex-audit';

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
        foreach (audit_model()::cursor() as $audit) {
            es()->index([
                "index" => config("auditelasticsearch.elasticsearch.prefix") . config("auditelasticsearch.audit_log_es_sufix"),
                'body' => $audit,
                'id' => $audit->id
            ]);
        }
        $this->info("Indexing successfully");
    }
}
