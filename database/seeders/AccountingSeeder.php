<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets (1000)
            ['account_code' => '1101', 'account_name' => 'Kas Operasional Utama', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'description' => 'Kas tunai kantor pusat & hub'],
            ['account_code' => '1102', 'account_name' => 'Bank BCA Operasional', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'description' => 'Rekening utama penampung ongkir & invoice'],
            ['account_code' => '1103', 'account_name' => 'Bank Mandiri Transaksi', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'description' => 'Rekening operasional penerimaan'],
            ['account_code' => '1104', 'account_name' => 'Piutang Pengiriman Customer', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'description' => 'Piutang tagihan invoice pengiriman customer'],
            ['account_code' => '1201', 'account_name' => 'Armada Kendaraan & Inventaris', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'description' => 'Nilai aset mobil, motor, & peralatan hub'],
            ['account_code' => '1202', 'account_name' => 'Akumulasi Penyusutan Kendaraan', 'account_type' => 'Asset', 'normal_balance' => 'Credit', 'description' => 'Akumulasi penyusutan kendaraan operasional'],

            // Liabilities (2000)
            ['account_code' => '2101', 'account_name' => 'Utang Usaha / Vendor', 'account_type' => 'Liability', 'normal_balance' => 'Credit', 'description' => 'Kewajiban pembayaran supplier & vendor logistik'],
            ['account_code' => '2102', 'account_name' => 'Utang Komisi Pengantaran Kurir', 'account_type' => 'Liability', 'normal_balance' => 'Credit', 'description' => 'Akrual tagihan komisi kurir terutang'],
            ['account_code' => '2103', 'account_name' => 'Utang Pajak (PPN/PPh)', 'account_type' => 'Liability', 'normal_balance' => 'Credit', 'description' => 'Kewajiban perpajakan terutang'],

            // Equity (3000)
            ['account_code' => '3101', 'account_name' => 'Modal Usaha Pemilik', 'account_type' => 'Equity', 'normal_balance' => 'Credit', 'description' => 'Modal disetor pemilik perusahaan'],
            ['account_code' => '3201', 'account_name' => 'Laba Ditahan (Retained Earnings)', 'account_type' => 'Equity', 'normal_balance' => 'Credit', 'description' => 'Akumulasi laba tahun-tahun sebelumnya'],
            ['account_code' => '3301', 'account_name' => 'Laba Tahun Berjalan', 'account_type' => 'Equity', 'normal_balance' => 'Credit', 'description' => 'Laba bersih berjalan tahun aktif'],

            // Revenue (4000)
            ['account_code' => '4101', 'account_name' => 'Pendapatan Jasa Pengiriman (Ongkir)', 'account_type' => 'Revenue', 'normal_balance' => 'Credit', 'description' => 'Pendapatan utama dari pengiriman resi AWB'],
            ['account_code' => '4102', 'account_name' => 'Pendapatan Premium Asuransi', 'account_type' => 'Revenue', 'normal_balance' => 'Credit', 'description' => 'Pendapatan premi asuransi barang'],
            ['account_code' => '4201', 'account_name' => 'Pendapatan Operasional Lainnya', 'account_type' => 'Revenue', 'normal_balance' => 'Credit', 'description' => 'Pendapatan penanganan & biaya pengemasan'],

            // Expenses (5000)
            ['account_code' => '5101', 'account_name' => 'Beban Komisi Pengantaran Kurir', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'description' => 'Beban komisi kurir per paket terantar'],
            ['account_code' => '5102', 'account_name' => 'Beban Bahan Bakar (BBM) & Tol', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'description' => 'Beban BBM & e-Toll pengiriman'],
            ['account_code' => '5103', 'account_name' => 'Beban Perawatan & Servis Armada', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'description' => 'Biaya perbaikan & pemeliharaan kendaraan'],
            ['account_code' => '5201', 'account_name' => 'Beban Gaji Staf & Karyawan', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'description' => 'Beban gaji pegawai non-kurir'],
            ['account_code' => '5202', 'account_name' => 'Beban Sewa Gudang & Transit Hub', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'description' => 'Sewa bangunan gudang transit'],
            ['account_code' => '5203', 'account_name' => 'Beban Listrik, Air & Operasional Kantor', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'description' => 'Beban utilitas operasional kantor'],
        ];

        foreach ($accounts as $acc) {
            ChartOfAccount::firstOrCreate(['account_code' => $acc['account_code']], $acc);
        }
    }
}
