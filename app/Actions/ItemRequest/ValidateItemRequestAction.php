<?php

namespace App\Actions\ItemRequest;

use App\Enums\ItemRequestStatus;
use App\Models\ItemRequest;
use App\Models\ItemRequestHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ValidateItemRequestAction
{
    /**
     * @param  array{category_id: int, item_code: string}  $input
     */
    public function handle(User $actor, ItemRequest $itemRequest, array $input): ItemRequest
    {
        if ($itemRequest->status !== ItemRequestStatus::Submitted) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa divalidasi dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest, $input): ItemRequest {
            $itemRequest->forceFill([
                'category_id' => $input['category_id'],
                'item_code' => $input['item_code'],
                'status' => ItemRequestStatus::Validated,
                'current_step' => 'accounting',
                'validated_at' => now(),
            ])->save();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'validated',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Validated->value,
                'snapshot' => $itemRequest->fresh()?->toArray(),
            ]);

            return $itemRequest->fresh();
        });
    }
}
