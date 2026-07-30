<?php

namespace Modules\ItemRequest\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\ItemRequest\Actions\CreateItemRequestAction;
use Modules\ItemRequest\Models\ItemRequest;

#[Title('Create Item Request')]
class Create extends Component
{
    use AuthorizesRequests;

    public string $businessUnitId = '';

    public string $unitId = '';

    public string $warehouseId = '';

    public string $notes = '';

    /** @var array<int, array{item_name: string, item_type: string}> */
    public array $items = [
        ['item_name' => '', 'item_type' => ''],
    ];

    public function mount(): void
    {
        $this->authorize('create', ItemRequest::class);
    }

    public function addItem(): void
    {
        $this->items[] = ['item_name' => '', 'item_type' => ''];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) === 1) {
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(CreateItemRequestAction $action): void
    {
        $validated = $this->validate([
            'businessUnitId' => ['required', 'integer', 'exists:business_units,id'],
            'unitId' => ['required', 'integer', 'exists:units,id'],
            'warehouseId' => ['required', 'integer', 'exists:warehouses,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:150'],
            'items.*.item_type' => ['required', 'string', 'max:100'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $itemRequest = $action->handle($user, [
            'business_unit_id' => (int) $validated['businessUnitId'],
            'unit_id' => (int) $validated['unitId'],
            'warehouse_id' => (int) $validated['warehouseId'],
            'notes' => $validated['notes'],
            'items' => $validated['items'],
        ]);

        $this->redirectRoute('item-requests.show', $itemRequest, navigate: true);
    }

    public function render(): View
    {
        $businessUnits = DB::table('business_units')->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $units = DB::table('units')->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $warehouses = DB::table('warehouses')->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('item-request::livewire.create', compact('businessUnits', 'units', 'warehouses'))->layout('layouts.app');
    }
}
