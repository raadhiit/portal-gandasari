<?php

namespace Modules\ItemRequest\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Modules\ItemRequest\Models\ItemRequest;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'item_request_id',
    'proof_content',
    'actor_id',
])]
class ItemRequestProof extends Model
{
    protected function casts(): array
    {
        return [
            'proof_content' => 'string',
        ];
    }
    
    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
