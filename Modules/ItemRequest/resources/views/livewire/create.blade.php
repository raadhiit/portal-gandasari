<div class="pb-10">
    <div class="max-w-4xl space-y-2">
        <flux:heading size="xl" level="1">{{ __('Create intake request') }}</flux:heading>
        <flux:text class="text-[#66736D] dark:text-[#9BAAB3]">{{ __('Record item identity and destination. You can review the draft before submitting.') }}</flux:text>
    </div>

    <form wire:submit="save" class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-start">
        <div class="space-y-5">
            <div class="rounded-2xl border border-[#DCDAD6] bg-white p-5 shadow-sm dark:border-[#383838] dark:bg-[#1A1C1A]">
                <div class="mb-6 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#3B6478] dark:text-[#6EA3BA]">
                    <flux:icon icon="cube" class="size-3.5" />
                    {{ __('Details') }}
                </div>
                <div class="grid gap-6 md:grid-cols-3">
                    <flux:field>
                        <flux:label>{{ __('Business Unit') }}</flux:label>
                        <flux:select wire:model="businessUnitId">
                            <option value="">-- {{ __('Select') }} --</option>
                            @foreach ($businessUnits as $bu)
                                <option value="{{ $bu->id }}">{{ $bu->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="businessUnitId" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Unit') }}</flux:label>
                        <flux:select wire:model="unitId">
                            <option value="">-- {{ __('Select') }} --</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="unitId" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Warehouse') }}</flux:label>
                        <flux:select wire:model="warehouseId">
                            <option value="">-- {{ __('Select') }} --</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="warehouseId" />
                    </flux:field>
                </div>
            </div>

            <div class="rounded-2xl border border-[#DCDAD6] bg-white p-5 shadow-sm dark:border-[#383838] dark:bg-[#1A1C1A]">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#3F7A5F] dark:text-[#75B892]">
                        <flux:icon icon="list-bullet" class="size-3.5" />
                        {{ __('Items') }}
                    </div>
                    <flux:button wire:click="addItem" variant="ghost" size="sm" icon="plus">{{ __('Add item') }}</flux:button>
                </div>

                <div class="space-y-4">
                    @foreach ($items as $index => $item)
                        <div wire:key="item-{{ $index }}" class="grid gap-4 md:grid-cols-[1fr_1fr_auto]">
                            <flux:field>
                                <flux:input wire:model="items.{{ $index }}.item_name" placeholder="Item name" />
                                <flux:error name="items.{{ $index }}.item_name" />
                            </flux:field>
                            <flux:field>
                                <flux:input wire:model="items.{{ $index }}.item_type" placeholder="Item type" />
                                <flux:error name="items.{{ $index }}.item_type" />
                            </flux:field>
                            <flux:button wire:click="removeItem({{ $index }})" variant="ghost" icon="trash" class="text-red-500" />
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-[#DCDAD6] bg-white p-5 shadow-sm dark:border-[#383838] dark:bg-[#1A1C1A]">
                <flux:field>
                    <flux:label>{{ __('Notes') }}</flux:label>
                    <flux:textarea wire:model="notes" maxlength="1000" placeholder="Additional details..." />
                    <flux:error name="notes" />
                </flux:field>
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" icon="document-check">{{ __('Save as draft') }}</flux:button>
                <flux:button :href="route('item-requests.index')" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
            </div>
        </div>

        <aside class="hidden lg:block">
            <div class="sticky top-24 w-72 overflow-hidden rounded-2xl border border-[#D7E3E2] bg-white shadow-[0_10px_30px_rgba(18,24,22,0.06)] dark:border-[#2E4B46] dark:bg-[#151B1A]">
                <div class="border-b border-[#D7E3E2] bg-[#F2FBF9] px-5 py-4 dark:border-[#2E4B46] dark:bg-[#10211E]">
                    <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#327A7A] dark:text-[#6DB8B3]">
                        <flux:icon icon="sparkles" class="size-3.5" />
                        Panduan alur
                    </div>
                </div>

                <div class="space-y-4 px-5 py-5">
                    <flux:text class="text-sm leading-6 text-[#66736D] dark:text-[#9BAAB3]">
                        Simpan form ini sebagai draft terlebih dahulu. Setelah itu, Anda bisa meninjau data, menambahkan komentar, dan melampirkan file sebelum meneruskannya ke alur persetujuan.
                    </flux:text>
                </div>
            </div>
        </aside>
    </form>
</div>
