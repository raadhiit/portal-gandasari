<?php

namespace Modules\ItemRequest\Livewire;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Modules\ItemRequest\Actions\CreateItemRequestAction;
use Modules\ItemRequest\Actions\SubmitItemRequestAction;
use Modules\ItemRequest\Models\ItemRequest;

#[Title('Create Item Request')]
class Create extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public string $businessUnitId = '';

    public string $notes = '';

    public ?UploadedFile $attachment = null;

    /** @var array<int, array{item_name: string, item_type: string, unit_id: string}> */
    public array $items = [
        ['item_name' => '', 'item_type' => '', 'unit_id' => ''],
    ];

    public function mount(): void
    {
        $this->authorize('create', ItemRequest::class);
    }

    public function addItem(): void
    {
        $this->items[] = ['item_name' => '', 'item_type' => '', 'unit_id' => ''];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) === 1) {
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(CreateItemRequestAction $action, SubmitItemRequestAction $submitItemRequestAction): void
    {
        $validated = $this->validate([
            'businessUnitId' => ['required', 'integer', 'exists:business_units,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,xlsx,docx'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:150'],
            'items.*.item_type' => ['required', 'string', 'max:100'],
            'items.*.unit_id' => ['required', 'integer', 'exists:units,id'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $itemRequest = $action->handle($user, [
            'business_unit_id' => (int) $validated['businessUnitId'],
            'notes' => $validated['notes'],
            'items' => array_map(fn (array $item): array => [
                'item_name' => $item['item_name'],
                'item_type' => $item['item_type'],
                'unit_id' => (int) $item['unit_id'],
            ], $validated['items']),
        ]);

        if ($this->attachment !== null) {
            $path = $this->attachment->store('item-request-attachments', 'public');

            $itemRequest->attachments()->create([
                'uploaded_by' => $user->id,
                'disk' => 'public',
                'path' => $path,
                'original_name' => $this->attachment->getClientOriginalName(),
                'mime_type' => $this->attachment->getMimeType(),
                'size' => $this->attachment->getSize(),
            ]);
        }

        $submitItemRequestAction->handle($user, $itemRequest);

        Flux::toast(variant: 'success', text: 'Item request submitted.');

        $this->redirectRoute('item-requests.index', navigate: true);
    }

    public function render(): View
    {
        $businessUnits = DB::table('business_units')->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $units = DB::table('units')->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('item-request::livewire.create', compact('businessUnits', 'units'))->layout('layouts.app');
    }
}
