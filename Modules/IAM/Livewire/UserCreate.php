<?php

namespace Modules\IAM\Livewire;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\IAM\Support\PermissionRegistry;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Title('Create User')]
class UserCreate extends Component
{
    use AuthorizesRequests;

    public string $name = '';

    public string $email = '';

    public string $departmentId = '';

    public string $password = '';

    public string $password_confirmation = '';

    /** @var array<int, string> */
    public array $selectedRoles = [];

    public function mount(): void
    {
        $this->authorize('create', User::class);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'selectedRoles' => ['array'],
            'selectedRoles.*' => ['string', 'exists:roles,name'],
        ], attributes: [
            'departmentId' => 'department',
            'selectedRoles' => 'roles',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['departmentId'] === '' ? null : (int) $validated['departmentId'],
            'password' => $validated['password'],
            'email_verified_at' => now(),
        ]);

        $user->syncRoles($validated['selectedRoles']);

        Flux::toast(variant: 'success', text: 'User created.');

        $this->redirectRoute('admin.users.edit', $user);
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

        return view('iam::users.create', compact('departments', 'roles', 'permissionGroups'))->layout('layouts.app');
    }
}
