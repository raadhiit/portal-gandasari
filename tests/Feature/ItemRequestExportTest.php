<?php

use App\Jobs\ExportItemRequest;
use App\Livewire\ItemRequest\ExportIndex;
use App\Models\ItemRequestExport;
use App\Models\User;
use App\Notifications\ItemRequestExportReadyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Modules\ItemRequest\Actions\CreateItemRequestAction;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;
use Modules\ItemRequest\Models\ItemRequestItem;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    DB::table('departments')->updateOrInsert(
        ['id' => 2],
        ['code' => 'DEPT-2', 'name' => 'Dept 2', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    );
});

it('queues an export from export inbox', function () {
    Queue::fake();

    $requester = User::factory()->create(['department_id' => 1]);
    $requester->givePermissionTo(['item-request.create', 'item-request.update-own', 'item-request.submit', 'item-request.view', 'item-request.export']);

    $itemRequest = app(CreateItemRequestAction::class)->handle($requester, [
        'business_unit_id' => 1,
        'items' => [['item_name' => 'Test Item', 'item_type' => 'Goods', 'unit_id' => 1]],
    ]);
    $itemRequest->update(['status' => ItemRequestStatus::Approved, 'approved_at' => now()]);
    $itemRequest->items()->update([
        'category_id' => 1,
        'item_code' => 'ITEM-0001',
        'inventory_coa_id' => 1,
        'sales_coa_id' => 1,
    ]);

    $this->actingAs($requester);

    Livewire::test(ExportIndex::class)
        ->call('queueExport', $itemRequest->id);

    $export = ItemRequestExport::query()->first();

    expect($export)->not->toBeNull();
    expect($export->status)->toBe('queued');
    expect($export->requested_by)->toBe($requester->id);

    Queue::assertPushed(ExportItemRequest::class);
});

it('generates export file with coa mapping and allows authorized download', function () {
    Notification::fake();
    Storage::fake('local');

    $requester = User::factory()->create(['department_id' => 1]);
    $requester->givePermissionTo(['item-request.create', 'item-request.update-own', 'item-request.submit', 'item-request.view', 'item-request.export']);

    $itemRequest = app(CreateItemRequestAction::class)->handle($requester, [
        'business_unit_id' => 1,
        'items' => [['item_name' => 'Test Item', 'item_type' => 'Goods', 'unit_id' => 1]],
    ]);
    $itemRequest->update(['status' => ItemRequestStatus::Approved, 'approved_at' => now(), 'notes' => 'Catatan export']);
    $itemRequest->items()->update([
        'category_id' => 1,
        'item_code' => 'ITEM-0001',
        'inventory_coa_id' => 1,
        'sales_coa_id' => 2,
    ]);

    $export = ItemRequestExport::create([
        'item_request_id' => $itemRequest->id,
        'requested_by' => $requester->id,
        'status' => 'queued',
    ]);

    (new ExportItemRequest($export))->handle();

    $export->refresh();

    expect($export->status)->toBe('ready');
    expect(Storage::disk('local')->exists($export->file_path))->toBeTrue();

    $zip = new ZipArchive;
    $opened = $zip->open(Storage::disk('local')->path($export->file_path));
    expect($opened)->toBeTrue();

    $sheetXml = (string) $zip->getFromName('xl/worksheets/sheet1.xml');
    $zip->close();

    expect($sheetXml)->toContain('r="BL2"');
    expect($sheetXml)->toContain('r="BM2"');
    expect($sheetXml)->toContain('6000');
    expect($sheetXml)->toContain('6100');
    expect($sheetXml)->not->toContain('r="BN2" t="inlineStr"><is><t>6100</t></is></c>');

    Notification::assertSentTo($requester, ItemRequestExportReadyNotification::class);

    $response = $this->actingAs($requester)
        ->get(route('item-requests.exports.download', $export));

    $response->assertSuccessful()
        ->assertHeader('content-disposition');

    $response->deleteFileAfterSend(false);
});

it('forbids download outside department scope', function () {
    Storage::fake('local');

    $requester = User::factory()->create(['department_id' => 1]);
    $outsider = User::factory()->create(['department_id' => 2]);

    $requester->givePermissionTo(['item-request.create', 'item-request.update-own', 'item-request.submit', 'item-request.view', 'item-request.export']);
    $outsider->givePermissionTo(['item-request.view', 'item-request.export']);

    $itemRequest = app(CreateItemRequestAction::class)->handle($requester, [
        'business_unit_id' => 1,
        'items' => [['item_name' => 'Test Item', 'item_type' => 'Goods', 'unit_id' => 1]],
    ]);
    $itemRequest->update(['status' => ItemRequestStatus::Approved, 'approved_at' => now()]);
    $itemRequest->items()->update([
        'category_id' => 1,
        'item_code' => 'ITEM-0001',
        'inventory_coa_id' => 1,
        'sales_coa_id' => 1,
    ]);

    $export = ItemRequestExport::create([
        'item_request_id' => $itemRequest->id,
        'requested_by' => $requester->id,
        'status' => 'queued',
    ]);

    (new ExportItemRequest($export))->handle();

    $this->actingAs($outsider)
        ->get(route('item-requests.exports.download', $export->fresh()))
        ->assertForbidden();
});

it('renders sidebar navigation from config and paginates ready requests on export page', function () {
    $user = User::factory()->create(['department_id' => 1]);
    $user->givePermissionTo(['dashboard.view', 'notification.view', 'item-request.view', 'item-request.export']);

    for ($i = 1; $i <= 11; $i++) {
        $itemRequest = ItemRequest::create([
            'request_no' => 'PAGE-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            'requester_id' => $user->id,
            'department_id' => 1,
            'business_unit_id' => 1,
            'status' => ItemRequestStatus::Approved,
            'current_step' => 'done',
            'submitted_at' => now()->addMinutes($i),
            'validated_at' => now()->addMinutes($i + 1),
            'accounted_at' => now()->addMinutes($i + 2),
            'approved_at' => now()->addMinutes($i + 3),
        ]);

        ItemRequestItem::create([
            'item_request_id' => $itemRequest->id,
            'unit_id' => 1,
            'category_id' => 1,
            'inventory_coa_id' => 1,
            'sales_coa_id' => 2,
            'item_name' => 'Paged Item '.$i,
            'item_type' => 'Goods',
            'item_code' => 'PAGE-ITEM-'.$i,
        ]);
    }

    $this->actingAs($user)
        ->get(route('item-requests.exports.index'))
        ->assertSuccessful()
        ->assertSee('Dashboard')
        ->assertSee('Notifications')
        ->assertSee('Item Requests')
        ->assertSee('Export')
        ->assertSee('PAGE-011')
        ->assertSee('PAGE-002')
        ->assertDontSee('PAGE-001');

    $this->actingAs($user)
        ->get(route('item-requests.exports.index', ['readyPage' => 2]))
        ->assertSuccessful()
        ->assertSee('PAGE-001');
});
