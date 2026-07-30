<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\ItemRequest\Models\ItemRequest;

class ItemRequestStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ItemRequest $itemRequest,
        public string $message,
        public string $status,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_request_id' => $this->itemRequest->id,
            'request_no' => $this->itemRequest->request_no,
            'item_name' => $this->itemRequest->items->first()?->item_name,
            'status' => $this->status,
            'message' => $this->message,
            'url' => route('item-requests.show', $this->itemRequest),
        ];
    }
}
