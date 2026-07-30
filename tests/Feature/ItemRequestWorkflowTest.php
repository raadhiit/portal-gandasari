<?php

use App\Models\User;
use App\Notifications\ItemRequestStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\ItemRequest\Actions\AccountItemRequestAction;
use Modules\ItemRequest\Actions\ApproveItemRequestAction;
use Modules\ItemRequest\Actions\CreateItemRequestAction;
use Modules\ItemRequest\Actions\RejectItemRequestAction;
use Modules\ItemRequest\Actions\ReviseItemRequestAction;
use Modules\ItemRequest\Actions\SubmitItemRequestAction;
use Modules\ItemRequest\Actions\ValidateItemRequestAction;
use Modules\ItemRequest\Enums\ItemRequestStatus;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    DB::table('coas')->upsert([
        [
            'code' => '5000',
            'name' => 'Inventory Expense',
            'department_id' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ], ['code'], ['name', 'department_id', 'is_active', 'updated_at']);
});

it('runs item request workflow until approved', function () {
    Notification::fake();

    $requester = User::factory()->create(['department_id' => 1]);
    $validator = User::factory()->create(['department_id' => 1]);
    $accounting = User::factory()->create(['department_id' => 1]);

    $requester->givePermissionTo(['item-request.create', 'item-request.update-own', 'item-request.submit', 'item-request.view']);
    $validator->givePermissionTo(['item-request.view', 'item-request.validate']);
    $accounting->givePermissionTo(['item-request.view', 'item-request.account']);

    $itemRequest = app(CreateItemRequestAction::class)->handle($requester, [
        'business_unit_id' => 1,
        'notes' => 'Untuk produksi',
        'items' => [
            ['item_name' => 'Soda Api', 'item_type' => 'Chemical', 'unit_id' => 1],
            ['item_name' => 'Kapur', 'item_type' => 'Chemical', 'unit_id' => 1],
        ],
    ]);

    expect($itemRequest->status)->toBe(ItemRequestStatus::Draft);
    expect($itemRequest->items)->toHaveCount(2);
    expect($requester->can('submit', $itemRequest))->toBeTrue();

    $itemRequest = app(SubmitItemRequestAction::class)->handle($requester, $itemRequest);
    expect($itemRequest->status)->toBe(ItemRequestStatus::Submitted);
    expect($validator->can('validate', $itemRequest))->toBeTrue();

    $itemRequest = app(ValidateItemRequestAction::class)->handle($validator, $itemRequest, [
        ['id' => $itemRequest->items[0]->id, 'category_id' => 1, 'item_code' => 'ITEM-0001'],
        ['id' => $itemRequest->items[1]->id, 'category_id' => 1, 'item_code' => 'ITEM-0002'],
    ]);

    expect($itemRequest->status)->toBe(ItemRequestStatus::Validated);
    expect($itemRequest->items[0]->item_code)->toBe('ITEM-0001');
    expect($itemRequest->items[1]->item_code)->toBe('ITEM-0002');
    expect($accounting->can('account', $itemRequest))->toBeTrue();

    $itemRequest = app(AccountItemRequestAction::class)->handle($accounting, $itemRequest, [
        ['id' => $itemRequest->items[0]->id, 'inventory_coa_id' => 1, 'sales_coa_id' => 1],
        ['id' => $itemRequest->items[1]->id, 'inventory_coa_id' => 1, 'sales_coa_id' => 1],
    ]);
    expect($itemRequest->status)->toBe(ItemRequestStatus::Accounted);

    $itemRequest = app(ApproveItemRequestAction::class)->handle($accounting, $itemRequest);
    expect($itemRequest->status)->toBe(ItemRequestStatus::Approved);

    expect($itemRequest->histories()->count())->toBe(5);
    Notification::assertSentTo($validator, ItemRequestStatusChangedNotification::class, function (ItemRequestStatusChangedNotification $notification): bool {
        return $notification->status === ItemRequestStatus::Submitted->value;
    });
    Notification::assertSentTo($accounting, ItemRequestStatusChangedNotification::class, function (ItemRequestStatusChangedNotification $notification): bool {
        return $notification->status === ItemRequestStatus::Validated->value;
    });
    Notification::assertSentTo($requester, ItemRequestStatusChangedNotification::class, function (ItemRequestStatusChangedNotification $notification): bool {
        return $notification->status === ItemRequestStatus::Approved->value;
    });
    expect(DB::table('audit_logs')->where('module', 'item-request')->count())->toBe(4);
});

