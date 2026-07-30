<?php

namespace Modules\ItemRequest\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;
use Modules\ItemRequest\Models\ItemRequestHistory;

class CreateItemRequestAction
{
    /**
     * @param  array{business_unit_id: int, notes?: string|null, items: array<int, array{item_name: string, item_type: string, unit_id: int}>}  $input
     */
    public function handle(User $requester, array $input): ItemRequest
    {
        return DB::transaction(function () use ($requester, $input): ItemRequest {
            $itemRequest = ItemRequest::create([
                'requester_id' => $requester->id,
                'department_id' => $requester->department_id,
                'business_unit_id' => $input['business_unit_id'],
                'notes' => $input['notes'] ?? null,
                'status' => ItemRequestStatus::Draft,
                'current_step' => 'requester',
            ]);

            $itemRequest->items()->createMany($input['items']);

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $requester->id,
                'action' => 'draft_created',
                'to_status' => ItemRequestStatus::Draft->value,
                'snapshot' => $itemRequest->fresh(['items'])?->toArray(),
            ]);

            return $itemRequest->fresh(['items']);
        });
    }
}
