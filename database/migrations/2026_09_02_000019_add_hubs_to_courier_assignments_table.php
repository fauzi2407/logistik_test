<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('courier_assignments', 'origin_hub_id')) {
                $table->foreignId('origin_hub_id')->nullable()->after('assignment_type')->constrained('branch_hubs')->nullOnDelete();
            }
            if (!Schema::hasColumn('courier_assignments', 'destination_hub_id')) {
                $table->foreignId('destination_hub_id')->nullable()->after('origin_hub_id')->constrained('branch_hubs')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('courier_assignments', function (Blueprint $table) {
            $table->dropForeign(['origin_hub_id']);
            $table->dropForeign(['destination_hub_id']);
            $table->dropColumn(['origin_hub_id', 'destination_hub_id']);
        });
    }
};
