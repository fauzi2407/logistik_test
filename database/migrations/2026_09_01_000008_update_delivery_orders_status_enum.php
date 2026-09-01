<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE delivery_orders MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE delivery_orders MODIFY COLUMN status ENUM('draft', 'approved', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'draft'");
    }
};
