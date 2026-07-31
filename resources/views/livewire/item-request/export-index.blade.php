<div class="space-y-7 pb-12">
    <header class="overflow-hidden rounded-[1.75rem] bg-[#173F4A] text-white shadow-[0_18px_50px_rgba(23,63,74,0.18)] dark:bg-[#15343C]">
        <div class="grid gap-8 px-6 py-7 lg:grid-cols-[1fr_auto] lg:items-end lg:px-8">
            <div class="max-w-2xl">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#A9D3D8]">
                    <flux:icon icon="arrow-down-tray" class="size-4" />
                    {{ __('Accounting export desk') }}
                </div>
                <flux:heading size="xl" level="1" class="mt-3 text-white">{{ __('Item export queue') }}</flux:heading>
                <p class="mt-2 max-w-xl text-sm leading-6 text-[#C9DEE1]">{{ __('Prepare approved item requests for Excel, track active jobs, and retrieve completed files.') }}</p>
            </div>

            <div class="grid grid-cols-3 divide-x divide-white/15 rounded-xl bg-white/8 ring-1 ring-white/12">
                <div class="min-w-24 px-5 py-4">
                    <div class="font-mono text-2xl font-semibold tabular-nums">{{ $readyRequests->total() }}</div>
                    <div class="mt-1 text-xs text-[#C9DEE1]">{{ __('Waiting') }}</div>
                </div>
                <div class="min-w-24 px-5 py-4">
                    <div class="font-mono text-2xl font-semibold tabular-nums">{{ $processingExports->count() }}</div>
                    <div class="mt-1 text-xs text-[#C9DEE1]">{{ __('Active') }}</div>
                </div>
                <div class="min-w-24 px-5 py-4">
                    <div class="font-mono text-2xl font-semibold tabular-nums {{ $failedExports->isNotEmpty() ? 'text-[#FFB4A7]' : '' }}">{{ $failedExports->count() }}</div>
                    <div class="mt-1 text-xs text-[#C9DEE1]">{{ __('Failed') }}</div>
                </div>
            </div>
        </div>
    </header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_21rem]">
        <section aria-labelledby="ready-export-heading" class="overflow-hidden rounded-2xl bg-white shadow-[0_12px_35px_rgba(25,54,60,0.08)] ring-1 ring-[#D4DEDC] dark:bg-[#1A1C1A] dark:ring-[#34403C]">
            <div class="flex flex-col gap-3 border-b border-[#DCE5E3] px-5 py-5 sm:flex-row sm:items-end sm:justify-between dark:border-[#34403C]">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="h-6 w-1 rounded-full bg-[#D9673F]"></span>
                        <flux:heading id="ready-export-heading" size="lg">{{ __('Ready to export') }}</flux:heading>
                    </div>
                    <p class="mt-2 pl-4 text-sm text-[#66736D] dark:text-[#9BAAB3]">{{ __('Approved requests requiring action, oldest approval first.') }}</p>
                </div>
                <div class="pl-4 font-mono text-xs font-semibold uppercase tracking-[0.12em] text-[#66736D] sm:pl-0 dark:text-[#9BAAB3]">
                    {{ $readyRequests->total() }} {{ trans_choice('request|requests', $readyRequests->total()) }}
                </div>
            </div>

            <div class="divide-y divide-[#E3E9E7] dark:divide-[#303936]">
                @forelse ($readyRequests as $request)
                    <article wire:key="ready-export-{{ $request->id }}" class="group grid gap-4 px-5 py-5 transition-colors duration-200 hover:bg-[#F2F7F6] sm:grid-cols-[3rem_minmax(0,1fr)_auto] sm:items-center dark:hover:bg-[#202725]">
                        <div class="hidden size-10 items-center justify-center rounded-lg bg-[#E6F0EF] font-mono text-xs font-semibold tabular-nums text-[#275E68] sm:flex dark:bg-[#233633] dark:text-[#8EC3C7]">
                            {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="font-mono text-xs font-semibold tracking-[0.08em] text-[#275E68] dark:text-[#8EC3C7]">{{ $request->request_no }}</span>
                                <span class="text-xs text-[#7B8782] dark:text-[#8C9994]">{{ $request->approved_at?->format('d M Y, H:i') }}</span>
                            </div>
                            <h3 class="mt-1.5 truncate text-base font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ $request->items->first()?->item_name ?? __('Item request') }}</h3>
                            <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-[#66736D] dark:text-[#9BAAB3]">
                                <span>{{ trans_choice(':count item|:count items', $request->items_count, ['count' => $request->items_count]) }}</span>
                                <span>{{ $request->requester?->name }}</span>
                            </div>
                        </div>

                        <flux:button wire:click="queueExport({{ $request->id }})" wire:loading.attr="disabled" wire:target="queueExport({{ $request->id }})" variant="primary" icon="arrow-down-tray" class="w-full bg-[#D9673F] hover:bg-[#C85B36] sm:w-auto">
                            <span wire:loading.remove wire:target="queueExport({{ $request->id }})">{{ __('Queue export') }}</span>
                            <span wire:loading wire:target="queueExport({{ $request->id }})">{{ __('Queuing…') }}</span>
                        </flux:button>
                    </article>
                @empty
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto flex size-11 items-center justify-center rounded-xl bg-[#E6F0EF] text-[#275E68] dark:bg-[#233633] dark:text-[#8EC3C7]">
                            <flux:icon icon="check" class="size-5" />
                        </div>
                        <div class="mt-4 font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ __('Queue is clear') }}</div>
                        <p class="mt-1 text-sm text-[#66736D] dark:text-[#9BAAB3]">{{ __('Approved requests will appear here when ready.') }}</p>
                    </div>
                @endforelse
            </div>
            <div class="px-5 py-4 border-t border-[#E3E9E7] dark:border-[#303936]">
                {{ $readyRequests->links() }}
            </div>
        </section>

        <aside class="space-y-5">
            <section aria-labelledby="active-export-heading" class="rounded-2xl bg-[#F3F6F5] p-5 ring-1 ring-[#D4DEDC] dark:bg-[#1A211F] dark:ring-[#34403C]">
                <div class="flex items-center justify-between gap-3">
                    <flux:heading id="active-export-heading" size="sm">{{ __('Active jobs') }}</flux:heading>
                    <span class="font-mono text-xs font-semibold tabular-nums text-[#66736D] dark:text-[#9BAAB3]">{{ $processingExports->count() }}</span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($processingExports as $export)
                        <div wire:key="processing-export-{{ $export->id }}" class="rounded-xl border-l-4 border-[#C58A35] bg-white px-4 py-3 dark:bg-[#202725]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate font-mono text-xs font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ $export->itemRequest->request_no }}</div>
                                    <div class="mt-1 text-xs text-[#66736D] dark:text-[#9BAAB3]">{{ $export->requester?->name }}</div>
                                </div>
                                <span class="shrink-0 text-xs font-semibold text-[#92631F] dark:text-[#E0B66E]">{{ str($export->status)->headline() }}</span>
                            </div>
                            <div class="mt-3 h-1 overflow-hidden rounded-full bg-[#E9E3D8] dark:bg-[#423925]">
                                <div class="h-full w-2/3 rounded-full bg-[#C58A35]"></div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-[#CBD6D3] px-4 py-7 text-center text-sm text-[#66736D] dark:border-[#34403C] dark:text-[#9BAAB3]">{{ __('No exports are running.') }}</p>
                    @endforelse
                </div>
            </section>

            @if ($failedExports->isNotEmpty())
                <section aria-labelledby="failed-export-heading" class="rounded-2xl bg-[#FBF3F1] p-5 ring-1 ring-[#E7CBC4] dark:bg-[#271C1A] dark:ring-[#51322C]">
                    <div class="flex items-center justify-between gap-3">
                        <flux:heading id="failed-export-heading" size="sm" class="text-[#8C3F31] dark:text-[#E6A397]">{{ __('Needs attention') }}</flux:heading>
                        <span class="font-mono text-xs font-semibold text-[#A45151]">{{ $failedExports->count() }}</span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @foreach ($failedExports as $export)
                            <div wire:key="failed-export-{{ $export->id }}" class="border-l-2 border-[#B85C4B] pl-3">
                                <div class="font-mono text-xs font-semibold text-[#6F3026] dark:text-[#F0B8AE]">{{ $export->itemRequest->request_no }}</div>
                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-[#955548] dark:text-[#D59286]">{{ $export->error_message }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </aside>
    </div>

    <section aria-labelledby="completed-export-heading" class="overflow-hidden rounded-2xl border border-[#D4DEDC] bg-[#F8FAF9] dark:border-[#34403C] dark:bg-[#181D1B]">
        <div class="flex items-center justify-between gap-4 border-b border-[#DCE5E3] px-5 py-4 dark:border-[#34403C]">
            <div>
                <flux:heading id="completed-export-heading" size="sm">{{ __('Recent files') }}</flux:heading>
                <p class="mt-1 text-xs text-[#66736D] dark:text-[#9BAAB3]">{{ __('Completed exports.') }}</p>
            </div>
            <span class="font-mono text-xs font-semibold tabular-nums text-[#66736D] dark:text-[#9BAAB3]">{{ $completedExports->total() }}</span>
        </div>

        <div class="divide-y divide-[#E3E9E7] dark:divide-[#303936]">
            @forelse ($completedExports as $export)
                <div wire:key="completed-export-{{ $export->id }}" class="grid gap-3 px-5 py-4 sm:grid-cols-[minmax(0,1fr)_auto_auto] sm:items-center">
                    <div class="min-w-0">
                        <div class="truncate font-mono text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ $export->itemRequest->request_no }}</div>
                        <div class="mt-1 text-xs text-[#66736D] dark:text-[#9BAAB3]">{{ $export->requester?->name }}</div>
                    </div>
                    <time class="text-xs tabular-nums text-[#66736D] dark:text-[#9BAAB3]">{{ $export->finished_at?->format('d M Y · H:i') }}</time>
                    <flux:button :href="route('item-requests.exports.download', $export)" size="sm" icon="arrow-down-tray">{{ __('Download') }}</flux:button>
                </div>
            @empty
                <p class="px-6 py-10 text-center text-sm text-[#66736D] dark:text-[#9BAAB3]">{{ __('Completed files will be listed here.') }}</p>
            @endforelse
        </div>

        @if ($completedExports->hasPages())
            <div class="border-t border-[#DCE5E3] px-5 py-4 dark:border-[#34403C]">
                {{ $completedExports->links() }}
            </div>
        @endif
    </section>
</div>
