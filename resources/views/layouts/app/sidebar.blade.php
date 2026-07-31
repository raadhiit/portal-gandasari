<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#E9EDEB] dark:bg-[#121615]">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-[#CED4D0] bg-[#F7F8F7] dark:border-[#34403C] dark:bg-[#1A1C1A]">
            <flux:sidebar.header class="border-b border-[#CED4D0] bg-[#EEF1EF] dark:border-[#34403C] dark:bg-[#171B1A]">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="px-3 py-4">
                @foreach (config('navigation') as $group)
                    @canany($group['visibility'])
                        <flux:sidebar.group expandable :heading="__($group['heading'])" class="{{ $group['class'] ?? '[&_h3]:text-[#66736D] dark:[&_h3]:text-[#9BAAB3]' }}">
                            @foreach ($group['items'] as $item)
                                @can($item['permission'])
                                    <flux:sidebar.item :icon="$item['icon']" :href="route($item['route'])" :current="request()->routeIs($item['active'])" wire:navigate class="{{ $item['class'] }}">
                                        {{ __($item['label']) }}
                                    </flux:sidebar.item>
                                @endcan
                            @endforeach
                        </flux:sidebar.group>
                    @endcanany
                @endforeach
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
