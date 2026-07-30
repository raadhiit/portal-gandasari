<?php

namespace Modules\ItemRequest\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $item_request_id
 * @property string $item_name
 * @property string $item_type
 * @property string|null $item_code
 */
#[Fillable(['item_request_id', 'category_id', 'created_item_id', 'item_name', 'item_type', 'item_code'])]
class ItemRequestItem extends Model
{
    /** @return BelongsTo<ItemRequest, $this> */
    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }
}
