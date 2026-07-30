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

class ApproveItemRequestAction
{
    public function __construct(
        private WorkflowNotificationService $workflowNotificationService,
        private WorkflowAuditService $workflowAuditService,
    ) {}

    public function handle(User $actor, ItemRequest $itemRequest): ItemRequest
    {
        if ($itemRequest->status !== ItemRequestStatus::Accounted) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa diapprove dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest): ItemRequest {
            $before = $itemRequest->toArray();

            $itemRequest->forceFill([
                'status' => ItemRequestStatus::Approved,
                'current_step' => 'done',
                'approved_at' => now(),
            ])->save();

            $freshItemRequest = $itemRequest->fresh();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'approved',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Approved->value,
                'snapshot' => $freshItemRequest?->toArray(),
            ]);

            if ($freshItemRequest !== null) {
                $this->workflowNotificationService->notifyApproved($freshItemRequest);
                $this->workflowAuditService->log($actor, $freshItemRequest, 'approve', null, $before, $freshItemRequest->toArray());
            }

            return $freshItemRequest;
        });
    }
}
