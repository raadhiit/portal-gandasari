<?php

use App\Actions\ItemRequest\AccountItemRequestAction;
use App\Actions\ItemRequest\CreateItemRequestAction;
use App\Actions\ItemRequest\RejectItemRequestAction;
use App\Actions\ItemRequest\ReviseItemRequestAction;
use App\Actions\ItemRequest\SubmitItemRequestAction;
use App\Actions\ItemRequest\ValidateItemRequestAction;
use App\Enums\ItemRequestStatus;
use App\Models\ItemRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    DB::table('units')->insert([
        'code' => 'PCS',
        'name' => 'Pieces',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('warehouses')->insert([
        'code' => 'WH-01',
        'name' => 'Main Warehouse',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('item_categories')->insert([
        'code' => 'RAW',
        'name' => 'Raw Material',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('coas')->insert([
        'code' => '5000',
        'name' => 'Inventory Expense',
        'department_id' => 1,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

it('runs item request workflow until accounted', function () {
    $requester = User::factory()->create(['department_id' => 1]);
    $validator = User::factory()->create(['department_id' => 1]);
    $accounting = User::factory()->create(['department_id' => 1]);

    $requester->givePermissionTo(['item-request.create', 'item-request.update-own', 'item-request.submit', 'item-request.view']);
    $validator->givePermissionTo(['item-request.view', 'item-request.validate']);
    $accounting->givePermissionTo(['item-request.view', 'item-request.account']);

    $itemRequest = app(CreateItemRequestAction::class)->handle($requester, [
        'unit_id' => 1,
        'warehouse_id' => 1,
        'item_name' => 'Soda Api',
        'item_type' => 'Chemical',
        'notes' => 'Untuk produksi',
    ]);

    expect($itemRequest->status)->toBe(ItemRequestStatus::Draft);
    expect($requester->can('submit', $itemRequest))->toBeTrue();

    $itemRequest = app(SubmitItemRequestAction::class)->handle($requester, $itemRequest);
    expect($itemRequest->status)->toBe(ItemRequestStatus::Submitted);
    expect($validator->can('validate', $itemRequest))->toBeTrue();

    $itemRequest = app(ValidateItemRequestAction::class)->handle($validator, $itemRequest, [
        'category_id' => 1,
        'item_code' => 'ITEM-0001',
    ]);

    expect($itemRequest->status)->toBe(ItemRequestStatus::Validated);
    expect($accounting->can('account', $itemRequest))->toBeTrue();

    $itemRequest = app(AccountItemRequestAction::class)->handle($accounting, $itemRequest, 1);

    expect($itemRequest->status)->toBe(ItemRequestStatus::Accounted);
    expect(ItemRequest::query()->first()?->histories()->count())->toBe(4);
});

it('rejects invalid transition from draft to validate', function () {
    $requester = User::factory()->create(['department_id' => 1]);
    $validator = User::factory()->create(['department_id' => 1]);

    $itemRequest = app(CreateItemRequestAction::class)->handle($requester, [
        'unit_id' => 1,
        'warehouse_id' => 1,
        'item_name' => 'Plastik',
        'item_type' => 'Packaging',
    ]);

    expect(fn () => app(ValidateItemRequestAction::class)->handle($validator, $itemRequest, [
        'category_id' => 1,
        'item_code' => 'ITEM-0002',
    ]))->toThrow(ValidationException::class);
});

it('can revise and reject after submitted', function () {
    $requester = User::factory()->create(['department_id' => 1]);
    $validator = User::factory()->create(['department_id' => 1]);

    $requester->givePermissionTo(['item-request.create', 'item-request.update-own', 'item-request.submit', 'item-request.view']);
    $validator->givePermissionTo(['item-request.view', 'item-request.validate', 'item-request.reject', 'item-request.revise']);

    $itemRequest = app(CreateItemRequestAction::class)->handle($requester, [
        'unit_id' => 1,
        'warehouse_id' => 1,
        'item_name' => 'Karton',
        'item_type' => 'Packaging',
    ]);

    $itemRequest = app(SubmitItemRequestAction::class)->handle($requester, $itemRequest);

    $revised = app(ReviseItemRequestAction::class)->handle($validator, $itemRequest, 'Lengkapi spesifikasi');
    expect($revised->status)->toBe(ItemRequestStatus::Revised);
    expect($revised->revision_count)->toBe(1);

    $resubmitted = app(SubmitItemRequestAction::class)->handle($requester, $revised);
    $rejected = app(RejectItemRequestAction::class)->handle($validator, $resubmitted, 'Tidak sesuai kebutuhan');

    expect($rejected->status)->toBe(ItemRequestStatus::Rejected);
    expect($rejected->last_remarks)->toBe('Tidak sesuai kebutuhan');
});
