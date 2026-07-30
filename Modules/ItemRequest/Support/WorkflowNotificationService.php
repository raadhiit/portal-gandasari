<?php

namespace Modules\ItemRequest\Support;

use App\Models\User;
use App\Notifications\ItemRequestStatusChangedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;

class WorkflowNotificationService
{
    public function notifySubmitted(ItemRequest $itemRequest): void
    {
        $this->sendToPermission(
            $itemRequest,
            'item-request.validate',
            ItemRequestStatus::Submitted->value,
            sprintf('%s submitted and waiting for validation', $itemRequest->request_no)
        );
    }

    public function notifyValidated(ItemRequest $itemRequest): void
    {
        $this->sendToPermission(
            $itemRequest,
            'item-request.account',
            ItemRequestStatus::Validated->value,
            sprintf('%s validated and waiting for accounting', $itemRequest->request_no)
        );
    }

    public function notifyRevised(ItemRequest $itemRequest): void
    {
        $this->sendToUsers(
            collect([$itemRequest->requester])->filter(),
            $itemRequest,
            ItemRequestStatus::Revised->value,
            sprintf('%s revised, please update and resubmit', $itemRequest->request_no)
        );
    }

    public function notifyRejected(ItemRequest $itemRequest): void
    {
        $this->sendToUsers(
            collect([$itemRequest->requester])->filter(),
            $itemRequest,
            ItemRequestStatus::Rejected->value,
            sprintf('%s rejected', $itemRequest->request_no)
        );
    }

    public function notifyApproved(ItemRequest $itemRequest): void
    {
        $this->sendToUsers(
            collect([$itemRequest->requester])->filter(),
            $itemRequest,
            ItemRequestStatus::Approved->value,
            sprintf('%s approved', $itemRequest->request_no)
        );
    }

    private function sendToPermission(ItemRequest $itemRequest, string $permission, string $status, string $message): void
    {
        $users = User::query()
            ->where('department_id', $itemRequest->department_id)
            ->get()
            ->filter(fn (User $user): bool => $user->can($permission));

        $this->sendToUsers($users, $itemRequest, $status, $message);
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function sendToUsers(Collection $users, ItemRequest $itemRequest, string $status, string $message): void
    {
        if ($users->isEmpty()) {
            return;
        }

        Notification::send($users, new ItemRequestStatusChangedNotification($itemRequest, $message, $status));
    }
}
