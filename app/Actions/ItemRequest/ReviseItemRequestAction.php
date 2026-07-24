<?php

namespace App\Actions\ItemRequest;

use App\Enums\ItemRequestStatus;
use App\Models\ItemRequest;
use App\Models\ItemRequestHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviseItemRequestAction
{
    public function handle(User $actor, ItemRequest $itemRequest, string $remarks): ItemRequest
    {
        if (! in_array($itemRequest->status, [ItemRequestStatus::Submitted, ItemRequestStatus::Validated, ItemRequestStatus::Accounted], true)) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa direvisi dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $remarks): ItemRequest {
            $itemRequest->forceFill([
                'status' => ItemRequestStatus::Revised,
                'current_step' => 'requester',
                'last_remarks' => $remarks,
                'revision_count' => $itemRequest->revision_count + 1,
            ])->save();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'revised',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Revised->value,
                'remarks' => $remarks,
                'snapshot' => $itemRequest->fresh()?->toArray(),
            ]);

            return $itemRequest->fresh();
        });
    }
}
