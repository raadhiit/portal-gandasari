<?php

namespace Modules\IAM\Livewire;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\IAM\Support\PermissionRegistry;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Title('Edit Role')]
class RoleEdit extends Component
{
    use AuthorizesRequests;

    public Role $role;

    public string $name = '';

    /** @var array<int, string> */
    public array $selectedPermissions = [];

    public function mount(Role $role): void
    {
        $this->authorize('update', $role);
        $this->role = $role->load('permissions');
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($this->role->id)],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $this->role->forceFill(['name' => $validated['name']])->save();
        $this->role->syncPermissions($validated['selectedPermissions']);
        $this->role->refresh();

        Flux::toast(variant: 'success', text: 'Role updated.');
    }

    public function render(): View
    {
        $permissionGroups = PermissionRegistry::groups(
            Permission::query()->orderBy('name')->pluck('name')->all()
        );

        return view('iam::roles.edit', compact('permissionGroups'))->layout('layouts.app');
    }
}
