<?php

namespace Iqbalatma\AuditElasticsearch\Jobs;

use Carbon\Carbon;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Iqbalatma\AuditElasticsearch\Models\Audit;

class ReindexingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Audit $audit)
    {
    }

    /**
     * @return void
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $this->audit->refresh();
            $this->audit->synced_at = Carbon::now();
            $this->audit->save();

            es()->index([
                "index" => config("auditelasticsearch.elasticsearch.prefix") . "_" . $this->audit->app_name . "_" . $this->audit->created_at->format("Ymd"),
                'body' => $this->audit,
                'id' => $this->audit->id
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Indexing audit to " . $this->audit->app_name . " data with id: " . $this->audit->id . " failed. Error : " . $e->getMessage());
            throw $e;
        }
    }
}
