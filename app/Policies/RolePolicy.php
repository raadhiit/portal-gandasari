<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('role.view') || $user->can('role.manage');
    }

    public function view(User $user, Role $role): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('role.create') || $user->can('role.manage');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('role.update') || $user->can('role.manage');
    }

    public function delete(User $user, Role $role): bool
    {
        return ($user->can('role.delete') || $user->can('role.manage'))
            && ! in_array($role->name, ['Admin Portal', 'Superadmin'], true);
    }
}
