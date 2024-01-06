<?php

namespace Iqbalatma\AuditElasticsearch\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Iqbalatma\AuditElasticsearch\Audit;
use JsonException;

class AuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Audit $audit)
    {
    }

    /**
     * Execute the job.
     * @throws JsonException
     */
    public function handle(): void
    {
        $audit = audit_model()::query()->create([
            "message" => $this->audit->message ?? "",
            "action" => $this->audit->action ?? "",
            "ip_address" => $this->audit->ipAddress ?? "",
            "endpoint" => $this->audit->endpoint ?? "",
            "user_agent" => $this->audit->userAgent ?? "",
            "actor_type" => $this->audit->actorType ?? "",
            "actor_id" => $this->audit->actorId ?? "",
            "actor_name" => $this->audit->actorName ?? "",
            "actor_phone" => $this->audit->actorPhone ?? "",
            "trail" => json_encode([
                "before" => $this->audit->before,
                "after" => $this->audit->after,
            ], JSON_THROW_ON_ERROR),
        ]);

        if (config("auditelasticsearch.elasticsearch.enable")) {
            app("elasticsearch")->index([
                "index" => config("auditelasticsearch.elasticsearch.prefix") . config("auditelasticsearch.audit_log_es_sufix"),
                'body' => $audit,
                'id' => $audit->id,
            ]);
        }
    }
}
