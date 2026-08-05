@php
    $statusConfig = [
        'draft' => ['label' => 'Draft', 'icon' => 'document-text', 'tone' => '#2D9CDB'],
        'submitted' => ['label' => 'Submitted', 'icon' => 'paper-airplane', 'tone' => '#E28A12'],
        'validated' => ['label' => 'Validated', 'icon' => 'shield-check', 'tone' => '#0E9F6E'],
        'accounted' => ['label' => 'Accounted', 'icon' => 'banknotes', 'tone' => '#3B82F6'],
        'approved' => ['label' => 'Approved', 'icon' => 'check-badge', 'tone' => '#14B8A6'],
        'rejected' => ['label' => 'Rejected', 'icon' => 'x-circle', 'tone' => '#F43F5E'],
        'revised' => ['label' => 'Revised', 'icon' => 'arrow-path', 'tone' => '#F97316'],
    ];

    $current = $statusConfig[$itemRequest->status->value] ?? ['label' => ucfirst($itemRequest->status->value), 'icon' => 'ellipsis-horizontal', 'tone' => '#2D9CDB'];

    $steps = [
        ['key' => 'draft', 'label' => 'Draft', 'icon' => 'document-text'],
        ['key' => 'submitted', 'label' => 'Validation', 'icon' => 'shield-check'],
        ['key' => 'validated', 'label' => 'Accounting', 'icon' => 'banknotes'],
        ['key' => 'accounted', 'label' => 'Approval', 'icon' => 'check-badge'],
        ['key' => 'waiting_for_it', 'label' => 'IT Lane', 'icon' => 'server-stack'],
        ['key' => 'approved', 'label' => 'Done', 'icon' => 'sparkles'],
    ];

    $currentStepIdx = array_search(match ($itemRequest->status->value) {
        'draft' => 'draft',
        'submitted', 'revised' => 'submitted',
        'validated' => 'validated',
        'accounted' => 'accounted',
        'waiting_for_it' => 'waiting_for_it',
        'approved' => 'approved',
        'rejected' => 'submitted',
        default => 'draft',
    }, array_column($steps, 'key'), true);
@endphp

