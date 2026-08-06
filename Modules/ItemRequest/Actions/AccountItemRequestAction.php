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

class AccountItemRequestAction
{
    public function __construct(
        private WorkflowAuditService $workflowAuditService,
        private WorkflowNotificationService $workflowNotificationService,
    ) {}

    /**
     * @param  array<int, array{id: int, inventory_coa_id: int, sales_coa_id: int}>  $items
     */
    public function handle(User $actor, ItemRequest $itemRequest, array $items): ItemRequest
    {
        if ($itemRequest->status !== ItemRequestStatus::Validated) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa diaccount dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $items): ItemRequest {
            $before = $itemRequest->load('items')->toArray();

            foreach ($items as $item) {
                $itemRequest->items()->whereKey($item['id'])->update([
                    'inventory_coa_id' => $item['inventory_coa_id'],
                    'sales_coa_id' => $item['sales_coa_id'],
                    'updated_at' => now(),
                ]);
            }

            $itemRequest->forceFill([
                'status' => ItemRequestStatus::WaitingForIT,
                'current_step' => 'it',
                'accounted_at' => now(),
            ])->save();

            $freshItemRequest = $itemRequest->fresh(['items']);

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'approved',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::WaitingForIT->value,
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
