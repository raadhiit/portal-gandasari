<?php

namespace Modules\ItemRequest\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;
use Modules\ItemRequest\Models\ItemRequestHistory;
use Modules\ItemRequest\Support\WorkflowAuditService;

class AccountItemRequestAction
{
    public function __construct(private WorkflowAuditService $workflowAuditService) {}

    public function handle(User $actor, ItemRequest $itemRequest, int $coaId): ItemRequest
    {
        if ($itemRequest->status !== ItemRequestStatus::Validated) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa diaccount dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $coaId): ItemRequest {
            $before = $itemRequest->toArray();

            $itemRequest->forceFill([
                'coa_id' => $coaId,
                'status' => ItemRequestStatus::Accounted,
                'current_step' => 'approver',
                'accounted_at' => now(),
            ])->save();

            $freshItemRequest = $itemRequest->fresh();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'accounted',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Accounted->value,
                'snapshot' => $freshItemRequest?->toArray(),
            ]);

            if ($freshItemRequest !== null) {
                $this->workflowAuditService->log($actor, $freshItemRequest, 'account', null, $before, $freshItemRequest->toArray());
            }

            return $freshItemRequest;
        });
    }
}
