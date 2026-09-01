<?php

namespace Database\Seeders;

use App\Models\BranchHub;
use App\Models\Courier;
use App\Models\CourierAssignment;
use App\Models\CourierAssignmentItem;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use App\Models\Tariff;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LogisticsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::updateOrCreate(['email' => 'admin@logistik.com'], [
            'name' => 'Administrator Logistik',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        $staff = User::updateOrCreate(['email' => 'staff@logistik.com'], [
            'name' => 'Staf Operasional Hub',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '081234567891',
        ]);

        $kurirUser = User::updateOrCreate(['email' => 'kurir1@logistik.com'], [
            'name' => 'Budi Santoso (Driver)',
            'password' => Hash::make('password'),
            'role' => 'courier',
            'phone' => '081298765432',
        ]);

        $customerUser = User::updateOrCreate(['email' => 'customer@indofood.com'], [
            'name' => 'PT Indofood Sukses Makmur',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '0215554321',
        ]);

        // 2. Hubs
        $hubJkt = BranchHub::updateOrCreate(['name' => 'Hub Utama Jakarta Selatan'], [
            'code' => 'HUB-JKT01',
            'city' => 'Jakarta Selatan',
            'address' => 'Jl. Logistik Utama No. 88, Cilandak',
            'phone' => '0217654321',
            'person_in_charge' => 'Hendra Setiawan',
        ]);

        $hubBdg = BranchHub::updateOrCreate(['name' => 'Hub Cabang Bandung'], [
            'code' => 'HUB-BDG01',
            'city' => 'Bandung',
            'address' => 'Jl. Soekarno Hatta No. 450',
            'phone' => '0227890123',
            'person_in_charge' => 'Agus Priyanto',
        ]);

        $hubSby = BranchHub::updateOrCreate(['name' => 'Hub Transit Surabaya'], [
            'code' => 'HUB-SBY01',
            'city' => 'Surabaya',
            'address' => 'Jl. Raya Rungkut Industri No. 12',
            'phone' => '0318765432',
            'person_in_charge' => 'Bambang Hartono',
        ]);

        // 3. Vehicles
        $vh1 = Vehicle::updateOrCreate(['plate_number' => 'B 9123 LGS'], [
            'vehicle_type' => 'Truck Box Hino 4-Wheel',
            'capacity_kg' => 2500,
            'status' => 'active',
        ]);

        $vh2 = Vehicle::updateOrCreate(['plate_number' => 'B 9876 POD'], [
            'vehicle_type' => 'Blind Van Gran Max',
            'capacity_kg' => 800,
            'status' => 'active',
        ]);

        // 4. Couriers
        $cr1 = Courier::updateOrCreate(['courier_code' => 'KUR-001'], [
            'user_id' => $kurirUser->id,
            'branch_hub_id' => $hubJkt->id,
            'vehicle_id' => $vh1->id,
            'name' => 'Budi Santoso',
            'phone' => '081298765432',
            'license_number' => 'SIM-B1-889922',
            'status' => 'on_duty',
        ]);

        $cr2 = Courier::updateOrCreate(['courier_code' => 'KUR-002'], [
            'branch_hub_id' => $hubJkt->id,
            'vehicle_id' => $vh2->id,
            'name' => 'Rahmat Hidayat',
            'phone' => '085811223344',
            'license_number' => 'SIM-A-998811',
            'status' => 'available',
        ]);

        // 5. Customers (including Banks)
        $custBankBCA = Customer::updateOrCreate(['customer_code' => 'CUST-BCA01'], [
            'name' => 'PT Bank Central Asia Tbk (BCA Card Center)',
            'company_name' => 'BCA Card & Billing Distribution',
            'customer_type' => 'corporate',
            'phone' => '02123588000',
            'email' => 'distribution@bca.co.id',
            'city' => 'Jakarta Selatan',
            'address' => 'Menara BCA Lt. 18, Jl. M.H. Thamrin No. 1',
            'postal_code' => '10310',
        ]);

        $custBankMandiri = Customer::updateOrCreate(['customer_code' => 'CUST-MDR01'], [
            'name' => 'PT Bank Mandiri (Persero) Tbk',
            'company_name' => 'Bank Mandiri Credit Card Unit',
            'customer_type' => 'corporate',
            'phone' => '0215265000',
            'email' => 'cc-logistics@bankmandiri.co.id',
            'city' => 'Jakarta Selatan',
            'address' => 'Plaza Mandiri, Jl. Jend. Gatot Subroto Kav. 36',
            'postal_code' => '12190',
        ]);

        $custIndofood = Customer::updateOrCreate(['customer_code' => 'CUST-INDF01'], [
            'name' => 'PT Indofood Sukses Makmur',
            'company_name' => 'Indofood Distribution Center',
            'customer_type' => 'corporate',
            'phone' => '0215554321',
            'email' => 'customer@indofood.com',
            'city' => 'Jakarta Selatan',
            'address' => 'Indofood Tower Lt. 23, Jl. Jend. Sudirman Kav. 76',
            'postal_code' => '12910',
        ]);

        // 6. Tariffs
        Tariff::updateOrCreate(['origin_city' => 'Jakarta Selatan', 'destination_city' => 'Bandung', 'service_type' => 'Regular'], [
            'price_per_kg' => 12000,
            'min_weight_kg' => 1.0,
            'estimated_days' => '2-3 Hari',
        ]);
        Tariff::updateOrCreate(['origin_city' => 'Jakarta Selatan', 'destination_city' => 'Bandung', 'service_type' => 'Express'], [
            'price_per_kg' => 20000,
            'min_weight_kg' => 1.0,
            'estimated_days' => '1 Hari (Next Day)',
        ]);
        Tariff::updateOrCreate(['origin_city' => 'Jakarta Selatan', 'destination_city' => 'Surabaya', 'service_type' => 'Regular'], [
            'price_per_kg' => 18000,
            'min_weight_kg' => 1.0,
            'estimated_days' => '3-4 Hari',
        ]);
        Tariff::updateOrCreate(['origin_city' => 'Jakarta Selatan', 'destination_city' => 'Surabaya', 'service_type' => 'Express'], [
            'price_per_kg' => 32000,
            'min_weight_kg' => 1.0,
            'estimated_days' => '1-2 Hari',
        ]);

        // 7. Multi-Destination Bank Delivery Order (BCA Card Center Batch)
        $doBank = DeliveryOrder::updateOrCreate(['do_number' => 'DO-BCA-20260822-0001'], [
            'customer_id' => $custBankBCA->id,
            'order_date' => now()->subDays(1),
            'delivery_date' => now(),
            'sender_name' => 'BCA Card & Billing Distribution',
            'sender_phone' => '02123588000',
            'sender_address' => 'Menara BCA Lt. 18, Jl. M.H. Thamrin No. 1',
            'sender_city' => 'Jakarta Selatan',
            'recipient_name' => 'Multi Tujuan (3 Penerima Kartu Kredit)',
            'recipient_phone' => '02123588000',
            'recipient_address' => 'Multi Lokasi Penerima Bank',
            'recipient_city' => 'Multi Kota',
            'status' => 'processing',
            'notes' => 'Batch Pengiriman Kartu Kredit BCA Visa/MasterCard dan Tagihan Bulanan Agustus',
        ]);

        // Recipients list for Bank DO
        $bankRecipients = [
            [
                'recipient_name' => 'Bpk. Anton Wijaya',
                'recipient_phone' => '081299887766',
                'recipient_city' => 'Bandung',
                'recipient_address' => 'Jl. Dago No. 120, Coblong, Bandung',
                'account_ref' => 'CC-4512-8899-01',
                'item_name' => 'Kartu Kredit BCA Visa Platinum',
                'weight_kg' => 0.2,
                'trk' => 'TRK-20260822-0001',
            ],
            [
                'recipient_name' => 'Ibu Dewi Lestari',
                'recipient_phone' => '081377665544',
                'recipient_city' => 'Surabaya',
                'recipient_address' => 'Jl. Pemuda No. 45, Genteng, Surabaya',
                'account_ref' => 'CC-4512-8899-02',
                'item_name' => 'Kartu Kredit BCA Everyday Card',
                'weight_kg' => 0.2,
                'trk' => 'TRK-20260822-0002',
            ],
            [
                'recipient_name' => 'Bpk. Rahmat Subagyo',
                'recipient_phone' => '081744556677',
                'recipient_city' => 'Bandung',
                'recipient_address' => 'Jl. Riau No. 88, Cibeunying, Bandung',
                'account_ref' => 'CC-4512-8899-03',
                'item_name' => 'Tagihan Kartu Kredit BCA Solitaire',
                'weight_kg' => 0.1,
                'trk' => 'TRK-20260822-0003',
            ],
        ];

        foreach ($bankRecipients as $r) {
            $sh = Shipment::updateOrCreate(['tracking_number' => $r['trk']], [
                'delivery_order_id' => $doBank->id,
                'customer_id' => $custBankBCA->id,
                'origin_hub_id' => $hubJkt->id,
                'destination_hub_id' => $r['recipient_city'] == 'Bandung' ? $hubBdg->id : $hubSby->id,
                'courier_id' => $cr1->id,
                'vehicle_id' => $vh1->id,
                'sender_name' => 'BCA Card Center',
                'sender_phone' => '02123588000',
                'sender_address' => 'Menara BCA Lt. 18, Jl. M.H. Thamrin No. 1',
                'sender_city' => 'Jakarta Selatan',
                'recipient_name' => $r['recipient_name'],
                'recipient_phone' => $r['recipient_phone'],
                'recipient_address' => $r['recipient_address'],
                'recipient_city' => $r['recipient_city'],
                'service_type' => 'Express',
                'weight_kg' => $r['weight_kg'],
                'shipping_fee' => 20000,
                'total_amount' => 20000,
                'payment_method' => 'Transfer',
                'payment_status' => 'Paid',
                'status' => 'out_for_delivery',
            ]);

            DeliveryOrderItem::updateOrCreate([
                'delivery_order_id' => $doBank->id,
                'tracking_number' => $r['trk'],
            ], [
                'recipient_name' => $r['recipient_name'],
                'recipient_phone' => $r['recipient_phone'],
                'recipient_city' => $r['recipient_city'],
                'recipient_address' => $r['recipient_address'],
                'account_ref' => $r['account_ref'],
                'item_name' => $r['item_name'],
                'qty' => 1,
                'unit' => 'Amplop',
                'weight_kg' => $r['weight_kg'],
            ]);

            ShipmentTrackingLog::updateOrCreate([
                'shipment_id' => $sh->id,
                'status' => 'out_for_delivery',
            ], [
                'location' => $r['recipient_city'],
                'description' => "Paket {$r['item_name']} (Ref: {$r['account_ref']}) sedang diantar oleh kurir Budi Santoso.",
                'updated_by_user_id' => $admin->id,
            ]);
        }

        // 8. Courier Assignment
        $assignment = CourierAssignment::updateOrCreate(['assignment_number' => 'ASN-20260822-0001'], [
            'courier_id' => $cr1->id,
            'vehicle_id' => $vh1->id,
            'assignment_type' => 'delivery',
            'assignment_date' => now(),
            'status' => 'in_progress',
            'notes' => 'Penugasan Pengantaran Dokumen & Kartu Kredit Bank BCA',
        ]);

        foreach ($bankRecipients as $r) {
            $sh = Shipment::where('tracking_number', $r['trk'])->first();
            if ($sh) {
                CourierAssignmentItem::updateOrCreate([
                    'courier_assignment_id' => $assignment->id,
                    'shipment_id' => $sh->id,
                ], [
                    'status' => 'pending',
                ]);
            }
        }
    }
}
