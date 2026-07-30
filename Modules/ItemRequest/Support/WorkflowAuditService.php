<?php

namespace Modules\ItemRequest\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\ItemRequest\Models\ItemRequest;

class WorkflowAuditService
{
    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    public function log(User $actor, ItemRequest $itemRequest, string $action, ?string $remarks = null, ?array $before = null, ?array $after = null): void
    {
        DB::table('audit_logs')->insert([
            'actor_id' => $actor->id,
            'module' => 'item-request',
            'action' => $action,
            'auditable_type' => ItemRequest::class,
            'auditable_id' => $itemRequest->id,
            'before_json' => $before === null ? null : json_encode($before, JSON_THROW_ON_ERROR),
            'after_json' => $after === null ? null : json_encode($after, JSON_THROW_ON_ERROR),
            'remarks' => $remarks,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
