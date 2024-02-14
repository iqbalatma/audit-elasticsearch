<?php

namespace Iqbalatma\AuditElasticsearch\Commands;

use Illuminate\Console\Command;
use Iqbalatma\AuditElasticsearch\Jobs\ReindexingJob;

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

        if($isForce){
            audit_model()::query()->update(["synced_at" => null]);
        }
        foreach (audit_model()::whereNull("synced_at")->cursor() as $audit) {
            ReindexingJob::dispatch($isForce, $audit);
        }
        $this->info("Indexing successfully");
    }
}
