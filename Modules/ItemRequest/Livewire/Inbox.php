<?php

namespace Modules\ItemRequest\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;

#[Title('Inbox')]
class Inbox extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $mode = 'validation';

    public function mount(): void
    {
        $this->mode = match (true) {
            request()->routeIs('item-requests.accounting-inbox') => 'accounting',
            request()->routeIs('item-requests.approval-inbox') => 'approval',
            default => 'validation',
        };
    }

    public function render(): View
    {
        $user = Auth::user();

        $status = match ($this->mode) {
            'accounting' => ItemRequestStatus::Validated,
            'approval' => ItemRequestStatus::Accounted,
            default => ItemRequestStatus::Submitted,
        };

        $permission = match ($this->mode) {
            'accounting' => 'item-request.account',
            'approval' => 'item-request.approve',
            default => 'item-request.validate',
        };

        abort_unless($user->can($permission), 403);

        $itemRequests = ItemRequest::query()
            ->with(['requester', 'items'])
            ->withCount('items')
            ->where('department_id', $user->department_id)
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return view('item-request::livewire.inbox', compact('itemRequests'));
    }
}
