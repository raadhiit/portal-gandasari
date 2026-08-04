<?php

namespace Modules\ItemRequest\Livewire;

use App\Models\Item;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\ItemRequest\Actions\AccountItemRequestAction;
use Modules\ItemRequest\Actions\SubmitItemRequestAction;
use Modules\ItemRequest\Actions\ValidateItemRequestAction;
use Modules\ItemRequest\Models\ItemRequest;

#[Title('Item Request Detail')]
class Show extends Component
{
    use AuthorizesRequests;

    public ItemRequest $itemRequest;

    /** @var array<int, array{id: int, category_id: string, item_code: string, inventory_coa_id: string, sales_coa_id: string}> */
    public array $items = [];

    public function mount(ItemRequest $itemRequest): void
    {
        $this->authorize('view', $itemRequest);
        $this->itemRequest = $itemRequest->load('items');
        $this->items = $this->itemRequest->items->map(fn ($item): array => [
            'id' => $item->id,
            'category_id' => (string) ($item->category_id ?? ''),
            'item_code' => (string) ($item->item_code ?? ''),
            'inventory_coa_id' => (string) ($item->inventory_coa_id ?? ''),
            'sales_coa_id' => (string) ($item->sales_coa_id ?? ''),
        ])->all();
    }

    public function validateRequest(ValidateItemRequestAction $action): void
    {
        $this->authorize('validate', $this->itemRequest);

        $validated = $this->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:item_request_items,id'],
            'items.*.category_id' => ['required', 'integer', 'exists:item_categories,id'],
            'items.*.item_code' => ['required', 'string', 'max:50', 'distinct'],
        ]);

        foreach ($validated['items'] as $index => $item) {
            $itemCode = trim((string) $item['item_code']);
            $itemId = (int) $item['id'];

            validator(
                ['item_code' => $itemCode],
                [
                    'item_code' => [
                        'required',
                        'string',
                        'max:50',
                        Rule::unique(Item::class, 'item_code'),
                        Rule::unique('item_request_items', 'item_code')->ignore($itemId),
                    ],
                ],
                [
                    'item_code.unique' => __('Item code has already been used.'),
                ],
            )->validate();

            $validated['items'][$index]['item_code'] = $itemCode;
        }

        /** @var User $user */
        $user = Auth::user();
        $this->itemRequest = $action->handle($user, $this->itemRequest, array_map(
            fn (array $item): array => [
                'id' => (int) $item['id'],
                'category_id' => (int) $item['category_id'],
                'item_code' => $item['item_code'],
            ],
            $validated['items'],
        ));

        Flux::toast(variant: 'success', text: 'Item request validated.');

        $this->redirectRoute('item-requests.validation-inbox');
    }

    public function accountRequest(AccountItemRequestAction $action): void
    {
        $this->authorize('account', $this->itemRequest);

        $validated = $this->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:item_request_items,id'],
            'items.*.inventory_coa_id' => ['required', 'integer', 'exists:coas,id'],
            'items.*.sales_coa_id' => ['required', 'integer', 'exists:coas,id'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $this->itemRequest = $action->handle($user, $this->itemRequest, array_map(
            fn (array $item): array => [
                'id' => (int) $item['id'],
                'inventory_coa_id' => (int) $item['inventory_coa_id'],
                'sales_coa_id' => (int) $item['sales_coa_id'],
            ],
            $validated['items'],
        ));

        Flux::toast(variant: 'success', text: 'Item request approved.');

        $this->redirectRoute('item-requests.index');
    }

    public function submit(SubmitItemRequestAction $action): void
    {
        $this->authorize('submit', $this->itemRequest);

        /** @var User $user */
        $user = Auth::user();
        $this->itemRequest = $action->handle($user, $this->itemRequest);

        Flux::toast(variant: 'success', text: 'Item request submitted.');

        $this->redirectRoute('item-requests.index');
    }

    public function render(): View
    {
        $this->itemRequest->load(['requester', 'histories.actor', 'attachments', 'items']);

        return view('item-request::livewire.show')->layout('layouts.app');
    }
}
