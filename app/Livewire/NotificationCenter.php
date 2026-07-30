<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Notifications')]
class NotificationCenter extends Component
{
    public function markAsRead(string $id): void
    {
        Auth::user()->unreadNotifications->where('id', $id)->markAsRead();
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render(): View
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);

        return view('livewire.notification-center', compact('notifications'));
    }
}
