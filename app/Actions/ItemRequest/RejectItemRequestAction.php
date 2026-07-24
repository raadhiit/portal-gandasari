<?php

namespace App\Actions\ItemRequest;

use App\Enums\ItemRequestStatus;
use App\Models\ItemRequest;
use App\Models\ItemRequestHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RejectItemRequestAction
{
    public function handle(User $actor, ItemRequest $itemRequest, string $remarks): ItemRequest
    {
        if (! in_array($itemRequest->status, [ItemRequestStatus::Submitted, ItemRequestStatus::Validated, ItemRequestStatus::Accounted], true)) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa direject dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $remarks): ItemRequest {
            $itemRequest->forceFill([
                'status' => ItemRequestStatus::Rejected,
                'current_step' => 'done',
                'last_remarks' => $remarks,
                'rejected_at' => now(),
            ])->save();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'rejected',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Rejected->value,
                'remarks' => $remarks,
                'snapshot' => $itemRequest->fresh()?->toArray(),
            ]);

            return $itemRequest->fresh();
        });
    }
}
