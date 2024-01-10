<?php

namespace Iqbalatma\AuditElasticsearch\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Iqbalatma\AuditElasticsearch\Audit;
use JsonException;

class AuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const PACKAGE_VERSION = "0.0.12";

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
        audit_model()::query()->create([
            "message" => $this->audit->message ?? "",
            "action" => $this->audit->action ?? "",
            "ip_address" => $this->audit->ipAddress ?? "",
            "endpoint" => $this->audit->endpoint ?? "",
            "user_agent" => $this->audit->userAgent ?? "",
            "actor_type" => $this->audit->actorType ?? "",
            "actor_id" => $this->audit->actorId ?? "",
            "actor_name" => $this->audit->actorName ?? "",
            "actor_phone" => $this->audit->actorPhone ?? "",
            "tags" => json_encode($this->audit->tag, JSON_THROW_ON_ERROR),
            "additional_data" => json_encode(array_merge(["version" => self::PACKAGE_VERSION], $this->audit->additional), JSON_THROW_ON_ERROR),
            "is_elastic_sync" => false,
            "app_name" => config("auditelasticsearch.elasticsearch.app_name"),
            "trail" => json_encode([
                "before" => $this->audit->before,
                "after" => $this->audit->after,
            ], JSON_THROW_ON_ERROR),
        ]);
    }
}
