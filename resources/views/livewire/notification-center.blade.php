<div>
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Notifications') }}</flux:heading>
        @if($notifications->isNotEmpty())
            <flux:button wire:click="markAllAsRead" variant="ghost">{{ __('Mark all as read') }}</flux:button>
        @endif
    </div>

    <div class="mt-6 grid gap-4">
        @forelse ($notifications as $notification)
            <flux:card wire:key="notif-{{ $notification->id }}" class="flex items-start justify-between {{ $notification->read_at ? 'opacity-70' : '' }}">
                <div class="flex-1">
                    <flux:text class="font-medium">{{ $notification->data['message'] }}</flux:text>
                    <flux:text class="text-xs text-zinc-500">{{ $notification->created_at->diffForHumans() }}</flux:text>
                </div>
                
                @if(!$notification->read_at)
                    <flux:button wire:click="markAsRead('{{ $notification->id }}')" variant="ghost" icon="check" />
                @endif
            </flux:card>
        @empty
            <flux:text>{{ __('No notifications.') }}</flux:text>
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
