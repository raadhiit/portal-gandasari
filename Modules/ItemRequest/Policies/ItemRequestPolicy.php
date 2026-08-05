<?php

namespace Modules\ItemRequest\Policies;

use App\Models\User;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;

class ItemRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('item-request.view');
    }

    public function view(User $user, ItemRequest $itemRequest): bool
    {
        if (! $user->can('item-request.view')) {
            return false;
        }

        if ($user->can('item-request.validate') || $user->can('item-request.account') || $user->can('item-request.approve')) {
            return $this->inDepartmentScope($user, $itemRequest);
        }

        return $user->id === $itemRequest->requester_id || $this->inDepartmentScope($user, $itemRequest);
    }

    public function create(User $user): bool
    {
        return $user->can('item-request.create');
    }

    public function update(User $user, ItemRequest $itemRequest): bool
    {
        if (! $user->can('item-request.update-own')) {
            return false;
        }

        if ($user->id !== $itemRequest->requester_id) {
            return false;
        }

        return in_array($itemRequest->status, [ItemRequestStatus::Draft, ItemRequestStatus::Revised], true);
    }

    public function submit(User $user, ItemRequest $itemRequest): bool
    {
        return $user->can('item-request.submit')
            && $user->id === $itemRequest->requester_id
            && in_array($itemRequest->status, [ItemRequestStatus::Draft, ItemRequestStatus::Revised], true);
    }

    public function validate(User $user, ItemRequest $itemRequest): bool
    {
        return $user->can('item-request.validate')
            && $this->inDepartmentScope($user, $itemRequest)
            && $itemRequest->status === ItemRequestStatus::Submitted;
    }

    public function account(User $user, ItemRequest $itemRequest): bool
    {
        return $user->can('item-request.account')
            && $this->inDepartmentScope($user, $itemRequest)
            && $itemRequest->status === ItemRequestStatus::Validated;
    }

    public function reject(User $user, ItemRequest $itemRequest): bool
    {
        return ($this->validate($user, $itemRequest) || $this->account($user, $itemRequest))
            || ($user->can('item-request.approve') && $this->inDepartmentScope($user, $itemRequest) && $itemRequest->status === ItemRequestStatus::Accounted);
    }

    public function revise(User $user, ItemRequest $itemRequest): bool
    {
        return $this->reject($user, $itemRequest);
    }

    public function approve(User $user, ItemRequest $itemRequest): bool
    {
        return $user->can('item-request.approve')
            && $this->inDepartmentScope($user, $itemRequest)
            && $itemRequest->status === ItemRequestStatus::WaitingForIT;
    }

    public function export(User $user, ItemRequest $itemRequest): bool
    {
        return $user->can('item-request.export')
            && $this->inDepartmentScope($user, $itemRequest)
            && $itemRequest->status === ItemRequestStatus::Approved;
    }

    private function inDepartmentScope(User $user, ItemRequest $itemRequest): bool
    {
        return $user->department_id !== null && $user->department_id === $itemRequest->department_id;
    }
}