<div class="space-y-8 pb-10">
    <section class="rounded-2xl border border-[#DCDAD6] bg-white shadow-[0_4px_24px_rgba(0,0,0,0.03)] dark:border-[#34403C] dark:bg-[#1A1C1A]">
        <div class="overflow-hidden rounded-2xl">
            <div class="border-b border-[#DCDAD6] bg-[#F4F6F5] px-6 py-3 dark:border-[#34403C] dark:bg-[#121615]">
                <flux:breadcrumbs>
                    <flux:breadcrumbs.item :href="route('item-requests.index')" wire:navigate>{{ __('Item requests') }}</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item>{{ $itemRequest->request_no ?? __('Draft') }}</flux:breadcrumbs.item>
                </flux:breadcrumbs>
            </div>

            <div class="grid gap-0 lg:grid-cols-[1.35fr_0.75fr]">
                <div class="space-y-8 border-r border-[#DCDAD6] px-6 py-8 dark:border-[#34403C] lg:px-8">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-2 rounded-full bg-[#EEF2F0] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-[#66736D] dark:bg-[#222927] dark:text-[#9BAAB3]">
                                <flux:icon icon="archive-box" class="size-3.5" />
                                {{ __('Warehouse intake') }}
                            </div>
                            <flux:heading size="xl" level="1" class="mt-3 text-[#17201B] dark:text-[#E6E6E4]">{{ $itemRequest->items->first()?->item_name ?? __('Item request') }}</flux:heading>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            @can('export', $itemRequest)
                                <flux:button :href="route('item-requests.exports.index')" icon="arrow-down-tray">{{ __('Export Excel') }}</flux:button>
                            @endcan
                            <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold text-white shadow-sm" style="background-color: {{ $current['tone'] }}">
                                <flux:icon :icon="$current['icon']" class="size-4" />
                                {{ $current['label'] }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#66736D] dark:text-[#9BAAB3]">
                            <flux:icon icon="bolt" class="size-3.5" />
                            {{ __('Actions') }}
                        </div>

                        @can('submit', $itemRequest)
                            <div class="rounded-xl border border-[#DCDAD6] border-l-4 border-l-[#3B6478] bg-[#F7FAFB] p-5 shadow-[0_4px_14px_rgba(0,0,0,0.04)] dark:border-[#34403C] dark:border-l-[#6EA3BA] dark:bg-[#1E2427]">
                                <div class="flex items-start gap-4">
                                    <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-white text-[#3B6478] shadow-sm dark:bg-[#2A3439] dark:text-[#6EA3BA]">
                                        <flux:icon icon="paper-airplane" class="size-5" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ __('Submit draft to validator') }}</div>
                                        <div class="mt-1 text-sm leading-6 text-[#66736D] dark:text-[#9BAAB3]">{{ __('Mark this request ready for item coding when all fields are complete.') }}</div>
                                        <div class="mt-4 flex justify-end">
                                            <flux:button wire:click="submit" wire:loading.attr="disabled" variant="primary" icon="paper-airplane">{{ __('Submit request') }}</flux:button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan

                        @can('validate', $itemRequest)
                            <form wire:submit="validateRequest" class="rounded-xl border border-[#DCDAD6] border-l-4 border-l-[#3F7A5F] bg-[#F6FAF8] p-5 shadow-[0_4px_14px_rgba(0,0,0,0.04)] dark:border-[#34403C] dark:border-l-[#75B892] dark:bg-[#1E2622]">
                                <div class="flex items-start gap-4">
                                    <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-white text-[#3F7A5F] shadow-sm dark:bg-[#2A3A32] dark:text-[#75B892]">
                                        <flux:icon icon="shield-check" class="size-5" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ __('Assign category and code') }}</div>
                                        <div class="mt-1 text-sm leading-6 text-[#66736D] dark:text-[#9BAAB3]">{{ __('Give each item a warehouse category and code before accounting review.') }}</div>
                                        <div class="mt-4 space-y-4">
                                            @foreach ($itemRequest->items as $index => $requestItem)
                                                <div wire:key="validate-item-{{ $requestItem->id }}" class="grid gap-3 rounded-xl border border-[#DCDAD6] bg-white p-4 dark:border-[#34403C] dark:bg-[#1A1C1A] md:grid-cols-3">
                                                    <div>
                                                        <div class="text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ $requestItem->item_name }}</div>
                                                        <div class="mt-1 text-xs text-[#66736D] dark:text-[#9BAAB3]">{{ $requestItem->item_type }}</div>
                                                    </div>
                                                    <div>
                                                        <flux:select wire:model="items.{{ $index }}.category_id" label="{{ __('Category') }}">
                                                            <option value="">-- {{ __('Select category') }} --</option>
                                                            @foreach (\App\Models\ItemCategory::all() as $cat)
                                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                            @endforeach
                                                        </flux:select>
                                                        <flux:error name="items.{{ $index }}.category_id" />
                                                    </div>
                                                    <div>
                                                        <flux:input wire:model="items.{{ $index }}.item_code" label="{{ __('Item code') }}" />
                                                        <flux:error name="items.{{ $index }}.item_code" />
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-4 flex justify-end">
                                            <flux:button type="submit" variant="primary" icon="shield-check">{{ __('Validate request') }}</flux:button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endcan

                        @can('account', $itemRequest)
                            @if ($itemRequest->status->value === 'validated')
                                <form wire:submit="accountRequest" class="rounded-xl border border-[#DCDAD6] border-l-4 border-l-[#3B6478] bg-[#F7FAFB] p-5 shadow-[0_4px_14px_rgba(0,0,0,0.04)] dark:border-[#34403C] dark:border-l-[#6EA3BA] dark:bg-[#1E2427]">
                                    <div class="flex items-start gap-4">
                                        <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-white text-[#3B6478] shadow-sm dark:bg-[#2A3439] dark:text-[#6EA3BA]">
                                            <flux:icon icon="banknotes" class="size-5" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ __('Map item accounts') }}</div>
                                            <div class="mt-1 text-sm leading-6 text-[#66736D] dark:text-[#9BAAB3]">{{ __('Assign inventory and sales COA for each item.') }}</div>
                                            <div class="mt-4 space-y-4">
                                                @foreach ($itemRequest->items as $index => $requestItem)
                                                    <div wire:key="account-item-{{ $requestItem->id }}" class="grid gap-3 rounded-xl border border-[#DCDAD6] bg-white p-4 dark:border-[#34403C] dark:bg-[#1A1C1A] md:grid-cols-3">
                                                        <div>
                                                            <div class="text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ $requestItem->item_name }}</div>
                                                            <div class="mt-1 text-xs text-[#66736D] dark:text-[#9BAAB3]">{{ $requestItem->item_type }}</div>
                                                        </div>
                                                        <div>
                                                            <flux:select wire:model="items.{{ $index }}.inventory_coa_id" label="{{ __('Inventory COA') }}">
                                                                <option value="">-- {{ __('Select COA') }} --</option>
                                                                @foreach (\App\Models\Coa::all() as $coa)
                                                                    <option value="{{ $coa->id }}">{{ $coa->name }}</option>
                                                                @endforeach
                                                            </flux:select>
                                                            <flux:error name="items.{{ $index }}.inventory_coa_id" />
                                                        </div>
                                                        <div>
                                                            <flux:select wire:model="items.{{ $index }}.sales_coa_id" label="{{ __('Sales COA') }}">
                                                                <option value="">-- {{ __('Select COA') }} --</option>
                                                                @foreach (\App\Models\Coa::all() as $coa)
                                                                    <option value="{{ $coa->id }}">{{ $coa->name }}</option>
                                                                @endforeach
                                                            </flux:select>
                                                            <flux:error name="items.{{ $index }}.sales_coa_id" />
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <flux:button type="submit" variant="primary" icon="banknotes">{{ __('Save mapping') }}</flux:button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @endcan
                        
                        @can('approve', $itemRequest)
                            @if ($itemRequest->status === \Modules\ItemRequest\Enums\ItemRequestStatus::WaitingForIT)
                                <form wire:submit="submitITProof" class="rounded-xl border border-[#DCDAD6] border-l-4 border-l-[#8A6F35] bg-[#F8F6F0] p-5 shadow-[0_4px_14px_rgba(0,0,0,0.04)] dark:border-[#34403C] dark:border-l-[#D0B36A] dark:bg-[#1C1B16]">
                                    <div class="flex items-start gap-4">
                                        <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-white text-[#8A6F35] shadow-sm dark:bg-[#2C2A1E] dark:text-[#D0B36A]">
                                            <flux:icon icon="server-stack" class="size-5" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ __('Submit Accurate Proof') }}</div>
                                            <div class="mt-4" wire:ignore>
                                                <input id="itProof" type="hidden" name="itProof" x-ref="trix">
                                                <trix-editor input="itProof" @trix-change="$wire.set('itProof', $event.target.value)"></trix-editor>
                                                <flux:error name="itProof" />
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <flux:button type="submit" variant="primary" icon="check">{{ __('Approve and finalize') }}</flux:button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @endcan


                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#327A7A] dark:text-[#6DB8B3]">
                                <flux:icon icon="paper-clip" class="size-3.5" />
                                {{ __('Attachments') }}
                            </div>
                            <div class="rounded-full bg-[#EEF2F0] px-2.5 py-0.5 text-xs text-[#66736D] dark:bg-[#222927] dark:text-[#9BAAB3]">
                                {{ $itemRequest->attachments->count() }}
                            </div>
                        </div>

                        <div class="space-y-3">
                            @forelse ($itemRequest->attachments as $file)
                                <div wire:key="attachment-{{ $file->id }}" class="flex items-center justify-between gap-4 rounded-xl border border-[#D6E3E2] bg-[#F4F9F8] p-4 dark:border-[#30433F] dark:bg-[#1C2624]">
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk($file->disk)->url($file->path) }}" target="_blank" class="flex min-w-0 flex-1 items-center gap-3 group">
                                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white text-[#327A7A] shadow-sm dark:bg-[#2A3D3C] dark:text-[#6DB8B3]">
                                            <flux:icon icon="document" class="size-4.5" />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-semibold text-[#17201B] underline-offset-2 group-hover:underline dark:text-[#E6E6E4]">{{ $file->original_name }}</div>
                                            <div class="mt-0.5 text-xs text-[#66736D] dark:text-[#9BAAB3]">{{ number_format(($file->size ?? 0) / 1024, 1) }} KB</div>
                                        </div>
                                    </a>
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk($file->disk)->url($file->path) }}" target="_blank" class="shrink-0" download>
                                        <flux:icon icon="arrow-down-tray" class="size-4 text-[#8C8C8C] transition hover:text-[#327A7A] dark:text-[#6F6F6F] dark:hover:text-[#6DB8B3]" />
                                    </a>
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-[#DCDAD6] bg-[#F4F6F5] px-5 py-8 text-center dark:border-[#34403C] dark:bg-[#121615]">
                                    <flux:icon icon="paper-clip" class="mx-auto size-5 text-[#8C8C8C] dark:text-[#6F6F6F]" />
                                    <div class="mt-3 text-sm font-medium text-[#17201B] dark:text-[#E6E6E4]">{{ __('No attachments yet') }}</div>
                                    <div class="mt-1 text-sm text-[#66736D] dark:text-[#9BAAB3]">{{ __('Upload supplier sheets, specs, or references when needed.') }}</div>
                                </div>
                            @endforelse
                        </div>

                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#8A6F35] dark:text-[#D0B36A]">
                                <flux:icon icon="queue-list" class="size-3.5" />
                                {{ __('History') }}
                            </div>
                            <div class="rounded-full bg-[#EEF2F0] px-2.5 py-0.5 text-xs text-[#66736D] dark:bg-[#222927] dark:text-[#9BAAB3]">
                                {{ $itemRequest->histories->count() }}
                            </div>
                        </div>

                        <div class="space-y-3">
                            @foreach ($itemRequest->histories as $history)
                                <div wire:key="history-{{ $history->id }}" class="flex gap-4 rounded-xl border border-[#E7E0D0] bg-[#F8F6F0] p-4 dark:border-[#3F392C] dark:bg-[#1C1B16]">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white text-[#8A6F35] shadow-sm dark:bg-[#2C2A1E] dark:text-[#D0B36A]">
                                        <flux:icon icon="arrow-path-rounded-square" class="size-4.5" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <div class="text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ str($history->action)->replace('_', ' ')->headline() }}</div>
                                            <div class="text-xs text-[#66736D] dark:text-[#9BAAB3]">{{ $history->created_at?->format('d M Y H:i') }}</div>
                                        </div>
                                        <div class="mt-1 text-sm text-[#66736D] dark:text-[#9BAAB3]">
                                            {{ $history->actor?->name ?? '—' }}
                                        </div>
                                        @if ($history->remarks)
                                            <div class="mt-2 rounded-2xl bg-white px-3 py-2 text-sm text-[#4A4A4A] dark:bg-[#2C2C2C] dark:text-[#C0C0C0]">{{ $history->remarks }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-6 bg-[#F8F9F7] px-6 py-8 dark:bg-[#1B1D1D] lg:px-8">
                    <div class="rounded-xl border border-[#DCDAD6] bg-white p-5 dark:border-[#34403C] dark:bg-[#1A1C1A]">
                        <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#3F7A5F] dark:text-[#75B892]">
                            <flux:icon icon="queue-list" class="size-3.5" />
                            {{ __('Routing') }}
                        </div>

                        <div class="mt-5 space-y-4">
                            @foreach ($steps as $i => $step)
                                @php
                                    $isCurrent = $i === $currentStepIdx;
                                    $isDone = $i < $currentStepIdx;
                                @endphp
                                <div class="flex items-start gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="flex size-9 items-center justify-center rounded-full border-2 text-sm {{ $isCurrent ? 'border-[#3F7A5F] bg-white text-[#3F7A5F] shadow-sm dark:border-[#75B892] dark:bg-[#222927] dark:text-[#75B892]' : ($isDone ? 'border-[#3F7A5F] bg-[#3F7A5F] text-white dark:border-[#448960] dark:bg-[#448960]' : 'border-[#DCDAD6] bg-[#EEF2F0] text-[#66736D] dark:border-[#3A3F3C] dark:bg-[#222927] dark:text-[#66736D]') }}">
                                            @if ($isDone)
                                                <flux:icon icon="check" class="size-4" />
                                            @else
                                                <flux:icon :icon="$step['icon']" class="size-4" />
                                            @endif
                                        </div>
                                        @if ($i < count($steps) - 1)
                                            <div class="mt-1 w-px flex-1 {{ $isDone ? 'bg-[#3F7A5F] dark:bg-[#448960]' : 'bg-[#DCDAD6] dark:bg-[#3A3F3C]' }}"></div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1 pb-6 text-sm">
                                        <div class="font-semibold {{ $isCurrent ? 'text-[#17201B] dark:text-[#E6E6E4]' : ($isDone ? 'text-[#3F7A5F] dark:text-[#75B892]' : 'text-[#66736D] dark:text-[#66736D]') }}">{{ $step['label'] }}</div>
                                        <div class="mt-0.5 text-xs {{ $isCurrent ? 'font-medium text-[#3F7A5F] dark:text-[#75B892]' : ($isDone ? 'text-[#3F7A5F] dark:text-[#75B892]' : 'text-[#8C8C8C] dark:text-[#5C5C5C]') }}">
                                            {{ $isCurrent ? __('Active lane') : ($isDone ? __('Completed') : __('Waiting')) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-xl border border-[#DCDAD6] bg-white p-5 dark:border-[#34403C] dark:bg-[#1A1C1A]">
                        <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#4E5C66] dark:text-[#9BAAB3]">
                            <flux:icon icon="clipboard-document-list" class="size-3.5" />
                            {{ __('Intake details') }}
                        </div>

                        <div class="mt-5 space-y-4">
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#66736D] dark:text-[#9BAAB3]">{{ __('Request no') }}</div>
                                <div class="mt-1.5 font-mono text-sm font-semibold tracking-[0.1em] text-[#17201B] dark:text-[#E6E6E4]">{{ $itemRequest->request_no ?? '—' }}</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#EEF2F0] text-[#66736D] dark:bg-[#222927] dark:text-[#9BAAB3]">
                                    <flux:icon icon="user" class="size-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs text-[#66736D] dark:text-[#9BAAB3]">{{ __('Requester') }}</div>
                                    <div class="truncate text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ $itemRequest->requester?->name ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#EEF2F0] text-[#66736D] dark:bg-[#222927] dark:text-[#9BAAB3]">
                                    <flux:icon icon="calendar-days" class="size-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs text-[#66736D] dark:text-[#9BAAB3]">{{ __('Created') }}</div>
                                    <div class="truncate text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ $itemRequest->created_at?->format('d M Y') ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="h-px bg-[#DCDAD6] dark:bg-[#34403C]"></div>
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#66736D] dark:text-[#9BAAB3]">{{ __('Items') }}</div>
                                <div class="mt-3 space-y-3">
                                    @foreach ($itemRequest->items as $requestItem)
                                        <div class="rounded-xl border border-[#DCDAD6] bg-white p-4 shadow-sm dark:border-[#34403C] dark:bg-[#1A1C1A]">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="text-sm font-semibold text-[#17201B] dark:text-[#E6E6E4]">{{ $requestItem->item_name }}</div>
                                                <flux:badge size="sm" variant="pill" class="bg-[#F4F6F5] text-[#66736D] dark:bg-[#222927]">{{ $requestItem->item_type }}</flux:badge>
                                            </div>
                                            <div class="mt-4 grid gap-3 border-t border-[#DCDAD6] pt-3 text-xs dark:border-[#34403C]">
                                                <div class="flex items-center gap-2 text-[#66736D] dark:text-[#9BAAB3]">
                                                    <flux:icon icon="cube" class="size-3.5" />
                                                    {{ $requestItem->unit_id ? \Illuminate\Support\Facades\DB::table('units')->where('id', $requestItem->unit_id)->value('name') : '—' }}
                                                </div>
                                                <div class="flex items-center gap-2 text-[#3F7A5F] dark:text-[#75B892]">
                                                    <flux:icon icon="tag" class="size-3.5" />
                                                    {{ $requestItem->category_id ? \App\Models\ItemCategory::find($requestItem->category_id)?->name : __('Uncategorized') }}
                                                    <span class="font-mono text-[#66736D]">{{ $requestItem->item_code ? '('.$requestItem->item_code.')' : '' }}</span>
                                                </div>
                                                <div class="flex items-center gap-2 text-[#3B6478] dark:text-[#6EA3BA]">
                                                    <flux:icon icon="banknotes" class="size-3.5" />
                                                    {{ $requestItem->inventory_coa_id ? \App\Models\Coa::find($requestItem->inventory_coa_id)?->name : __('Inv COA: —') }}
                                                    <span class="text-[#66736D]">|</span>
                                                    {{ $requestItem->sales_coa_id ? \App\Models\Coa::find($requestItem->sales_coa_id)?->name : __('Sales COA: —') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#66736D] dark:text-[#9BAAB3]">{{ __('Notes') }}</div>
                                <div class="mt-1.5 text-sm leading-6 text-[#4A4A4A] dark:text-[#C0C0C0]">{{ $itemRequest->notes ?: __('No notes.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
