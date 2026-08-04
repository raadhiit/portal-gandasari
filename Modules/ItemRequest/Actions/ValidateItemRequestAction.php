<?php

namespace Modules\ItemRequest\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;
use Modules\ItemRequest\Models\ItemRequestHistory;
use Modules\ItemRequest\Models\ItemRequestItem;
use Modules\ItemRequest\Support\WorkflowAuditService;
use Modules\ItemRequest\Support\WorkflowNotificationService;

class ValidateItemRequestAction
{
    public function __construct(
        private WorkflowNotificationService $workflowNotificationService,
        private WorkflowAuditService $workflowAuditService,
    ) {}

    /**
     * @param  array<int, array{id: int, category_id: int, item_code: string}>  $items
     */
    public function handle(User $actor, ItemRequest $itemRequest, array $items): ItemRequest
    {
        if ($itemRequest->status !== ItemRequestStatus::Submitted) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa divalidasi dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $items): ItemRequest {
            $before = $itemRequest->load('items')->toArray();

            foreach ($items as $item) {
                $itemCode = trim($item['item_code']);

                if (DB::table('items')->where('item_code', $itemCode)->exists()) {
                    throw ValidationException::withMessages([
                        'items' => sprintf('Item code %s has already been used in master items.', $itemCode),
                    ]);
                }

                if (ItemRequestItem::query()->where('item_code', $itemCode)->whereKeyNot($item['id'])->exists()) {
                    throw ValidationException::withMessages([
                        'items' => sprintf('Item code %s has already been used in another request item.', $itemCode),
                    ]);
                }

                $itemRequest->items()->whereKey($item['id'])->update([
                    'category_id' => $item['category_id'],
                    'item_code' => $itemCode,
                    'updated_at' => now(),
                ]);
            }

            $itemRequest->forceFill([
                'status' => ItemRequestStatus::Validated,
                'current_step' => 'accounting',
                'validated_at' => now(),
            ])->save();

            $freshItemRequest = $itemRequest->fresh(['items']);

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'validated',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Validated->value,
                'snapshot' => $freshItemRequest?->toArray(),
            ]);

            if ($freshItemRequest !== null) {
                $this->workflowNotificationService->notifyValidated($freshItemRequest);
                $this->workflowAuditService->log($actor, $freshItemRequest, 'validate', null, $before, $freshItemRequest->toArray());
            }

            return $freshItemRequest;
        });
    }
}
