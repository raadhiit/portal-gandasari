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

#[Title('IT Inbox')]
class ITInbox extends Component
{
    use AuthorizesRequests, WithPagination;

    public function render(): View
    {
        $user = Auth::user();

        abort_unless($user->can('item-request.approve'), 403);

        $itemRequests = ItemRequest::query()
            ->with(['requester', 'items'])
            ->withCount('items')
            ->where('status', ItemRequestStatus::WaitingForIT)
            ->latest()
            ->paginate(10);

        $mode = 'it';

        return view('item-request::livewire.inbox', compact('itemRequests', 'mode'))->layout('layouts.app');
    }

}
