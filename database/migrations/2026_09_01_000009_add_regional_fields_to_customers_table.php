<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('province')->nullable()->after('address');
            $table->string('district')->nullable()->after('city');
            $table->string('subdistrict')->nullable()->after('district');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['province', 'district', 'subdistrict']);
        });
    }
};
