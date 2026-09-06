<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY COLUMN status ENUM('draft', 'pending', 'picked_up', 'in_sorting_hub', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'cancelled') NOT NULL DEFAULT 'draft'");
    }
};
