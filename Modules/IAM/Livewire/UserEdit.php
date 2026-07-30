<?php

namespace Modules\IAM\Livewire;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Title('Edit User')]
class UserEdit extends Component
{
    use AuthorizesRequests;

    public User $user;

    public string $departmentId = '';

    /** @var array<int, string> */
    public array $selectedRoles = [];

    public function mount(User $user): void
    {
        $this->authorize('update', $user);

        $this->user = $user->load('roles');
        $this->departmentId = (string) ($user->department_id ?? '');
        $this->selectedRoles = $user->roles->pluck('name')->all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'selectedRoles' => ['array'],
            'selectedRoles.*' => ['string', 'exists:roles,name'],
        ]);

        $this->user->forceFill([
            'department_id' => $validated['departmentId'] === '' ? null : (int) $validated['departmentId'],
        ])->save();

        $this->user->syncRoles($validated['selectedRoles']);
        $this->user->refresh();

        Flux::toast(variant: 'success', text: 'User updated.');
    }

    public function render(): View
    {
        $departments = DB::table('departments')->orderBy('name')->get(['id', 'name']);
        $roles = Role::query()->orderBy('name')->get(['name']);

        return view('iam::users.edit', compact('departments', 'roles'))->layout('layouts.app');
    }
}
