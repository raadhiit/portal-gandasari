<?php

namespace App\Actions\ItemRequest;

use App\Enums\ItemRequestStatus;
use App\Models\ItemRequest;
use App\Models\ItemRequestHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateItemRequestAction
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function handle(User $requester, array $input): ItemRequest
    {
        return DB::transaction(function () use ($requester, $input): ItemRequest {
            $itemRequest = ItemRequest::create([
                'requester_id' => $requester->id,
                'department_id' => $requester->department_id,
                'unit_id' => $input['unit_id'],
                'warehouse_id' => $input['warehouse_id'],
                'item_name' => $input['item_name'],
                'item_type' => $input['item_type'],
                'notes' => $input['notes'] ?? null,
                'status' => ItemRequestStatus::Draft,
                'current_step' => 'requester',
            ]);

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $requester->id,
                'action' => 'draft_created',
                'to_status' => ItemRequestStatus::Draft->value,
                'snapshot' => $itemRequest->fresh()?->toArray(),
            ]);

            return $itemRequest->fresh();
        });
    }
}
