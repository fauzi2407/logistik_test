@extends('layouts.app')

@section('title', 'Manajemen Invoice Penagihan')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Invoice & Penagihan Pengiriman</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola penagihan tagihan ongkir pengiriman barang kepada customer / perusahaan.</p>
        </div>
        @if(!Auth::user()->isCustomer())
            <a href="{{ route('invoices.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center justify-center space-x-1.5">
                <i class="fa-solid fa-file-circle-plus"></i>
                <span>+ Terbit Invoice Baru</span>
            </a>
        @endif
    </div>

    <!-- Summary Stats Widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Invoice Diterbitkan</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalInvoiceCount, 0, ',', '.') }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-500">Total Belum Dibayar (Unpaid)</div>
                <div class="text-2xl font-black text-amber-600 mt-1">Rp {{ number_format($totalUnpaidAmount, 0, ',', '.') }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-500">Total Lunas Pembayaran (Paid)</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('invoices.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari Invoice</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="No Invoice, nama customer, no. DO..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Pembayaran</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Semua Status --</option>
                    <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>Belum Dibayar (Unpaid)</option>
                    <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                </button>
                @if($search || $status)
                    <a href="{{ route('invoices.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">No. Invoice</th>
                        <th class="p-4">Customer Pengirim</th>
                        <th class="p-4">Ref DO Surat Jalan</th>
                        <th class="p-4">Tgl Invoice & Jatuh Tempo</th>
                        <th class="p-4">Total Tagihan</th>
                        <th class="p-4">Status Pembayaran</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="hover:underline">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td class="p-4">
                                <div class="font-extrabold text-slate-900">{{ $inv->customer ? ($inv->customer->company_name ?: $inv->customer->name) : 'Customer Umum' }}</div>
                                <div class="text-[11px] text-slate-500"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $inv->customer ? $inv->customer->phone : '-' }}</div>
                            </td>
                            <td class="p-4">
                                @if($inv->deliveryOrder)
                                    <a href="{{ route('delivery-orders.show', $inv->deliveryOrder->id) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600">
                                        {{ $inv->deliveryOrder->do_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-700">
                                <div><i class="fa-solid fa-calendar text-slate-400 mr-1 text-[10px]"></i> {{ $inv->invoice_date->format('d M Y') }}</div>
                                <div class="text-[11px] text-rose-600 font-semibold mt-0.5">Jth Tempo: {{ $inv->due_date->format('d M Y') }}</div>
                            </td>
                            <td class="p-4 font-black text-slate-900 text-sm">
                                Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                {!! $inv->status_badge !!}
                            </td>
                            <td class="p-4 text-center space-x-1.5">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-[11px] transition" title="Lihat Detail Invoice">
                                    <i class="fa-solid fa-eye mr-1"></i> Detail
                                </a>
                                <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-[11px] transition" title="Cetak Invoice">
                                    <i class="fa-solid fa-print mr-1"></i> Cetak
                                </a>

                                @if(!Auth::user()->isCustomer())
                                    <form action="{{ route('invoices.destroy', $inv->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice {{ $inv->invoice_number }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded-lg text-rose-600 hover:bg-rose-50 font-bold transition" title="Hapus Invoice">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">Belum ada invoice penagihan pengiriman ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
