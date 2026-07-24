<?php

namespace App\Actions\ItemRequest;

use App\Enums\ItemRequestStatus;
use App\Models\ItemRequest;
use App\Models\ItemRequestHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountItemRequestAction
{
    public function handle(User $actor, ItemRequest $itemRequest, int $coaId): ItemRequest
    {
        if ($itemRequest->status !== ItemRequestStatus::Validated) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa diaccount dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $coaId): ItemRequest {
            $itemRequest->forceFill([
                'coa_id' => $coaId,
                'status' => ItemRequestStatus::Accounted,
                'current_step' => 'approver',
                'accounted_at' => now(),
            ])->save();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'accounted',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Accounted->value,
                'snapshot' => $itemRequest->fresh()?->toArray(),
            ]);

            return $itemRequest->fresh();
        });
    }
}
