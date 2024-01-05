<?php

namespace Iqbalatma\AuditElasticsearch\Commands;

use App\Models\Audit;
use Illuminate\Console\Command;

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
            foreach(Audit::cursor() as $audit){
                es()->index([
                    "index" => config("services.elasticsearch.prefix") . "admission_log",
                    'body' => $audit,
                    'id' => $audit->id
                ]);
            }
        $this->info("Indexing successfully");
    }
}
