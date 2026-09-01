<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Branch Hubs
        Schema::create('branch_hubs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('city');
            $table->text('address');
            $table->string('phone');
            $table->string('person_in_charge');
            $table->timestamps();
        });

        // 2. Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_code')->unique();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('postal_code')->nullable();
            $table->enum('customer_type', ['individual', 'corporate'])->default('individual');
            $table->timestamps();
        });

        // 3. Vehicles
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('vehicle_type'); // Truck Box, Blind Van, Pickup, Motorcycle
            $table->decimal('capacity_kg', 10, 2)->default(1000);
            $table->enum('status', ['active', 'maintenance', 'in_delivery'])->default('active');
            $table->timestamps();
        });

        // 4. Couriers
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('branch_hub_id')->nullable()->constrained('branch_hubs')->nullOnDelete();
            $table->string('courier_code')->unique();
            $table->string('name');
            $table->string('license_number')->nullable();
            $table->string('phone');
            $table->enum('status', ['available', 'on_duty', 'off'])->default('available');
            $table->timestamps();
        });

        // 5. Tariffs
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('origin_city');
            $table->string('destination_city');
            $table->string('service_type'); // Regular, Express, SameDay
            $table->decimal('price_per_kg', 12, 2);
            $table->decimal('min_weight_kg', 8, 2)->default(1.0);
            $table->string('estimated_days')->default('1-2 Hari');
            $table->timestamps();
        });

        // 6. Delivery Orders (DO Customer)
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('do_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->text('sender_address');
            $table->string('sender_city');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('recipient_address');
            $table->string('recipient_city');
            $table->enum('status', ['draft', 'approved', 'processing', 'shipped', 'delivered', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Delivery Order Items
        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained('delivery_orders')->cascadeOnDelete();
            $table->string('item_name');
            $table->integer('qty');
            $table->string('unit')->default('Pcs');
            $table->decimal('weight_kg', 8, 2)->default(1.0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Shipments (AWB / Resi)
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->foreignId('delivery_order_id')->nullable()->constrained('delivery_orders')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('origin_hub_id')->nullable()->constrained('branch_hubs')->nullOnDelete();
            $table->foreignId('destination_hub_id')->nullable()->constrained('branch_hubs')->nullOnDelete();
            $table->foreignId('current_hub_id')->nullable()->constrained('branch_hubs')->nullOnDelete();

            $table->string('sender_name');
            $table->string('sender_phone');
            $table->text('sender_address');
            $table->string('sender_city');

            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('recipient_address');
            $table->string('recipient_city');

            $table->string('service_type')->default('Regular');
            $table->decimal('weight_kg', 8, 2)->default(1.0);
            $table->string('dimensions')->nullable();
            $table->decimal('declared_value', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('insurance_fee', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('payment_method')->default('Cash');
            $table->string('payment_status')->default('Pending');

            $table->enum('status', [
                'draft',
                'picked_up',
                'in_sorting_hub',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'failed'
            ])->default('draft');

            // ePOD (Electronic Proof of Delivery)
            $table->string('pod_receiver_name')->nullable();
            $table->string('pod_receiver_relation')->nullable();
            $table->string('pod_photo')->nullable();
            $table->longText('pod_signature')->nullable();
            $table->dateTime('pod_delivered_at')->nullable();
            $table->text('pod_notes')->nullable();

            $table->timestamps();
        });

        // 9. Courier Assignments
        Schema::create('courier_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('assignment_number')->unique();
            $table->foreignId('courier_id')->constrained('couriers')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->enum('assignment_type', ['pickup', 'delivery'])->default('delivery');
            $table->date('assignment_date');
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 10. Courier Assignment Items
        Schema::create('courier_assignment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_assignment_id')->constrained('courier_assignments')->cascadeOnDelete();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });

        // 11. Shipment Tracking Logs
        Schema::create('shipment_tracking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('status');
            $table->string('location');
            $table->text('description');
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking_logs');
        Schema::dropIfExists('courier_assignment_items');
        Schema::dropIfExists('courier_assignments');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('delivery_order_items');
        Schema::dropIfExists('delivery_orders');
        Schema::dropIfExists('tariffs');
        Schema::dropIfExists('couriers');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('branch_hubs');
    }
};
