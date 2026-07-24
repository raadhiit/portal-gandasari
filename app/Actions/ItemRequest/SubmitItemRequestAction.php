<?php

namespace App\Actions\ItemRequest;

use App\Enums\ItemRequestStatus;
use App\Models\ItemRequest;
use App\Models\ItemRequestHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitItemRequestAction
{
    public function handle(User $actor, ItemRequest $itemRequest): ItemRequest
    {
        if (! in_array($itemRequest->status, [ItemRequestStatus::Draft, ItemRequestStatus::Revised], true)) {
            throw ValidationException::withMessages(['status' => 'Item request tidak bisa disubmit dari status saat ini.']);
        }

        return DB::transaction(function () use ($actor, $itemRequest): ItemRequest {
            $itemRequest->forceFill([
                'request_no' => $itemRequest->request_no ?? $this->generateRequestNumber(),
                'status' => ItemRequestStatus::Submitted,
                'current_step' => 'validator',
                'submitted_at' => now(),
            ])->save();

            ItemRequestHistory::create([
                'item_request_id' => $itemRequest->id,
                'actor_id' => $actor->id,
                'action' => 'submitted',
                'from_status' => $itemRequest->getOriginal('status'),
                'to_status' => ItemRequestStatus::Submitted->value,
                'snapshot' => $itemRequest->fresh()?->toArray(),
            ]);

            return $itemRequest->fresh();
        });
    }

    private function generateRequestNumber(): string
    {
        $datePrefix = now()->format('Y');
        $count = ItemRequest::query()->whereYear('created_at', now()->year)->count() + 1;

        return sprintf('REQ-%s-%04d', $datePrefix, $count);
    }
}