it('rejects invalid transition from draft to validate', function () {
    $requester = User::factory()->create(['department_id' => 1]);
    $validator = User::factory()->create(['department_id' => 1]);

    $itemRequest = app(CreateItemRequestAction::class)->handle($requester, [
        'business_unit_id' => 1,
        'items' => [
            ['item_name' => 'Plastik', 'item_type' => 'Packaging', 'unit_id' => 1],
        ],
    ]);

    expect(fn () => app(ValidateItemRequestAction::class)->handle($validator, $itemRequest, [
        ['id' => $itemRequest->items[0]->id, 'category_id' => 1, 'item_code' => 'ITEM-0003'],
    ]))->toThrow(ValidationException::class);
});

it('can revise and reject after submitted', function () {
    $requester = User::factory()->create(['department_id' => 1]);
    $validator = User::factory()->create(['department_id' => 1]);

    $requester->givePermissionTo(['item-request.create', 'item-request.update-own', 'item-request.submit', 'item-request.view']);
    $validator->givePermissionTo(['item-request.view', 'item-request.validate', 'item-request.reject', 'item-request.revise']);

    $itemRequest = app(CreateItemRequestAction::class)->handle($requester, [
        'business_unit_id' => 1,
        'items' => [
            ['item_name' => 'Karton', 'item_type' => 'Packaging', 'unit_id' => 1],
        ],
    ]);

    $itemRequest = app(SubmitItemRequestAction::class)->handle($requester, $itemRequest);

    $revised = app(ReviseItemRequestAction::class)->handle($validator, $itemRequest, 'Lengkapi spesifikasi');
    expect($revised->status)->toBe(ItemRequestStatus::Revised);
    expect($revised->revision_count)->toBe(1);

    $resubmitted = app(SubmitItemRequestAction::class)->handle($requester, $revised);
    $rejected = app(RejectItemRequestAction::class)->handle($validator, $resubmitted, 'Tidak sesuai kebutuhan');

    expect($rejected->status)->toBe(ItemRequestStatus::Rejected);
    expect($rejected->last_remarks)->toBe('Tidak sesuai kebutuhan');
    expect(DB::table('notifications')->where('notifiable_id', $requester->id)->count())->toBeGreaterThanOrEqual(2);
    expect(DB::table('audit_logs')->where('module', 'item-request')->count())->toBe(4);
});

it('shows notification center page', function () {
    $user = User::factory()->create([
        'department_id' => 1,
        'email_verified_at' => now(),
    ]);

    $user->givePermissionTo('notification.view');

    DB::table('notifications')->insert([
        'id' => (string) Str::uuid(),
        'type' => ItemRequestStatusChangedNotification::class,
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => json_encode([
            'message' => 'REQ-2026-0001 approved',
            'status' => ItemRequestStatus::Approved->value,
            'url' => '/item-requests/1',
        ], JSON_THROW_ON_ERROR),
        'read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertSuccessful()
        ->assertSee('Notifications')
        ->assertSee('REQ-2026-0001 approved');
});

it('writes iam audit log when user updated', function () {
    $actor = User::factory()->create([
        'department_id' => 1,
        'email_verified_at' => now(),
    ]);
    $target = User::factory()->create(['department_id' => 1]);

    $this->actingAs($actor);

    $target->update(['name' => 'Updated User']);

    expect(DB::table('audit_logs')->where('module', 'iam')->where('auditable_type', User::class)->where('auditable_id', $target->id)->exists())->toBeTrue();
});
