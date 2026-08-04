<?php

namespace Modules\IAM\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Users')]
class UserIndex extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $this->authorize('viewAny', User::class);

        $viewer = Auth::user();

        $users = User::query()
            ->with('roles')
            ->when(! $viewer->hasAnyRole(['Admin Portal', 'Superadmin']), fn (Builder $query): Builder => $query->where('department_id', $viewer->department_id))
            ->when($this->search !== '', fn (Builder $query): Builder => $query->where(function (Builder $query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(10);

        return view('iam::users.index', compact('users'))->layout('layouts.app');
    }
}
