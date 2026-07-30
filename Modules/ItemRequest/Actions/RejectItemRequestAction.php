<?php

namespace Modules\ItemRequest\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;
use Modules\ItemRequest\Models\ItemRequestHistory;
use Modules\ItemRequest\Support\WorkflowAuditService;
use Modules\ItemRequest\Support\WorkflowNotificationService;

class RejectItemRequestAction
{
    public function __construct(
        private WorkflowNotificationService $workflowNotificationService,
        private WorkflowAuditService $workflowAuditService,
    ) {}

    public function handle(User $actor, ItemRequest $itemRequest, string $remarks): ItemRequest
    {
        if (! in_array($itemRequest->status, [ItemRequestStatus::Submitted, ItemRequestStatus::Validated, ItemRequestStatus::Accounted], true)) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa direject dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $remarks): ItemRequest {
            $before = $itemRequest->toArray();

            $itemRequest->forceFill([
                'status' => ItemRequestStatus::Rejected,
                'current_step' => 'done',
                'last_remarks' => $remarks,
                'rejected_at' => now(),
            ])->save();

            $freshItemRequest = $itemRequest->fresh();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'rejected',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Rejected->value,
                'remarks' => $remarks,
                'snapshot' => $freshItemRequest?->toArray(),
            ]);

            if ($freshItemRequest !== null) {
                $this->workflowNotificationService->notifyRejected($freshItemRequest);
                $this->workflowAuditService->log($actor, $freshItemRequest, 'reject', $remarks, $before, $freshItemRequest->toArray());
            }

            return $freshItemRequest;
        });
    }
}
