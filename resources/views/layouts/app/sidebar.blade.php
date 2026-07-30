<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#F4F6F5] dark:bg-[#121615]">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-[#DCDAD6] bg-white dark:border-[#34403C] dark:bg-[#1A1C1A]">
            <flux:sidebar.header class="border-b border-[#DCDAD6] bg-[#F8F9F7] dark:border-[#34403C] dark:bg-[#171B1A]">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="px-3 py-4">
                <flux:sidebar.group expandable heading="Platform" class="[&_h3]:text-[#66736D] dark:[&_h3]:text-[#9BAAB3]">
                    @can('dashboard.view')
                        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="rounded-xl text-[#4E5C66] hover:bg-[#EEF2F0] hover:text-[#17201B] data-current:bg-[#EAF4FB] data-current:text-[#2D9CDB] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#1D2B33] dark:data-current:text-[#6EA3BA]">
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>
                    @endcan

                    @can('notification.view')
                        <flux:sidebar.item icon="bell" :href="route('notifications.index')" :current="request()->routeIs('notifications.*')" wire:navigate class="rounded-xl text-[#4E5C66] hover:bg-[#EEF2F0] hover:text-[#17201B] data-current:bg-[#F8EEF8] data-current:text-[#6B5F8F] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#241F2C] dark:data-current:text-[#A59ADD]">
                            {{ __('Notifications') }}
                        </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>

                @canany(['item-request.view', 'item-request.create', 'item-request.validate', 'item-request.account', 'item-request.approve'])
                    <flux:sidebar.group expandable heading="Item Management" class="mt-3 [&_h3]:text-[#66736D] dark:[&_h3]:text-[#9BAAB3]">
                        @can('item-request.view')
                            <flux:sidebar.item icon="clipboard-document-list" :href="route('item-requests.index')" :current="request()->routeIs('item-requests.index')" wire:navigate class="rounded-xl text-[#4E5C66] hover:bg-[#EEF2F0] hover:text-[#17201B] data-current:bg-[#EAF4FB] data-current:text-[#2D9CDB] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#1D2B33] dark:data-current:text-[#6EA3BA]">
                                {{ __('Item Requests') }}
                            </flux:sidebar.item>
                        @endcan

                        @can('item-request.create')
                            <flux:sidebar.item icon="plus" :href="route('item-requests.create')" :current="request()->routeIs('item-requests.create')" wire:navigate class="rounded-xl text-[#4E5C66] hover:bg-[#EEF2F0] hover:text-[#17201B] data-current:bg-[#EAF4FB] data-current:text-[#2D9CDB] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#1D2B33] dark:data-current:text-[#6EA3BA]">
                                {{ __('Create Request') }}
                            </flux:sidebar.item>
                        @endcan

                        @can('item-request.validate')
                            <flux:sidebar.item icon="inbox" :href="route('item-requests.validation-inbox')" :current="request()->routeIs('item-requests.validation-inbox')" wire:navigate class="rounded-xl text-[#4E5C66] hover:bg-[#EEF2F0] hover:text-[#17201B] data-current:bg-[#ECF8F1] data-current:text-[#0E9F6E] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#173126] dark:data-current:text-[#75B892]">
                                {{ __('Validation Inbox') }}
                            </flux:sidebar.item>
                        @endcan

                        @can('item-request.account')
                            <flux:sidebar.item icon="banknotes" :href="route('item-requests.accounting-inbox')" :current="request()->routeIs('item-requests.accounting-inbox')" wire:navigate class="rounded-xl text-[#4E5C66] hover:bg-[#EEF2F0] hover:text-[#17201B] data-current:bg-[#EAF4FB] data-current:text-[#3B82F6] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#1B2A3F] dark:data-current:text-[#7FB0FF]">
                                {{ __('Accounting Inbox') }}
                            </flux:sidebar.item>
                        @endcan

                        @can('item-request.approve')
                            <flux:sidebar.item icon="check-badge" :href="route('item-requests.approval-inbox')" :current="request()->routeIs('item-requests.approval-inbox')" wire:navigate class="rounded-xl text-[#4E5C66] hover:bg-[#EEF2F0] hover:text-[#17201B] data-current:bg-[#EAF9F7] data-current:text-[#14B8A6] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#173331] dark:data-current:text-[#6DB8B3]">
                                {{ __('Approval Inbox') }}
                            </flux:sidebar.item>
                        @endcan
                    </flux:sidebar.group>
                @endcanany

                @canany(['user.view', 'user.manage'])
                    <flux:sidebar.group expandable heading="Administration" class="mt-3 [&_h3]:text-[#66736D] dark:[&_h3]:text-[#9BAAB3]">
                        <flux:sidebar.item icon="users" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate class="rounded-xl text-[#4E5C66] hover:bg-[#EEF2F0] hover:text-[#17201B] data-current:bg-[#EEF2F0] data-current:text-[#4E5C66] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#222927] dark:data-current:text-[#E6E6E4]">
                            {{ __('Users') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endcanany
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <x-desktop-user-menu class="hidden lg:block border-t border-[#DCDAD6] bg-[#F8F9F7] dark:border-[#34403C] dark:bg-[#171B1A]" :name="auth()->user()->name" />
        </flux:sidebar>

        <flux:header class="lg:hidden border-b border-[#DCDAD6] bg-white dark:border-[#34403C] dark:bg-[#1A1C1A]">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <div class="px-2 py-1.5">
                        <flux:text class="mb-2 text-xs uppercase tracking-wide text-zinc-500">{{ __('Theme') }}</flux:text>
                        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                            <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                            <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                            <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
                        </flux:radio.group>
                    </div>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
