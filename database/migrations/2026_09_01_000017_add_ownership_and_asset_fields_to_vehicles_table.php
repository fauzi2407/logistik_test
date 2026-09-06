<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'ownership_type')) {
                $table->string('ownership_type', 30)->default('company')->after('vehicle_type'); // 'company' (Milik Perusahaan) or 'personal' (Milik Sendiri)
            }
            if (!Schema::hasColumn('vehicles', 'asset_value')) {
                $table->decimal('asset_value', 15, 2)->default(0)->after('capacity_kg');
            }
            if (!Schema::hasColumn('vehicles', 'journal_entry_id')) {
                $table->foreignId('journal_entry_id')->nullable()->after('status')->constrained('journal_entries')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['journal_entry_id']);
            $table->dropColumn(['ownership_type', 'asset_value', 'journal_entry_id']);
        });
    }
};
