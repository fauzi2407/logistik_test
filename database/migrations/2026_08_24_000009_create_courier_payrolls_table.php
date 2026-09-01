<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_code')->unique();
            $table->foreignId('courier_id')->constrained('couriers')->onDelete('cascade');
            $table->integer('period_month');
            $table->integer('period_year');
            $table->integer('total_deliveries')->default(0);
            $table->decimal('commission_per_delivery', 12, 2)->default(0);
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->decimal('bonus_amount', 12, 2)->default(0);
            $table->decimal('deduction_amount', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['courier_id', 'period_year', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_payrolls');
    }
};
