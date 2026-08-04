<div class="space-y-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ $user->name }}</flux:heading>
        <flux:text class="text-sm text-neutral-600 dark:text-neutral-300">{{ $user->email }}</flux:text>
    </div>

    <form wire:submit="save" class="grid gap-6 xl:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
        <section class="space-y-6 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="space-y-1">
                <flux:heading size="md">{{ __('User setup') }}</flux:heading>
                <flux:text class="text-sm text-neutral-600 dark:text-neutral-300">{{ __('Set department and assign role bundles.') }}</flux:text>
            </div>

            <flux:field>
                <flux:label>{{ __('Department') }}</flux:label>
                <flux:select wire:model="departmentId">
                    <option value="">--</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="departmentId" />
            </flux:field>

            <div class="space-y-4">
                <div>
                    <flux:label>{{ __('Roles') }}</flux:label>
                    <flux:text class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">{{ __('Roles stay as source of truth. Permission map below shows what each role opens per module.') }}</flux:text>
                </div>

                <div class="space-y-4">
                    @foreach ($roles as $role)
                        @php
                            $rolePermissions = $role->permissions->pluck('name');
                        @endphp
                        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-800/60">
                            <div class="flex items-start gap-3">
                                <flux:checkbox wire:model="selectedRoles" value="{{ $role->name }}" :label="$role->name" />
                            </div>

                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                @foreach ($permissionGroups as $group)
                                    @php
                                        $matchingSubmodules = collect($group['submodules'])->filter(function (array $submodule) use ($rolePermissions): bool {
                                            return collect($submodule['permissions'])->pluck('permission')->intersect($rolePermissions)->isNotEmpty();
                                        });
                                    @endphp
                                    @if ($matchingSubmodules->isNotEmpty())
                                        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-900">
                                            <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $group['label'] }}</div>
                                            <div class="mt-3 space-y-3">
                                                @foreach ($matchingSubmodules as $submodule)
                                                    <div>
                                                        <div class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">{{ $submodule['label'] }}</div>
                                                        <div class="mt-2 space-y-2">
                                                            @foreach ($submodule['permissions'] as $permission)
                                                                @if ($rolePermissions->contains($permission['permission']))
                                                                    <div class="flex items-start gap-2 text-sm text-neutral-600 dark:text-neutral-300">
                                                                        <flux:icon icon="check" class="mt-0.5 size-4 text-emerald-600 dark:text-emerald-400" />
                                                                        <span>{{ $permission['label'] }}</span>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <flux:error name="selectedRoles" />
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </section>

        <section class="space-y-4 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="space-y-1">
                <flux:heading size="md">{{ __('Permission map') }}</flux:heading>
                <flux:text class="text-sm text-neutral-600 dark:text-neutral-300">{{ __('Module and submodule grouping comes from current app structure and permission registry.') }}</flux:text>
            </div>

            <div class="space-y-4">
                @foreach ($permissionGroups as $group)
                    <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-800/60">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $group['label'] }}</div>
                            <flux:badge size="sm" color="zinc">{{ collect($group['submodules'])->sum(fn (array $submodule): int => count($submodule['permissions'])) }} {{ __('permissions') }}</flux:badge>
                        </div>

                        <div class="mt-3 space-y-3">
                            @foreach ($group['submodules'] as $submodule)
                                <div class="rounded-xl border border-neutral-200 bg-white px-3 py-3 dark:border-neutral-700 dark:bg-neutral-900">
                                    <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $submodule['label'] }}</div>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($submodule['permissions'] as $permission)
                                            <flux:badge size="sm" color="zinc">{{ $permission['permission'] }}</flux:badge>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </form>
</div>
