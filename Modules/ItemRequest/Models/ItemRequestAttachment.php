<?php

namespace Modules\ItemRequest\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $item_request_id
 */
#[Fillable(['item_request_id', 'uploaded_by', 'disk', 'path', 'original_name', 'mime_type', 'size'])]
class ItemRequestAttachment extends Model
{
    /** @return BelongsTo<ItemRequest, $this> */
    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    /** @return BelongsTo<User, $this> */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
