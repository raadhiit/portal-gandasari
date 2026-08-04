<div>
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="lg">{{ __('Roles') }}</flux:heading>
            <flux:text class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">{{ __('Manage reusable granular permission bundles per module and submodule.') }}</flux:text>
        </div>
        @can('create', Spatie\Permission\Models\Role::class)
            <flux:button :href="route('admin.roles.create')" wire:navigate variant="primary" icon="plus">{{ __('Create role') }}</flux:button>
        @endcan
    </div>

    <div class="mt-4 max-w-sm">
        <flux:input wire:model.live.debounce="search" placeholder="{{ __('Search role') }}" />
    </div>

    <div class="relative mt-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-900">
                <tr class="text-left text-sm font-medium text-neutral-700 dark:text-neutral-200">
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Permissions') }}</th>
                    <th class="px-4 py-3">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-800">
                @forelse ($roles as $role)
                    <tr wire:key="role-{{ $role->id }}" class="text-sm">
                        <td class="px-4 py-3 font-medium">{{ $role->name }}</td>
                        <td class="px-4 py-3">{{ $role->permissions_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @can('update', $role)
                                    <flux:button :href="route('admin.roles.edit', $role)" wire:navigate size="sm">{{ __('Edit') }}</flux:button>
                                @endcan
                                @can('delete', $role)
                                    <flux:button wire:click="deleteRole({{ $role->id }})" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-sm text-zinc-500">{{ __('No roles found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $roles->links() }}</div>
</div>
