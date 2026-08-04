<?php

namespace Modules\IAM\Livewire;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\IAM\Support\PermissionRegistry;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Title('Create Role')]
class RoleCreate extends Component
{
    use AuthorizesRequests;

    public string $name = '';

    /** @var array<int, string> */
    public array $selectedPermissions = [];

    public function mount(): void
    {
        $this->authorize('create', Role::class);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::findOrCreate($validated['name'], 'web');
        $role->syncPermissions($validated['selectedPermissions']);

        Flux::toast(variant: 'success', text: 'Role created.');

        $this->redirectRoute('admin.roles.edit', $role);
    }

    public function render(): View
    {
        $permissionGroups = PermissionRegistry::groups(
            Permission::query()->orderBy('name')->pluck('name')->all()
        );

        return view('iam::roles.create', compact('permissionGroups'))->layout('layouts.app');
    }
}
