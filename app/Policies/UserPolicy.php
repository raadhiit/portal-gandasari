<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('user.view') || $user->can('user.manage');
    }

    public function view(User $user, User $target): bool
    {
        if ($user->can('user.manage')) {
            return true;
        }

        if (! $user->can('user.view')) {
            return false;
        }

        return $user->department_id !== null && $user->department_id === $target->department_id;
    }

    public function create(User $user): bool
    {
        return $user->can('user.manage');
    }

    public function update(User $user, User $target): bool
    {
        if (! $user->can('user.manage')) {
            return false;
        }

        if ($user->id === $target->id) {
            return true;
        }

        return $user->department_id !== null && $user->department_id === $target->department_id;
    }

    public function delete(User $user, User $target): bool
    {
        if (! $user->can('user.manage') || $user->id === $target->id) {
            return false;
        }

        return $user->department_id !== null && $user->department_id === $target->department_id;
    }
}
