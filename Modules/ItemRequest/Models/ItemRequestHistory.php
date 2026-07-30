<?php

namespace Modules\ItemRequest\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $item_request_id
 * @property string $action
 */
#[Fillable(['item_request_id', 'actor_id', 'action', 'from_status', 'to_status', 'remarks', 'snapshot'])]
class ItemRequestHistory extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<ItemRequest, $this> */
    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
