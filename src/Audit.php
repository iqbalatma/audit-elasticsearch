<?php

namespace Iqbalatma\AuditElasticsearch;

use App\Models\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Iqbalatma\AuditElasticsearch\Jobs\AuditJob;

class Audit
{
    public string|null $actorType;
    public string|null $actorId;
    public string|null $actorName;
    public string|null $actorPhone;
    public string|null $endpoint;
    public string|null $ipAddress;
    public string|null $userAgent;
    public string $action;
    public string $method;
    public string $message;
    public array $tag;
    public array $additional;
    public Collection $before;
    public Collection $after;
    public string|null $objectType;
    public string|null $objectId;

    public function __construct()
    {
        if (method_exists(Auth::user(), "getRoleNames") && config("auditelasticsearch.is_role_from_spatie")) {
            $this->additional = ["actor_role" => Auth::user()->getRoleNames()->toArray()];
        } else {
            $this->additional = [];
        }
        $this->message = "";
        $this->tag = [];
        $this->before = collect();
        $this->after = collect();
        $this->objectType = null;
        $this->objectId = null;
        $this->setActor()
            ->setNetwork();
    }

    /**
     * @return self
     */
    public static function init(): self
    {
        return new static();
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function setObject(Model $model): self
    {
        $this->objectType = $model->getTable();
        $this->objectId = $model->getKey();

        return $this;
    }

    /**
     * @param string $key
     * @param array|string|null $before
     * @return $this
     */
    public function addBefore(string $key, array|string|null $before): self
    {
        $this->before->put($key, $before);

        return $this;
    }


    /**
     * @param string $key
     * @param array|string|null $after
     * @return $this
     */
    public function addAfter(string $key, array|string|null $after): self
    {
        $this->after->put($key, $after);
        return $this;
    }


    /**
     * @param string $action
     * @return $this
     */
    public function action(string $action): self
    {
        $this->action = $action;
        return $this;
    }


    /**
     * @param string $message
     * @return $this
     */
    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }


    /**
     * @param array $tag
     * @return $this
     */
    public function tag(array $tag): self
    {
        $this->tag = array_merge($this->tag, $tag);
        return $this;
    }

    /**
     * @param array $additional
     * @return $this
     */
    public function additional(array $additional): self
    {
        $this->additional = array_merge($this->additional, $additional);
        return $this;
    }


    /**
     * @return $this
     */
    protected function setActor(): self
    {
        $user = Auth::user();

        $this->actorType = $user?->getMorphClass();
        $this->actorId = $user?->getKey();
        $this->actorName = $user?->name;
        $this->actorPhone = $user?->phone;

        return $this;
    }




    /**
     * @return void
     */
    protected function setNetwork(): void
    {
        $this->method = request()?->getMethod();
        $this->ipAddress = request()?->getClientIp();
        $this->userAgent = request()?->header("user-agent");
        $this->endpoint = parse_url(request()?->url())["path"] ?? null;
    }

    /**
     * @param array $before
     * @param array $after
     * @return void
     */
    public function log(array $before = [], array $after = []): void
    {
        $this->before = $this->before->merge($before);
        $this->after = $this->after->merge($after);

        if (count($this->after) > 0 || count($this->before) > 0) {
            AuditJob::dispatch($this);
        }
    }
}
