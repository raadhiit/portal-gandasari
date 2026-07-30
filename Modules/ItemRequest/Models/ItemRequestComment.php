<?php

namespace Modules\ItemRequest\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $item_request_id
 * @property int $user_id
 */
#[Fillable(['item_request_id', 'user_id', 'body'])]
class ItemRequestComment extends Model
{
    /** @return BelongsTo<ItemRequest, $this> */
    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
