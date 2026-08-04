<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Create Role') }}</flux:heading>
            <flux:text class="text-sm text-neutral-600 dark:text-neutral-300">{{ __('Build reusable access bundle per module, submodule, and action.') }}</flux:text>
        </div>
        <flux:button :href="route('admin.roles.index')" wire:navigate variant="ghost">{{ __('Back') }}</flux:button>
    </div>

    <form wire:submit="save" class="space-y-6">
        <section class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:field>
                <flux:label>{{ __('Role name') }}</flux:label>
                <flux:input wire:model="name" />
                <flux:error name="name" />
            </flux:field>
        </section>

        <section class="space-y-6 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <flux:heading size="md">{{ __('Permission checklist') }}</flux:heading>
                <flux:text class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">{{ __('Admin can assign role access per module and submodule.') }}</flux:text>
            </div>

            <div class="space-y-5">
                @foreach ($permissionGroups as $group)
                    <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-800/60">
                        <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $group['label'] }}</div>
                        <div class="mt-4 grid gap-4 lg:grid-cols-2">
                            @foreach ($group['submodules'] as $submodule)
                                <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-900">
                                    <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $submodule['label'] }}</div>
                                    <div class="mt-3 grid gap-2">
                                        @foreach ($submodule['permissions'] as $permission)
                                            <flux:checkbox wire:model="selectedPermissions" value="{{ $permission['permission'] }}" :label="$permission['label']" />
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <flux:error name="selectedPermissions" />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">{{ __('Create role') }}</flux:button>
            </div>
        </section>
    </form>
</div>
