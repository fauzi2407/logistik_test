<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->text('address')->nullable()->after('phone');
            $table->string('emergency_phone')->nullable()->after('address');
            $table->integer('age')->nullable()->after('emergency_phone');
            $table->decimal('commission_per_delivery', 12, 2)->default(5000)->after('age');
        });
    }

    public function down(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn(['address', 'emergency_phone', 'age', 'commission_per_delivery']);
        });
    }
};
