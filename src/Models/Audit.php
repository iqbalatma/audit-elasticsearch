<?php

namespace Iqbalatma\AuditElasticsearch\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string id
 * @property string actor_type
 * @property string actor_id
 * @property string actor_name
 * @property string actor_phone
 * @property string endpoint
 * @property string ip_address
 * @property string user_agent
 * @property string action
 * @property string message
 * @property string trail
 * @property string app_name
 * @property string is_elastic_sync
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Audit extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = "public.audits";

    protected $fillable = [
        "actor_type", "actor_id", "actor_name", "actor_phone", "endpoint", "ip_address", "user_agent", "action", "message", "trail", "app_name", "is_elastic_sync"
    ];

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return (new static())->table;
    }
}
