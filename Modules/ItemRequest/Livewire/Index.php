<?php

namespace Modules\ItemRequest\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ItemRequest\Models\ItemRequest;

#[Title('Item Requests')]
class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';

    public string $status = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $this->authorize('viewAny', ItemRequest::class);

        $user = Auth::user();

        $itemRequests = ItemRequest::query()
            ->with(['requester', 'items'])
            ->withCount('items')
            ->when(! $user->hasAnyPermission(['item-request.validate', 'item-request.account', 'item-request.approve']), fn (Builder $query): Builder => $query->where('requester_id', $user->id))
            ->when($user->hasAnyPermission(['item-request.validate', 'item-request.account', 'item-request.approve']), fn (Builder $query): Builder => $query->where('department_id', $user->department_id))
            ->when($this->search !== '', fn (Builder $query): Builder => $query->where(function (Builder $query): void {
                $query->where('request_no', 'ilike', "%{$this->search}%")
                    ->orWhereHas('items', fn (Builder $itemQuery): Builder => $itemQuery->where('item_name', 'ilike', "%{$this->search}%"));
            }))
            ->when($this->status !== '', fn (Builder $query): Builder => $query->where('status', $this->status))
            ->latest()
            ->paginate(10);

        return view('item-request::livewire.index', compact('itemRequests'))->layout('layouts.app');
    }
}
