<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $appName = \App\Models\AppSetting::get('app_name', 'Radja Express Logistics');
        $appTagline = \App\Models\AppSetting::get('app_tagline', 'Logistics & Supply Chain Management System');
        $appPhone = \App\Models\AppSetting::get('company_phone', '021-7654321');
        $appEmail = \App\Models\AppSetting::get('company_email', 'info@logistik.com');
        $appAddress = \App\Models\AppSetting::get('company_address', 'Jl. Logistik Utama No. 88, Jakarta Selatan');
        $appLogoUrl = \App\Models\AppSetting::get('app_logo_url', null);
    @endphp
    <title>PURCHASE ORDER - {{ $purchase->purchase_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { font-size: 11pt; background: #fff !important; }
            .no-print { display: none !important; }
            .print-border { border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans p-4 md:p-8 min-h-screen">

    <!-- Action Bar Print -->
    <div class="max-w-3xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ route('purchases.show', $purchase->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Detail
        </a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg transition">
            <i class="fa-solid fa-print mr-1.5"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Official PO Document Card -->
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200 print-border space-y-6">
        <!-- Header Company -->
        <div class="flex items-center justify-between border-b border-slate-200 pb-5">
            <div class="flex items-center space-x-3">
                @if($appLogoUrl)
                    <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="w-12 h-12 object-contain">
                @else
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $appName }}</h1>
                    <p class="text-xs text-slate-500">{{ $appAddress }}</p>
                    <p class="text-xs text-slate-500">Telp: {{ $appPhone }} • Email: {{ $appEmail }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">SURAT PURCHASE ORDER (PO)</div>
                <div class="text-sm font-black font-mono text-indigo-600 mt-0.5">{{ $purchase->purchase_number }}</div>
                <div class="text-xs text-slate-500 mt-1">Tanggal: <strong>{{ $purchase->purchase_date->format('d F Y') }}</strong></div>
            </div>
        </div>

        <!-- Vendor & Ship To Grid -->
        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
            <div>
                <div class="text-slate-400 font-bold uppercase">TO (VENDOR SUPPLIER):</div>
                <div class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $purchase->vendor->name }}</div>
                <div class="text-slate-600 mt-0.5">PIC: {{ $purchase->vendor->contact_person ?: '-' }} ({{ $purchase->vendor->phone ?: '-' }})</div>
                <div class="text-slate-500 text-[11px] mt-0.5">{{ $purchase->vendor->address ?: '-' }}</div>
            </div>
            <div>
                <div class="text-slate-400 font-bold uppercase">SHIP TO (TENTANG ALAMAT GUDANG):</div>
                <div class="font-bold text-slate-800 mt-0.5">Gudang Utama PT {{ $appName }}</div>
                <div class="text-slate-600 mt-0.5">{{ $appAddress }}</div>
                <div class="text-slate-600 mt-0.5">Metode Bayar: <strong>{{ $purchase->payment_method ?: 'Transfer Bank' }}</strong></div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="space-y-3">
            <h2 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                Rincian Barang & Pengadaan Perusahaan
            </h2>

            <table class="w-full text-xs">
                <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">#</th>
                        <th class="py-2.5 px-3">Deskripsi Barang</th>
                        <th class="py-2.5 px-3 text-center">Qty</th>
                        <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                        <th class="py-2.5 px-3 text-right">Total Harga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($purchase->items as $it)
                        <tr>
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono">{{ $loop->iteration }}</td>
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-slate-900">{{ $it->item_name }}</div>
                                <div class="text-[10px] text-slate-500">Kategori: {{ $it->category }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-slate-800">{{ $it->quantity }} {{ $it->unit }}</td>
                            <td class="py-2.5 px-3 text-right font-mono">Rp {{ number_format($it->unit_price, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($it->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-slate-300 font-bold">
                    <tr>
                        <td colspan="4" class="py-2 px-3 text-right text-slate-600">Subtotal:</td>
                        <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($purchase->discount_amount > 0)
                        <tr>
                            <td colspan="4" class="py-2 px-3 text-right text-slate-600">Diskon:</td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-rose-600">- Rp {{ number_format($purchase->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($purchase->tax_amount > 0)
                        <tr>
                            <td colspan="4" class="py-2 px-3 text-right text-slate-600">PPN:</td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-indigo-600">+ Rp {{ number_format($purchase->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="bg-emerald-50 text-sm font-black">
                        <td colspan="4" class="py-3 px-3 text-right text-emerald-900 uppercase">TOTAL HARGA PO:</td>
                        <td class="py-3 px-3 text-right font-mono text-emerald-700">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($purchase->notes)
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                <span class="font-bold text-slate-700">Catatan / Syarat Ketentuan:</span> {{ $purchase->notes }}
            </div>
        @endif

        <!-- Signature Block -->
        <div class="pt-6 border-t border-slate-200 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <p class="text-slate-400 font-bold mb-12">Disetujui Oleh (Vendor / Supplier),</p>
                <p class="font-extrabold text-slate-900 underline">{{ $purchase->vendor->name }}</p>
                <p class="text-[10px] text-slate-400">Supplier Procurement</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold mb-12">Dibuat Oleh (Purchasing Department),</p>
                <p class="font-extrabold text-slate-900 underline">PT {{ $appName }}</p>
                <p class="text-[10px] text-slate-400">Procurement & Purchasing Manager</p>
            </div>
        </div>
    </div>
</body>
</html>
