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
            ->where('status', ItemRequestStatus::Approved)
            ->whereDoesntHave('exports', fn ($query) => $query->whereIn('status', ['queued', 'processing', 'ready']))
            ->latest('approved_at')
            ->paginate(10, pageName: 'readyPage');

        $processingExports = ItemRequestExport::query()
            ->with(['itemRequest.items', 'requester'])
            ->whereIn('status', ['queued', 'processing'])
            ->latest()
            ->get();

        $completedExports = ItemRequestExport::query()
            ->with(['itemRequest.items', 'requester'])
            ->where('status', 'ready')
            ->latest('finished_at')
            ->paginate(10, pageName: 'completedPage');

        $failedExports = ItemRequestExport::query()
            ->with(['itemRequest.items', 'requester'])
            ->where('status', 'failed')
            ->latest('finished_at')
            ->get();

        return view('livewire.item-request.export-index', compact('readyRequests', 'processingExports', 'completedExports', 'failedExports'))
            ->layout('layouts.app');
    }
}
