@extends('layouts.app')

@section('title', 'Detail Customer - ' . $customer->name)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $customer->customer_type == 'corporate' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }} mb-1">
                {{ $customer->customer_type }}
            </span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $customer->name }}</h2>
            <p class="text-xs text-indigo-600 font-mono font-semibold">{{ $customer->customer_code }}</p>
        </div>
        <a href="{{ route('customers.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Customer Details Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Perusahaan</div>
            <div class="text-sm font-bold text-slate-900">{{ $customer->company_name ?? '-' }}</div>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kontak Telepon</div>
            <div class="text-sm font-bold text-slate-900">{{ $customer->phone }}</div>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email</div>
            <div class="text-sm font-bold text-slate-900">{{ $customer->email ?? '-' }}</div>
        </div>
        <div class="md:col-span-3 pt-4 border-t border-slate-100">
            <div class="text-sm font-medium text-slate-800">
                {{ $customer->address }}
                @if($customer->subdistrict), Kel. {{ $customer->subdistrict }}@endif
                @if($customer->district), Kec. {{ $customer->district }}@endif
                , {{ $customer->city }}
                @if($customer->province), Prov. {{ $customer->province }}@endif
                {{ $customer->postal_code }}
            </div>
        </div>
    </div>

    <!-- Delivery Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-file-invoice text-indigo-600 mr-2"></i> Riwayat Delivery Order (DO Customer)
            </h3>
            <a href="{{ route('delivery-orders.create') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                + Buat DO Baru
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Nomor DO</th>
                        <th class="p-4">Tanggal Order</th>
                        <th class="p-4">Penerima & Tujuan</th>
                        <th class="p-4">Status DO</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($customer->deliveryOrders as $do)
                        <tr class="hover:bg-slate-50/80">
                            <td class="p-4 font-mono font-bold text-indigo-600">{{ $do->do_number }}</td>
                            <td class="p-4 text-slate-700">{{ $do->order_date->format('d M Y') }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $do->recipient_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $do->recipient_city }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-800">
                                    {{ $do->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('delivery-orders.show', $do->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 font-bold">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Belum ada riwayat Delivery Order untuk customer ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
