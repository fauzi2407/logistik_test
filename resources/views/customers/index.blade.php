@extends('layouts.app')

@section('title', 'Manajemen Customer')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Daftar Customer / Klien</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data pelanggan corporate & individual pengirim barang.</p>
        </div>
        <div class="flex items-center space-x-3 w-full md:w-auto">
            <form action="{{ route('customers.index') }}" method="GET" class="flex-1 md:w-64">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, kode, kota..." 
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </form>
            <a href="{{ route('customers.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex-shrink-0">
                <i class="fa-solid fa-plus mr-1.5"></i> Tambah Customer
            </a>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Kode & Nama Customer</th>
                        <th class="p-4">Tipe & Perusahaan</th>
                        <th class="p-4">Kontak (HP / Email)</th>
                        <th class="p-4">Kota / Alamat</th>
                        <th class="p-4">Total DO / Resi</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <a href="{{ route('customers.show', $c->id) }}" class="font-bold text-indigo-600 hover:text-indigo-800 text-sm">{{ $c->name }}</a>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $c->customer_code }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $c->customer_type == 'corporate' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $c->customer_type }}
                                </span>
                                <div class="text-[11px] text-slate-600 font-semibold mt-1">{{ $c->company_name ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-slate-700">
                                <div><i class="fa-solid fa-phone text-slate-400 mr-1 text-[10px]"></i> {{ $c->phone }}</div>
                                <div class="text-[11px] text-slate-400"><i class="fa-solid fa-envelope text-slate-400 mr-1 text-[10px]"></i> {{ $c->email ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-slate-700">
                                <div class="font-bold text-slate-900">{{ $c->city }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $c->address }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold">
                                    {{ $c->delivery_orders_count }} DO / {{ $c->shipments_count }} Resi
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <a href="{{ route('customers.show', $c->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition" title="Lihat Profil">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $c->id) }}" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition" title="Edit Customer">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus customer ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
