<?php

namespace Modules\ItemRequest\Livewire;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Modules\ItemRequest\Actions\AccountItemRequestAction;
use Modules\ItemRequest\Actions\ApproveItemRequestAction;
use Modules\ItemRequest\Actions\SubmitItemRequestAction;
use Modules\ItemRequest\Actions\ValidateItemRequestAction;
use Modules\ItemRequest\Models\ItemRequest;

#[Title('Item Request Detail')]
class Show extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public ItemRequest $itemRequest;

    public string $commentBody = '';

    public ?UploadedFile $attachment = null;

    /** @var array<int, array{id: int, category_id: string, item_code: string}> */
    public array $items = [];

    public ?int $coaId = null;

    public function mount(ItemRequest $itemRequest): void
    {
        $this->authorize('view', $itemRequest);
        $this->itemRequest = $itemRequest->load('items');
        $this->items = $this->itemRequest->items->map(fn ($item): array => [
            'id' => $item->id,
            'category_id' => (string) ($item->category_id ?? ''),
            'item_code' => (string) ($item->item_code ?? ''),
        ])->all();
        $this->coaId = $itemRequest->coa_id;
    }

    public function validateRequest(ValidateItemRequestAction $action): void
    {
        $this->authorize('validate', $this->itemRequest);

        $validated = $this->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:item_request_items,id'],
            'items.*.category_id' => ['required', 'integer', 'exists:item_categories,id'],
            'items.*.item_code' => ['required', 'string', 'max:50'],
        ]);

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
            'coaId' => ['required', 'exists:coas,id'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $this->itemRequest = $action->handle($user, $this->itemRequest, $validated['coaId']);

        Flux::toast(variant: 'success', text: 'Item request accounted.');

        $this->redirectRoute('item-requests.accounting-inbox');
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

    public function approve(ApproveItemRequestAction $action): void
    {
        $this->authorize('approve', $this->itemRequest);

        /** @var User $user */
        $user = Auth::user();
        $this->itemRequest = $action->handle($user, $this->itemRequest);

        Flux::toast(variant: 'success', text: 'Item request approved.');

        $this->redirectRoute('item-requests.index');
    }

    public function addComment(): void
    {
        abort_unless(Auth::user()->can('item-request.comment'), 403);

        $validated = $this->validate([
            'commentBody' => ['required', 'string', 'max:1000'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $this->itemRequest->comments()->create([
            'user_id' => $user->id,
            'body' => $validated['commentBody'],
        ]);

        $this->commentBody = '';
        Flux::toast(variant: 'success', text: 'Comment added.');
    }

    public function uploadAttachment(): void
    {
        abort_unless(Auth::user()->can('item-request.attach'), 403);

        $validated = $this->validate([
            'attachment' => ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,xlsx,docx'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        /** @var UploadedFile $attachment */
        $attachment = $validated['attachment'];

        $path = $attachment->store('item-request-attachments', 'public');

        $this->itemRequest->attachments()->create([
            'uploaded_by' => $user->id,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $attachment->getClientOriginalName(),
            'mime_type' => $attachment->getMimeType(),
            'size' => $attachment->getSize(),
        ]);

        $this->attachment = null;
        Flux::toast(variant: 'success', text: 'Attachment uploaded.');
    }

    public function render(): View
    {
        $this->itemRequest->load(['requester', 'histories.actor', 'comments.user', 'attachments', 'items']);

        return view('item-request::livewire.show')->layout('layouts.app');
    }
}
