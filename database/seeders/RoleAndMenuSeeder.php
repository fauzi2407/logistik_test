<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Database\Seeder;

class RoleAndMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        $rolesData = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Super Admin dengan hak akses penuh ke seluruh fitur dan sistem',
                'is_system' => true,
            ],
            [
                'name' => 'Staf Operasional Hub',
                'slug' => 'staff',
                'description' => 'Staf Operasional yang mengelola Customer, DO, AWB, dan Manifes Kurir',
                'is_system' => true,
            ],
            [
                'name' => 'Kurir / Driver',
                'slug' => 'courier',
                'description' => 'Kurir lapangan yang bertugas menjemput dan mengantar paket',
                'is_system' => true,
            ],
            [
                'name' => 'Customer / Klien',
                'slug' => 'customer',
                'description' => 'Pelanggan pengirim barang yang membuat Delivery Order & melihat invoice',
                'is_system' => true,
            ],
        ];

        $roles = [];
        foreach ($rolesData as $r) {
            $roles[$r['slug']] = Role::firstOrCreate(['slug' => $r['slug']], $r);
        }

        // 2. Seed Menus
        $menusData = [
            ['title' => 'Dashboard Analytics', 'route' => 'dashboard', 'icon' => 'fa-solid fa-chart-line', 'sort_order' => 1],
            ['title' => 'Laporan Keputusan SLA', 'route' => 'reports.index', 'icon' => 'fa-solid fa-file-invoice', 'sort_order' => 2],
            ['title' => 'Data Customer & Bank', 'route' => 'customers.index', 'icon' => 'fa-solid fa-building-user', 'sort_order' => 3],
            ['title' => 'DO Customer / Surat Jalan', 'route' => 'delivery-orders.index', 'icon' => 'fa-solid fa-file-contract', 'sort_order' => 4],
            ['title' => 'Invoice & Penagihan', 'route' => 'invoices.index', 'icon' => 'fa-solid fa-receipt', 'sort_order' => 5],
            ['title' => 'Resi Pengiriman (AWB)', 'route' => 'shipments.index', 'icon' => 'fa-solid fa-box', 'sort_order' => 6],
            ['title' => 'Penugasan Kurir & Manifes', 'route' => 'courier-assignments.index', 'icon' => 'fa-solid fa-clipboard-list', 'sort_order' => 7],
            ['title' => 'Penggajian & Komisi Kurir', 'route' => 'courier-payrolls.index', 'icon' => 'fa-solid fa-money-bill-wave', 'sort_order' => 8],
            ['title' => 'Pembelian Barang (PO)', 'route' => 'purchases.index', 'icon' => 'fa-solid fa-cart-shopping', 'sort_order' => 9],
            ['title' => 'Master Vendor / Supplier', 'route' => 'vendors.index', 'icon' => 'fa-solid fa-truck-field', 'sort_order' => 10],
            ['title' => 'Master COA (Bagan Akun)', 'route' => 'accounting.coa.index', 'icon' => 'fa-solid fa-list-ol', 'sort_order' => 9],
            ['title' => 'Saldo Awal Akun', 'route' => 'accounting.initial-balances.index', 'icon' => 'fa-solid fa-scale-balanced', 'sort_order' => 10],
            ['title' => 'Penjurnalan (Jurnal Umum)', 'route' => 'accounting.journals.index', 'icon' => 'fa-solid fa-book-journal-whills', 'sort_order' => 11],
            ['title' => 'Buku Besar (General Ledger)', 'route' => 'accounting.ledger.index', 'icon' => 'fa-solid fa-book-bookmark', 'sort_order' => 12],
            ['title' => 'Laporan Laba Rugi (P&L)', 'route' => 'accounting.profit-loss.index', 'icon' => 'fa-solid fa-chart-pie', 'sort_order' => 13],
            ['title' => 'Laporan Neraca (Balance Sheet)', 'route' => 'accounting.balance-sheet.index', 'icon' => 'fa-solid fa-building-columns', 'sort_order' => 14],
            ['title' => 'Tutup Buku Akhir Tahun', 'route' => 'accounting.closing.index', 'icon' => 'fa-solid fa-calendar-check', 'sort_order' => 15],
            ['title' => 'Master Transit Hub', 'route' => 'branch-hubs.index', 'icon' => 'fa-solid fa-warehouse', 'sort_order' => 16],
            ['title' => 'Manajemen Pengguna', 'route' => 'users.index', 'icon' => 'fa-solid fa-users-gear', 'sort_order' => 17],
            ['title' => 'Manajemen Menu & Role', 'route' => 'roles.index', 'icon' => 'fa-solid fa-shield-halved', 'sort_order' => 18],
            ['title' => 'Master Data Kurir', 'route' => 'couriers.index', 'icon' => 'fa-solid fa-user-gear', 'sort_order' => 19],
            ['title' => 'Armada Kendaraan', 'route' => 'vehicles.index', 'icon' => 'fa-solid fa-truck', 'sort_order' => 20],
            ['title' => 'Master Tarif & Ongkir', 'route' => 'tariffs.index', 'icon' => 'fa-solid fa-tags', 'sort_order' => 21],
            ['title' => 'Master Wilayah & Kode Pos', 'route' => 'regions.index', 'icon' => 'fa-solid fa-map-location-dot', 'sort_order' => 22],
            ['title' => 'Pengaturan Web & Logo', 'route' => 'settings.index', 'icon' => 'fa-solid fa-gear', 'sort_order' => 23],
        ];

        $menus = [];
        foreach ($menusData as $m) {
            $menus[$m['route']] = Menu::firstOrCreate(['route' => $m['route']], $m);
        }

        // 3. Grant Default Permissions
        foreach ($roles as $slug => $role) {
            foreach ($menus as $route => $menu) {
                $canView = true;
                $canCreate = true;
                $canEdit = true;
                $canDelete = true;

                if ($slug === 'staff') {
                    if (in_array($route, ['settings.index', 'roles.index'])) {
                        $canView = false;
                        $canCreate = false;
                        $canEdit = false;
                        $canDelete = false;
                    }
                } elseif ($slug === 'courier') {
                    if (!in_array($route, ['dashboard'])) {
                        $canView = false;
                        $canCreate = false;
                        $canEdit = false;
                        $canDelete = false;
                    }
                } elseif ($slug === 'customer') {
                    if (!in_array($route, ['dashboard', 'delivery-orders.index', 'shipments.index', 'invoices.index'])) {
                        $canView = false;
                        $canCreate = false;
                        $canEdit = false;
                        $canDelete = false;
                    } elseif ($route === 'invoices.index') {
                        $canCreate = false;
                        $canEdit = false;
                        $canDelete = false;
                    }
                }

                RoleMenuPermission::firstOrCreate(
                    ['role_id' => $role->id, 'menu_id' => $menu->id],
                    [
                        'can_view' => $canView,
                        'can_create' => $canCreate,
                        'can_edit' => $canEdit,
                        'can_delete' => $canDelete,
                    ]
                );
            }
        }
    }
}
