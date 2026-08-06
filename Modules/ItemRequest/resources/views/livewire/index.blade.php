@php
    $statusColors = [
        'draft' => '#66736D',
        'submitted' => '#3B6478',
        'validated' => '#3F7A5F',
        'waiting_for_it' => '#8A6F35',
        'finished' => '#327A7A',
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
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by request no or item name') }}" icon="magnifying-glass" class="max-w-sm" />
            <flux:select wire:model.live="status" class="max-w-xs">
                <option value="">-- {{ __('All Status') }} --</option>
                @foreach (\Modules\ItemRequest\Enums\ItemRequestStatus::cases() as $statusOption)
                    <option value="{{ $statusOption->value }}">{{ str($statusOption->value)->headline() }}</option>
                @endforeach
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
                            <div class="flex items-center justify-end gap-2">
                                <flux:modal.trigger name="request-tracking-{{ $req->id }}">
                                    <flux:button variant="ghost" icon="queue-list">{{ __('Track') }}</flux:button>
                                </flux:modal.trigger>
                                <flux:button :href="route('item-requests.show', $req)" wire:navigate variant="ghost" icon="arrow-right">{{ __('View') }}</flux:button>
                            </div>

                            <flux:modal name="request-tracking-{{ $req->id }}" class="max-w-2xl !overflow-visible !border-0 !bg-transparent !p-0 !shadow-none [&::backdrop]:bg-[#EEF3F1]/78 [&::backdrop]:backdrop-blur-[1px] dark:[&::backdrop]:bg-black/92 dark:[&::backdrop]:backdrop-blur-[3px]">
                                <div class="overflow-hidden rounded-[1.75rem] border border-[#C9D7D4] bg-[linear-gradient(180deg,#F8FBFA_0%,#F3F6F5_100%)] shadow-[0_24px_70px_rgba(19,43,41,0.18)] dark:border-[#2E4542] dark:bg-[linear-gradient(180deg,#182120_0%,#121918_100%)]">
                                    <div class="border-b border-[#D8E3E0] bg-[linear-gradient(135deg,#1D4C57_0%,#327A7A_100%)] px-6 py-5 text-white dark:border-[#31504C] dark:bg-[linear-gradient(135deg,#173E46_0%,#255E5C_100%)]">
                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="min-w-0">
                                                <div class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#B9E1DF]">{{ __('Approval tracking') }}</div>
                                                <flux:heading size="lg" class="mt-2 text-white text-left">{{ $req->request_no ?? __('Draft request') }}</flux:heading>
                                                <flux:text class="mt-1.5 text-sm text-[#D9ECEA] text-left">{{ $this->trackingSummary($req) }}</flux:text>
                                            </div>
                                            <div class="inline-flex shrink-0 items-center rounded-full border border-white/20 bg-white/12 px-3 py-1 text-sm font-semibold text-white backdrop-blur-sm">
                                                {{ str($req->status->value)->headline() }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-5 px-6 py-6 dark:text-[#E6E6E4]">
                                        @foreach ($this->trackingSteps($req) as $step)
                                            <div wire:key="tracking-step-{{ $req->id }}-{{ $step['key'] }}" class="grid gap-4 sm:grid-cols-[3.25rem_minmax(0,1fr)]">
                                                <div class="flex flex-col items-center">
                                                    <div @class([
                                                        'flex size-11 items-center justify-center rounded-2xl border shadow-sm transition-colors',
                                                        'border-[#2F8A7D] bg-[#2F8A7D] text-white shadow-[0_10px_24px_rgba(47,138,125,0.22)] dark:border-[#3CA191] dark:bg-[#3CA191]' => $step['state'] === 'complete',
                                                        'border-[#E29B3E] bg-[#FFF1DB] text-[#9B5D12] shadow-[0_10px_24px_rgba(226,155,62,0.18)] dark:border-[#B78138] dark:bg-[#3A2C18] dark:text-[#F1C98E]' => $step['state'] === 'current',
                                                        'border-[#D5DFDC] bg-[#EEF3F1] text-[#8B9490] dark:border-[#34403C] dark:bg-[#202725] dark:text-[#8F9A95]' => $step['state'] === 'pending',
                                                    ])>
                                                        <flux:icon :icon="$step['state'] === 'complete' ? 'check' : $step['icon']" class="size-4" />
                                                    </div>
                                                    @if (! $loop->last)
                                                        <div @class([
                                                            'mt-2 h-full min-h-10 w-px',
                                                            'bg-gradient-to-b from-[#69B4A8] to-[#D8E3E0] dark:from-[#3CA191] dark:to-[#34403C]' => $step['state'] === 'complete',
                                                            'bg-gradient-to-b from-[#E1B169] to-[#D8E3E0] dark:from-[#B78138] dark:to-[#34403C]' => $step['state'] === 'current',
                                                            'bg-[#D8E3E0] dark:bg-[#34403C]' => $step['state'] === 'pending',
                                                        ])></div>
                                                    @endif
                                                </div>
                                                <div @class([
                                                    'min-w-0 rounded-[1.45rem] border px-5 py-5 shadow-[0_8px_22px_rgba(12,29,28,0.05)]',
                                                    'border-[#CFE7E2] bg-[linear-gradient(180deg,#F6FCFB_0%,#EDF7F4_100%)] dark:border-[#21423D] dark:bg-[linear-gradient(180deg,#162322_0%,#14201F_100%)]' => $step['state'] === 'complete',
                                                    'border-[#F0D3A2] bg-[linear-gradient(180deg,#FFF8ED_0%,#FFF1DB_100%)] dark:border-[#5B4120] dark:bg-[linear-gradient(180deg,#2B2117_0%,#241B13_100%)]' => $step['state'] === 'current',
                                                    'border-[#DCE5E3] bg-white/80 dark:border-[#2D3735] dark:bg-[#1A1F1E]' => $step['state'] === 'pending',
                                                ])>
                                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                                        <div class="min-w-0 text-left">
                                                            <div class="text-lg font-semibold tracking-[-0.02em] text-[#17201B] dark:text-[#E6E6E4]">{{ __($step['label']) }}</div>
                                                            <div class="mt-2 text-sm leading-6 text-[#66736D] dark:text-[#9BAAB3]">{{ __($step['detail']) }}</div>
                                                        </div>
                                                        <span @class([
                                                            'inline-flex shrink-0 rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em]',
                                                            'bg-[#DDF3EE] text-[#24685D] dark:bg-[#17352F] dark:text-[#8BD1C2]' => $step['state'] === 'complete',
                                                            'bg-[#FFE8C6] text-[#9A5A0D] dark:bg-[#3C2C16] dark:text-[#EDC485]' => $step['state'] === 'current',
                                                            'bg-[#EEF2F0] text-[#7B8782] dark:bg-[#252C2A] dark:text-[#94A09B]' => $step['state'] === 'pending',
                                                        ])>
                                                            {{ __($step['state']) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="flex justify-end border-t border-[#DCE5E3] bg-[#F7FAF9] px-6 py-4 dark:border-[#2D3735] dark:bg-[#171C1B]">
                                        <flux:modal.close>
                                            <flux:button variant="ghost">{{ __('Close') }}</flux:button>
                                        </flux:modal.close>
                                    </div>
                                </div>
                            </flux:modal>
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
