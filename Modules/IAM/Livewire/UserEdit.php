<?php

namespace Modules\IAM\Livewire;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\IAM\Support\PermissionRegistry;
use Spatie\Permission\Models\Permission;
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

        $oldRoles = $this->user->roles->pluck('name')->toArray();
        $this->user->syncRoles($validated['selectedRoles']);
        $newRoles = $this->user->fresh()->roles->pluck('name')->toArray();

        if ($oldRoles !== $newRoles) {
            DB::table('audit_logs')->insert([
                'actor_id' => Auth::id(),
                'module' => 'iam',
                'action' => 'sync_roles',
                'auditable_type' => User::class,
                'auditable_id' => $this->user->id,
                'before_json' => json_encode(['roles' => $oldRoles]),
                'after_json' => json_encode(['roles' => $newRoles]),
                'created_at' => now(),
            ]);
        }

        $this->user->refresh();

        Flux::toast(variant: 'success', text: 'User updated.');
    }

    public function render(): View
    {
        $departments = DB::table('departments')->orderBy('name')->get(['id', 'name']);
        $roles = Role::query()
            ->with('permissions:name')
            ->orderBy('name')
            ->get(['id', 'name']);
        $permissionGroups = PermissionRegistry::groups(
            Permission::query()->orderBy('name')->pluck('name')->all()
        );

        return view('iam::users.edit', compact('departments', 'roles', 'permissionGroups'))->layout('layouts.app');
    }
}
