@php
    $inboxConfig = match ($mode) {
        'it' => [
            'title' => __('IT inbox'),
            'description' => __('Submit Accurate proof to complete validated requests.'),
            'icon' => 'server-stack',
            'accent' => '#8A6F35',
            'lane' => __('IT approval lane'),
            'action' => __('Submit proof'),
        ],
        default => [
            'title' => __('Validation inbox'),
            'description' => __('Give each submitted request a category and item code before accounting review.'),
            'icon' => 'shield-check',
            'accent' => '#A95C38',
            'lane' => __('Coding lane'),
            'action' => __('Validate item'),
        ],
    };
@endphp

@php
    $statusColors = [
        'draft' => '#2D9CDB',
        'submitted' => '#E28A12',
        'validated' => '#0E9F6E',
        'waiting_for_it' => '#8A6F35',
        'finished' => '#14B8A6',
        'rejected' => '#F43F5E',
        'revised' => '#F97316',
    ];
@endphp

<div class="space-y-8 pb-10">
    <section class="overflow-hidden rounded-[2rem] border border-[#d9cbbb] bg-[linear-gradient(132deg,#f8f1e7_0%,#eee4d8_58%,#e5d8ca_100%)] shadow-[0_18px_60px_rgba(77,57,39,0.08)] dark:border-[#58635f] dark:bg-[linear-gradient(132deg,#1e221f_0%,#26322d_58%,#2b211b_100%)]">
        <div class="grid gap-7 px-6 py-8 md:grid-cols-[1fr_auto] md:items-end md:px-8 lg:px-10 lg:py-10">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-[#cdbca6] bg-white/75 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#6c5a49] dark:border-white/10 dark:bg-white/10 dark:text-[#d8c5af]">
                    <flux:icon :icon="$inboxConfig['icon']" class="size-3.5" />
                    {{ $inboxConfig['lane'] }}
                </div>
                <flux:heading size="xl" level="1" class="mt-4 text-[#1f2a24] dark:text-[#f5ecdf]">{{ $inboxConfig['title'] }}</flux:heading>
                <flux:text class="mt-3 max-w-2xl text-sm leading-6 text-[#5f594f] dark:text-[#d0c4b7]">{{ $inboxConfig['description'] }}</flux:text>
            </div>

            <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-5 py-4 shadow-sm dark:border-white/10 dark:bg-white/10">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 items-center justify-center rounded-full text-white" style="background-color: {{ $inboxConfig['accent'] }}">
                        <flux:icon icon="queue-list" class="size-5" />
                    </div>
                    <div>
                        <div class="text-2xl font-semibold tabular-nums text-[#1f2a24] dark:text-[#f5ecdf]">{{ $itemRequests->total() }}</div>
                        <div class="text-xs text-[#756454] dark:text-[#c6b8aa]">{{ __('requests waiting') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex items-center justify-between gap-4 px-1">
            <div>
                <div class="flex items-center gap-2 text-[11px] uppercase tracking-[0.2em] text-[#8c6d57] dark:text-[#cab49a]">
                    <flux:icon icon="rectangle-stack" class="size-3.5" />
                    {{ __('Work queue') }}
                </div>
                <flux:heading size="sm" class="mt-2 text-[#1f2a24] dark:text-[#f4e7d6]">{{ __('Requests ready for your role') }}</flux:heading>
            </div>
        </div>

        <div class="relative space-y-3">
            <div wire:loading.flex wire:target="nextPage,previousPage,gotoPage" class="absolute inset-0 z-10 items-center justify-center rounded-[1.75rem] bg-[#FAF6F0]/80 backdrop-blur-[1px] dark:bg-[#1B201D]/80">
                <flux:icon icon="arrow-path" class="size-6 animate-spin text-[#8C6D57] dark:text-[#CAB49A]" />
                <span class="sr-only">{{ __('Loading page') }}</span>
            </div>
            @forelse ($itemRequests as $req)
                <article wire:key="inbox-request-{{ $req->id }}" class="group relative overflow-hidden rounded-[1.75rem] border border-[#DCDAD6] bg-white shadow-[0_6px_20px_rgba(0,0,0,0.04)] transition duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] hover:-translate-y-0.5 hover:shadow-[0_12px_28px_rgba(0,0,0,0.08)] dark:border-[#34403C] dark:bg-[#1A1C1A]">
                    <div class="absolute inset-y-0 left-0 w-1.5" style="background-color: {{ $inboxConfig['accent'] }}"></div>
                    <div class="grid gap-5 p-5 pl-7 md:grid-cols-[minmax(0,1.2fr)_minmax(0,0.7fr)_auto] md:items-center md:p-6 md:pl-8">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="font-mono text-xs font-semibold tracking-[0.12em] text-[#8d6f57] dark:text-[#d0b79e]">{{ $req->request_no ?? __('Draft') }}</div>
                                <div class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium text-white shadow-sm" style="background-color: {{ $statusColors[$req->status->value] ?? '#666666' }}">
                                    <flux:icon :icon="$inboxConfig['icon']" class="size-3.5" />
                                    {{ str($req->status->value)->headline() }}
                                </div>
                            </div>
                            <a href="{{ route('item-requests.show', $req) }}" wire:navigate class="mt-3 block text-lg font-semibold tracking-[-0.02em] text-[#17201B] transition duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] group-hover:text-[#3B6478] focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-4 dark:text-[#E6E6E4] dark:group-hover:text-[#6EA3BA]">
                                {{ $req->items->first()?->item_name ?? __('Item request') }}
                            </a>
                            <div class="mt-1 text-xs text-[#685e55] dark:text-[#bfb1a5]">{{ $req->items_count }} {{ \Illuminate\Support\Str::plural('item', $req->items_count) }}</div>
                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-[#685e55] dark:text-[#bfb1a5]">
                                <span class="inline-flex items-center gap-1.5">
                                    <flux:icon icon="user" class="size-4" />
                                    {{ $req->requester->name }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <flux:icon icon="calendar-days" class="size-4" />
                                    {{ $req->created_at?->format('d M Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-[1.35rem] border border-[#DCDAD6] bg-[#F4F6F5] px-4 py-3 dark:border-[#34403C] dark:bg-[#222927]">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#66736D] dark:text-[#9BAAB3]">{{ __('Next responsibility') }}</div>
                            <div class="mt-2 flex items-center gap-2 text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">
                                <flux:icon :icon="$inboxConfig['icon']" class="size-4" />
                                {{ $inboxConfig['action'] }}
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <flux:button :href="route('item-requests.show', $req)" wire:navigate icon="arrow-right" variant="primary">{{ __('Open sheet') }}</flux:button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[2rem] border border-dashed border-[#d4c3b0] bg-[#faf6f0] px-6 py-14 text-center dark:border-white/10 dark:bg-[#1b201d]">
                    <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-[#eee0d1] text-[#976645] dark:bg-[#312923] dark:text-[#d8b596]">
                        <flux:icon icon="inbox" class="size-6" />
                    </div>
                    <flux:heading size="sm" class="mt-5 text-[#1f2a24] dark:text-[#f4e7d6]">{{ __('Queue is clear') }}</flux:heading>
                    <flux:text class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#6b6056] dark:text-[#b9ab9f]">
                        {{ __('No requests currently need action from this role. New work will appear here after the previous lane completes.') }}
                    </flux:text>
                </div>
            @endforelse
        </div>
    </section>

    <div>{{ $itemRequests->links() }}</div>
</div>
