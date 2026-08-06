<?php

namespace Modules\ItemRequest\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;
use Modules\ItemRequest\Models\ItemRequestHistory;

#[Title('Item Requests')]
class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';

    public string $status = 'submitted';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    /** @return array<int, array{key: string, label: string, detail: string, icon: string, state: string}> */
    public function trackingSteps(ItemRequest $itemRequest): array
    {
        $itemRequest->loadMissing('histories.actor');

        $stepDefinitions = [
            [
                'key' => 'draft_created',
                'label' => 'Created',
                'icon' => 'document-plus',
                'history' => fn ($history): bool => $history->action === 'draft_created',
                'complete' => fn (ItemRequest $request): bool => true,
            ],
            [
                'key' => 'submitted',
                'label' => 'Submitted',
                'icon' => 'paper-airplane',
                'history' => fn ($history): bool => $history->action === 'submitted',
                'complete' => fn (ItemRequest $request): bool => in_array($request->status, [ItemRequestStatus::Submitted, ItemRequestStatus::Validated, ItemRequestStatus::WaitingForIT, ItemRequestStatus::Finished], true),
            ],
            [
                'key' => 'validated',
                'label' => 'Validated',
                'icon' => 'shield-check',
                'history' => fn ($history): bool => $history->action === 'validated',
                'complete' => fn (ItemRequest $request): bool => in_array($request->status, [ItemRequestStatus::Validated, ItemRequestStatus::WaitingForIT, ItemRequestStatus::Finished], true),
            ],
            [
                'key' => 'approved',
                'label' => 'Accounting approved',
                'icon' => 'banknotes',
                'history' => fn ($history): bool => $history->action === 'approved' && $history->from_status === 'validated',
                'complete' => fn (ItemRequest $request): bool => in_array($request->status, [ItemRequestStatus::WaitingForIT, ItemRequestStatus::Finished], true),
            ],
            [
                'key' => 'it_approved',
                'label' => 'Finished',
                'icon' => 'sparkles',
                'history' => fn ($history): bool => $history->action === 'approved' && $history->from_status === 'waiting_for_it',
                'complete' => fn (ItemRequest $request): bool => $request->status === ItemRequestStatus::Finished,
            ],
        ];

        return collect($stepDefinitions)->map(function (array $step) use ($itemRequest): array {
            $history = $itemRequest->histories->first($step['history']);
            $isComplete = $step['complete']($itemRequest);
            $isCurrent = ! $isComplete && $this->currentTrackingStepKey($itemRequest) === $step['key'];

            return [
                'key' => $step['key'],
                'label' => $step['label'],
                'icon' => $step['icon'],
                'state' => $isComplete ? 'complete' : ($isCurrent ? 'current' : 'pending'),
                'detail' => $history instanceof ItemRequestHistory
                    ? sprintf('%s · %s', $history->actor?->name ?? 'System', $history->created_at?->format('d M Y H:i') ?? '—')
                    : ($isCurrent ? 'Waiting at this step' : 'Pending'),
            ];
        })->all();
    }

    public function trackingSummary(ItemRequest $itemRequest): string
    {
        return match ($itemRequest->status) {
            ItemRequestStatus::Draft => 'Created',
            ItemRequestStatus::Submitted => 'Waiting for validation',
            ItemRequestStatus::Validated => 'Waiting for accounting approval',
            ItemRequestStatus::WaitingForIT => 'Waiting for IT finalization',
            ItemRequestStatus::Finished => 'Finished',
            ItemRequestStatus::Revised => 'Revised',
            ItemRequestStatus::Rejected => 'Rejected',
            default => str($itemRequest->status->value)->headline()->value(),
        };
    }

    private function currentTrackingStepKey(ItemRequest $itemRequest): string
    {
        return match ($itemRequest->status) {
            ItemRequestStatus::Draft => 'submitted',
            ItemRequestStatus::Submitted => 'validated',
            ItemRequestStatus::Validated => 'approved',
            ItemRequestStatus::WaitingForIT => 'it_approved',
            default => 'approved',
        };
    }

    public function render(): View
    {
        $this->authorize('viewAny', ItemRequest::class);

        $user = Auth::user();

        $itemRequests = ItemRequest::query()
            ->with(['requester', 'items', 'histories.actor'])
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
