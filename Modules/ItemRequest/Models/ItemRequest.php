<?php

namespace Modules\ItemRequest\Models;

use App\Models\ItemRequestExport;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ItemRequest\Enums\ItemRequestStatus;

/**
 * @property int $id
 * @property string|null $request_no
 * @property int $requester_id
 * @property ItemRequestStatus $status
 * @property string $current_step
 */
#[Fillable([
    'request_no',
    'requester_id',
    'department_id',
    'business_unit_id',
    'assigned_validator_id',
    'assigned_accounting_id',
    'status',
    'current_step',
    'revision_count',
    'notes',
    'last_remarks',
    'submitted_at',
    'validated_at',
    'accounted_at',
    'approved_at',
    'rejected_at',
    'cancelled_at',
])]
class ItemRequest extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ItemRequestStatus::class,
            'submitted_at' => 'datetime',
            'validated_at' => 'datetime',
            'accounted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /** @return BelongsTo<User, $this> */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_validator_id');
    }

    /** @return BelongsTo<User, $this> */
    public function accounting(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_accounting_id');
    }

    /** @return HasMany<ItemRequestHistory, $this> */
    public function histories(): HasMany
    {
        return $this->hasMany(ItemRequestHistory::class);
    }

    /** @return HasMany<ItemRequestComment, $this> */
    public function comments(): HasMany
    {
        return $this->hasMany(ItemRequestComment::class);
    }

    /** @return HasMany<ItemRequestAttachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(ItemRequestAttachment::class);
    }

    /** @return HasMany<ItemRequestItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(ItemRequestItem::class);
    }

    /** @return HasMany<ItemRequestExport, $this> */
    public function exports(): HasMany
    {
        return $this->hasMany(ItemRequestExport::class);
    }
}
