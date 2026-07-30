<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    public function created(User $user): void
    {
        $this->log('create', $user, null, $this->sanitize($user->toArray()));
    }

    public function updated(User $user): void
    {
        $changes = $user->getChanges();

        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        $before = Arr::only($this->sanitize($user->getOriginal()), array_keys($changes));
        $after = Arr::only($this->sanitize($user->fresh()?->toArray() ?? $user->toArray()), array_keys($changes));

        $this->log('update', $user, $before, $after);
    }

    public function deleted(User $user): void
    {
        $this->log('delete', $user, $this->sanitize($user->toArray()), null);
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    private function log(string $action, User $user, ?array $before, ?array $after): void
    {
        DB::table('audit_logs')->insert([
            'actor_id' => Auth::id(),
            'module' => 'iam',
            'action' => $action,
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'before_json' => $before === null ? null : json_encode($before, JSON_THROW_ON_ERROR),
            'after_json' => $after === null ? null : json_encode($after, JSON_THROW_ON_ERROR),
            'remarks' => null,
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
            'user_agent' => app()->runningInConsole() ? null : request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function sanitize(array $attributes): array
    {
        return Arr::except($attributes, [
            'password',
            'remember_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ]);
    }
}
