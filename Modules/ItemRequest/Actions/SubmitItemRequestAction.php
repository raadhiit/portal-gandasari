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

class SubmitItemRequestAction
{
    public function __construct(
        private WorkflowNotificationService $workflowNotificationService,
        private WorkflowAuditService $workflowAuditService,
    ) {}

    public function handle(User $actor, ItemRequest $itemRequest): ItemRequest
    {
        if (! in_array($itemRequest->status, [ItemRequestStatus::Draft, ItemRequestStatus::Revised], true)) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa disubmit dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest): ItemRequest {
            $before = $itemRequest->toArray();

            $itemRequest->forceFill([
                'request_no' => $itemRequest->request_no ?? $this->generateRequestNumber(),
                'status' => ItemRequestStatus::Submitted,
                'current_step' => 'validator',
                'submitted_at' => now(),
            ])->save();

            $freshItemRequest = $itemRequest->fresh();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'submitted',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Submitted->value,
                'snapshot' => $freshItemRequest?->toArray(),
            ]);

            if ($freshItemRequest !== null) {
                $this->workflowNotificationService->notifySubmitted($freshItemRequest);
                $this->workflowAuditService->log($actor, $freshItemRequest, 'submit', null, $before, $freshItemRequest->toArray());
            }

            return $freshItemRequest;
        });
    }

    private function generateRequestNumber(): string
    {
        $datePrefix = now()->format('Y');
        $count = ItemRequest::query()->whereYear('created_at', now()->year)->count() + 1;

        return sprintf('REQ-%s-%04d', $datePrefix, $count);
    }
}
