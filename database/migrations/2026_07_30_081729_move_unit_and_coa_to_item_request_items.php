<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('item_request_items', 'unit_id')) {
            Schema::table('item_request_items', function (Blueprint $table) {
                $table->foreignId('unit_id')->nullable()->after('item_request_id')->constrained('units')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('item_request_items', 'inventory_coa_id')) {
            Schema::table('item_request_items', function (Blueprint $table) {
                $table->foreignId('inventory_coa_id')->nullable()->after('category_id')->constrained('coas')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('item_request_items', 'sales_coa_id')) {
            Schema::table('item_request_items', function (Blueprint $table) {
                $table->foreignId('sales_coa_id')->nullable()->after('inventory_coa_id')->constrained('coas')->nullOnDelete();
            });
        }

        $columns = array_values(array_filter([
            Schema::hasColumn('item_requests', 'unit_id') ? 'unit_id' : null,
            Schema::hasColumn('item_requests', 'warehouse_id') ? 'warehouse_id' : null,
            Schema::hasColumn('item_requests', 'coa_id') ? 'coa_id' : null,
        ]));

        if ($columns !== []) {
            Schema::table('item_requests', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('item_requests', 'unit_id')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->foreignId('unit_id')->nullable()->after('business_unit_id')->constrained('units')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('item_requests', 'warehouse_id')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->foreignId('warehouse_id')->nullable()->after('unit_id')->constrained('warehouses')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('item_requests', 'coa_id')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->foreignId('coa_id')->nullable()->after('assigned_accounting_id')->constrained('coas')->nullOnDelete();
            });
        }

        $columns = array_values(array_filter([
            Schema::hasColumn('item_request_items', 'unit_id') ? 'unit_id' : null,
            Schema::hasColumn('item_request_items', 'inventory_coa_id') ? 'inventory_coa_id' : null,
            Schema::hasColumn('item_request_items', 'sales_coa_id') ? 'sales_coa_id' : null,
        ]));

        if ($columns !== []) {
            Schema::table('item_request_items', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
