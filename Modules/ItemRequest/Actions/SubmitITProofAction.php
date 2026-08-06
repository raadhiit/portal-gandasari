<?php

namespace Modules\ItemRequest\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;
use Modules\ItemRequest\Models\ItemRequestHistory;
use Modules\ItemRequest\Models\ItemRequestProof;
use Modules\ItemRequest\Support\WorkflowAuditService;
use Modules\ItemRequest\Support\WorkflowNotificationService;

class SubmitITProofAction
{
    public function __construct(
        private WorkflowAuditService $workflowAuditService,
        private WorkflowNotificationService $workflowNotificationService,
    ) {}

    public function handle(User $actor, ItemRequest $itemRequest, string $proofContent): ItemRequest
    {
        if ($itemRequest->status !== ItemRequestStatus::WaitingForIT) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa diapprove dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $proofContent): ItemRequest {
            $before = $itemRequest->toArray();

            ItemRequestProof::create([
                'item_request_id' => $itemRequest->id,
                'proof_content' => $proofContent,
                'actor_id' => $actor->id,
            ]);

            $itemRequest->forceFill([
                'status' => ItemRequestStatus::Finished,
                'current_step' => 'done',
                'approved_at' => now(),
            ])->save();

            $freshItemRequest = $itemRequest->fresh();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'approved',
                'from_status' => ItemRequestStatus::WaitingForIT->value,
                'to_status' => ItemRequestStatus::Finished->value,
                'remarks' => 'IT Proof submitted and approved.',
                'snapshot' => $freshItemRequest?->toArray(),
            ]);

            $this->workflowNotificationService->notifyApproved($freshItemRequest);
            $this->workflowAuditService->log($actor, $freshItemRequest, 'approve_it', null, $before, $freshItemRequest->toArray());

            return $freshItemRequest;
        });
    }
}
