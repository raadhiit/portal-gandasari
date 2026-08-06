<?php

namespace App\Livewire\ItemRequest;

use App\Jobs\ExportItemRequest;
use App\Models\ItemRequestExport;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;

#[Title('Item Export')]
class ExportIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $filter = 'waiting_for_it';

    public function updatedFilter(): void
    {
        $this->resetPage('readyPage');
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function queueExport(int $itemRequestId): void
    {
        $itemRequest = ItemRequest::query()->findOrFail($itemRequestId);
        $this->authorize('export', $itemRequest);

        $hasActiveExport = $itemRequest->exports()
            ->whereIn('status', ['queued', 'processing'])
            ->exists();

        if ($hasActiveExport) {
            Flux::toast(variant: 'warning', text: 'Export already in progress.');

            return;
        }

        $export = ItemRequestExport::create([
            'item_request_id' => $itemRequest->id,
            'requested_by' => Auth::id(),
            'status' => 'queued',
        ]);

        ExportItemRequest::dispatch($export);

        Flux::toast(variant: 'success', text: 'Export queued.');
    }

    public function render(): View
    {
        abort_unless(Auth::user()->can('item-request.export'), 403);

        $readyRequests = ItemRequest::query()
            ->with(['requester', 'items', 'exports' => fn ($query) => $query->latest()])
            ->withCount('items')
            ->when($this->filter === 'waiting_for_it', fn ($query) => $query->where('status', ItemRequestStatus::WaitingForIT))
            ->when($this->filter === 'finished', fn ($query) => $query->where('status', ItemRequestStatus::Finished))
            ->latest('accounted_at')
            ->paginate(10, pageName: 'readyPage');

        $processingExports = ItemRequestExport::query()
            ->with(['itemRequest.items', 'requester'])
            ->whereIn('status', ['queued', 'processing'])
            ->latest()
            ->get();

        $completedExports = ItemRequestExport::query()
            ->with(['itemRequest.items', 'requester'])
            ->where('status', 'ready')
            ->whereHas('itemRequest', fn ($query) => $query->where('status', ItemRequestStatus::WaitingForIT))
            ->latest('finished_at')
            ->paginate(10, pageName: 'completedPage');

        $failedExports = ItemRequestExport::query()
            ->with(['itemRequest.items', 'requester'])
            ->where('status', 'failed')
            ->whereHas('itemRequest', fn ($query) => $query->where('status', ItemRequestStatus::WaitingForIT))
            ->latest('finished_at')
            ->get();

        return view('livewire.item-request.export-index', compact('readyRequests', 'processingExports', 'completedExports', 'failedExports'))
            ->layout('layouts.app');
    }
}
