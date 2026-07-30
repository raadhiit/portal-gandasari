<div>
        <flux:heading size="lg">{{ __('Users') }}</flux:heading>

        <div class="mt-4 max-w-sm">
            <flux:input wire:model.live.debounce="search" placeholder="{{ __('Search name or email') }}" />
        </div>

        <div class="mt-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-900">
                    <tr class="text-left text-sm font-medium text-neutral-700 dark:text-neutral-200">
                        <th class="px-4 py-3">{{ __('Name') }}</th>
                        <th class="px-4 py-3">{{ __('Email') }}</th>
                        <th class="px-4 py-3">{{ __('Roles') }}</th>
                        <th class="px-4 py-3">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-800">
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="text-sm">
                            <td class="px-4 py-3">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
                            <td class="px-4 py-3">
                                @can('update', $user)
                                    <flux:button :href="route('admin.users.edit', $user)" wire:navigate size="sm">{{ __('Edit') }}</flux:button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-zinc-500">{{ __('No users found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
</div>
