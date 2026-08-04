<?php

namespace Modules\IAM\Livewire;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Title('Roles')]
class RoleIndex extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteRole(int $roleId): void
    {
        $role = Role::query()->findOrFail($roleId);
        $this->authorize('delete', $role);
        $role->delete();
        Flux::toast(variant: 'success', text: 'Role deleted.');
    }

    public function render(): View
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::query()
            ->withCount('permissions')
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);

        return view('iam::roles.index', compact('roles'))->layout('layouts.app');
    }
}
