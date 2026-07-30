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

class ReviseItemRequestAction
{
    public function __construct(
        private WorkflowNotificationService $workflowNotificationService,
        private WorkflowAuditService $workflowAuditService,
    ) {}

    public function handle(User $actor, ItemRequest $itemRequest, string $remarks): ItemRequest
    {
        if (! in_array($itemRequest->status, [ItemRequestStatus::Submitted, ItemRequestStatus::Validated, ItemRequestStatus::Accounted], true)) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa direvisi dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $remarks): ItemRequest {
            $before = $itemRequest->toArray();

            $itemRequest->forceFill([
                'status' => ItemRequestStatus::Revised,
                'current_step' => 'requester',
                'last_remarks' => $remarks,
                'revision_count' => $itemRequest->revision_count + 1,
            ])->save();

            $freshItemRequest = $itemRequest->fresh();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'revised',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Revised->value,
                'remarks' => $remarks,
                'snapshot' => $freshItemRequest?->toArray(),
            ]);

            if ($freshItemRequest !== null) {
                $this->workflowNotificationService->notifyRevised($freshItemRequest);
                $this->workflowAuditService->log($actor, $freshItemRequest, 'revise', $remarks, $before, $freshItemRequest->toArray());
            }

            return $freshItemRequest;
        });
    }
}
