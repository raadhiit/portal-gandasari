<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\ItemRequest\Enums\ItemRequestStatus;
use Modules\ItemRequest\Models\ItemRequest;
use Modules\ItemRequest\Models\ItemRequestItem;

class ItemRequestExportDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'demo.requester@gandasari.com'],
            ['name' => 'Demo Requester', 'password' => bcrypt('password'), 'department_id' => 1, 'email_verified_at' => now()]
        );

        for ($i = 1; $i <= 20; $i++) {
            $requestNo = 'EXP-DEMO-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);

            $itemRequest = ItemRequest::updateOrCreate(
                ['request_no' => $requestNo],
                [
                    'requester_id' => $user->id,
                    'department_id' => 1,
                    'business_unit_id' => 1,
                    'status' => ItemRequestStatus::WaitingForIT,
                    'current_step' => 'it',
                    'submitted_at' => now(),
                    'validated_at' => now(),
                    'accounted_at' => now(),
                    'notes' => 'Bulk demo request '.$i,
                ]
            );

            $itemRequest->items()->delete();

            for ($j = 1; $j <= 5; $j++) {
                ItemRequestItem::create([
                    'item_request_id' => $itemRequest->id,
                    'unit_id' => 1,
                    'category_id' => 1,
                    'inventory_coa_id' => 1,
                    'sales_coa_id' => 2,
                    'item_name' => 'Demo Item '.$j,
                    'item_type' => 'Goods',
                    'item_code' => 'DEMO-'.$i.'-'.$j,
                ]);
            }
        }
    }
}
