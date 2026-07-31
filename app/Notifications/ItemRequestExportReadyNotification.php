<?php

namespace App\Notifications;

use App\Models\ItemRequestExport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ItemRequestExportReadyNotification extends Notification
{
    use Queueable;

    public function __construct(public ItemRequestExport $export) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'export_id' => $this->export->id,
            'item_request_id' => $this->export->item_request_id,
            'message' => sprintf('%s export is ready to download', $this->export->itemRequest->request_no),
            'status' => $this->export->status,
            'url' => route('item-requests.exports.download', $this->export),
        ];
    }
}
