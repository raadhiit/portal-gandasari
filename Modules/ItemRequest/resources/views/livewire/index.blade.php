@php
    $statusColors = [
        'draft' => '#66736D',
        'submitted' => '#3B6478',
        'validated' => '#3F7A5F',
        'accounted' => '#3B6478',
        'approved' => '#327A7A',
        'rejected' => '#A45151',
        'revised' => '#8A6F35',
    ];
@endphp

<div class="space-y-8 pb-10">
    <div class="flex flex-col gap-2">
        <flux:heading size="xl" level="1">{{ __('Item requests') }}</flux:heading>
        <flux:text class="text-[#66736D] dark:text-[#9BAAB3]">{{ __('Track item setup requests from draft to approval.') }}</flux:text>
    </div>

    <div class="flex items-center justify-between gap-4 rounded-2xl border border-[#DCDAD6] bg-white p-4 shadow-sm dark:border-[#383838] dark:bg-[#1A1C1A]">
        <div class="flex flex-1 items-center gap-4">
            <flux:input wire:model.debounce.300ms="search" placeholder="{{ __('Search by request no or item name') }}" icon="magnifying-glass" class="max-w-sm" />
            <flux:select wire:model.change="status" class="max-w-xs">
                <option value="">-- {{ __('All Status') }} --</option>
                <option value="draft">Draft</option>
                <option value="submitted">Submitted</option>
                <option value="validated">Validated</option>
                <option value="accounted">Accounted</option>
                <option value="revised">Revised</option>
                <option value="rejected">Rejected</option>
            </flux:select>
        </div>
        <flux:button :href="route('item-requests.create')" wire:navigate icon="plus" variant="primary">{{ __('New Request') }}</flux:button>
    </div>

    <div class="relative overflow-hidden rounded-2xl border border-[#DCDAD6] bg-white shadow-sm dark:border-[#383838] dark:bg-[#1A1C1A]">
        <div wire:loading.flex wire:target="nextPage,previousPage,gotoPage" class="absolute inset-0 z-10 items-center justify-center bg-white/75 backdrop-blur-[1px] dark:bg-[#1A1C1A]/75">
            <flux:icon icon="arrow-path" class="size-6 animate-spin text-[#3B6478]" />
            <span class="sr-only">{{ __('Loading page') }}</span>
        </div>
        <table class="min-w-full divide-y divide-[#DCDAD6] dark:divide-[#383838]">
            <thead class="bg-[#F4F6F5] dark:bg-[#121615]">
                <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-[#66736D] dark:text-[#9BAAB3]">
                    <th class="px-6 py-4">{{ __('Request No') }}</th>
                    <th class="px-4 py-4">{{ __('Items') }}</th>
                    <th class="px-4 py-4">{{ __('Requester') }}</th>
                    <th class="px-4 py-4">{{ __('Status') }}</th>
                    <th class="px-4 py-4">{{ __('Created') }}</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#DCDAD6] dark:divide-[#383838]">
                @forelse ($itemRequests as $req)
                    @php
                        $firstItem = $req->items->first();
                    @endphp
                    <tr wire:key="request-{{ $req->id }}" class="group text-sm transition hover:bg-[#F9FAFB] dark:hover:bg-[#222624]">
                        <td class="px-6 py-4 font-mono font-semibold tracking-wider text-[#17201B] dark:text-[#E6E6E4]">{{ $req->request_no ?? __('Draft') }}</td>
                        <td class="px-4 py-4 text-[#17201B] dark:text-[#E6E6E4]">
                            <div class="font-semibold">{{ $firstItem?->item_name ?? '—' }}</div>
                            <div class="mt-1 text-xs text-[#66736D] dark:text-[#9BAAB3]">
                                {{ $req->items_count }} {{ \Illuminate\Support\Str::plural('item', $req->items_count) }}
                            </div>
                        </td>
                        <td class="px-4 py-4 text-[#66736D] dark:text-[#9BAAB3]">{{ $req->requester->name }}</td>
                        <td class="px-4 py-4">
                            <flux:badge variant="pill" style="background-color: {{ $statusColors[$req->status->value] ?? '#666' }}; color: white;">
                                {{ str($req->status->value)->headline() }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-4 text-[#66736D] dark:text-[#9BAAB3]">{{ $req->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <flux:button :href="route('item-requests.show', $req)" wire:navigate variant="ghost" icon="arrow-right">{{ __('View') }}</flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-[#66736D] dark:text-[#9BAAB3]">
                            <flux:icon icon="clipboard-document-list" class="mx-auto size-8 opacity-50" />
                            <div class="mt-4">{{ __('No item requests found.') }}</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $itemRequests->links() }}</div>
</div>
