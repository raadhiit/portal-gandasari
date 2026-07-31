<?php

namespace App\Providers;

use App\Livewire\ItemRequest\ExportIndex as ItemRequestExportIndex;
use App\Livewire\NotificationCenter;
use App\Models\User;
use App\Observers\UserObserver;
use App\Policies\UserPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Livewire\Livewire;
use Modules\IAM\Livewire\UserEdit;
use Modules\IAM\Livewire\UserIndex;
use Modules\ItemRequest\Livewire\Create;
use Modules\ItemRequest\Livewire\Inbox;
use Modules\ItemRequest\Livewire\Index;
use Modules\ItemRequest\Livewire\Show;
use Modules\ItemRequest\Models\ItemRequest;
use Modules\ItemRequest\Policies\ItemRequestPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(base_path('Modules/ItemRequest/resources/views'), 'item-request');
        $this->loadViewsFrom(base_path('Modules/IAM/resources/views'), 'iam');

        Livewire::component('notification-center', NotificationCenter::class);
        Livewire::component('item-request.index', Index::class);
        Livewire::component('item-request.create', Create::class);
        Livewire::component('item-request.show', Show::class);
        Livewire::component('item-request.inbox', Inbox::class);
        Livewire::component('item-request.export-index', ItemRequestExportIndex::class);
        Livewire::component('iam.user-index', UserIndex::class);
        Livewire::component('iam.user-edit', UserEdit::class);

        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ItemRequest::class, ItemRequestPolicy::class);

        User::observe(UserObserver::class);

        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
