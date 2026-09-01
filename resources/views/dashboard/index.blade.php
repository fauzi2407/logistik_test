@extends('layouts.app')

@section('title', 'Dashboard Eksekutif')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome Banner -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <span class="inline-block px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold uppercase tracking-wider mb-2">
                Executive Logistics Operations
            </span>
            <h2 class="text-2xl font-black tracking-tight">Selamat Datang, {{ Auth::user()->name }}</h2>
            <p class="text-slate-400 text-xs mt-1">Ringkasan performa pengiriman dan pemantauan armada kurir & paket real-time.</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(Auth::user()->hasPermission('shipments.index', 'create'))
                <a href="{{ route('shipments.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/40 transition">
                    <i class="fa-solid fa-plus mr-1"></i> Buat Resi Baru
                </a>
            @endif
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition border border-slate-700">
                <i class="fa-solid fa-file-chart-line mr-1"></i> Laporan Detail
            </a>
        </div>
    </div>

    <!-- Executive KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- KPI 1: Total Pengiriman -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Resi Pengiriman</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalShipments) }}</h3>
                <p class="text-[11px] text-slate-500 mt-1"><i class="fa-solid fa-arrow-trend-up text-emerald-500 mr-1"></i> Resi Terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
        </div>

        <!-- KPI 2: Total Pendapatan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Omset Ongkir</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-500 mt-1"><i class="fa-solid fa-wallet text-emerald-500 mr-1"></i> Pendapatan Logistik</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- KPI 3: In Transit & Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Dalam Perjalanan</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($activeInTransit) }}</h3>
                <p class="text-[11px] text-slate-500 mt-1"><i class="fa-solid fa-truck-ramp-box text-amber-500 mr-1"></i> Resi Aktif Transit</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
        </div>

        <!-- KPI 4: Performa SLA -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">SLA Delivery Rate</p>
                <h3 class="text-2xl font-black text-indigo-600 mt-1">{{ $slaSuccessRate }}%</h3>
                <p class="text-[11px] text-slate-500 mt-1"><i class="fa-solid fa-circle-check text-indigo-500 mr-1"></i> {{ $deliveredCount }} Paket Terkirim</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-bullseye"></i>
            </div>
        </div>
    </div>

    <!-- Pemantauan Kurir & Paket Yang Dibawa (Live Courier Fleet Monitoring) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-100 pb-4 gap-2">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-satellite-dish text-rose-600 mr-2 animate-pulse text-base"></i> Pemantauan Armada Kurir & Paket Yang Dibawa
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar kurir bertugas, kendaraan, kontak darurat, dan resi paket aktif yang sedang diantar.</p>
            </div>
            <a href="{{ route('couriers.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                Kelola Master Kurir <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($activeCouriersMonitoring as $cr)
                @php
                    $activePackages = $cr->shipments;
                    $stBadge = match($cr->status) {
                        'on_duty' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'available' => 'bg-sky-100 text-sky-800 border-sky-300',
                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                    };
                @endphp
                <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-3 hover:border-indigo-300 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-user-gear"></i>
                            </div>
                            <div>
                                <div class="font-extrabold text-slate-900 text-sm leading-tight">{{ $cr->name }}</div>
                                <div class="text-[11px] font-mono text-indigo-600 font-bold">{{ $cr->courier_code }} • {{ $cr->age ? $cr->age . ' th' : '' }}</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border {{ $stBadge }}">
                            {{ str_replace('_', ' ', $cr->status) }}
                        </span>
                    </div>

                    <!-- Contact & Vehicle -->
                    <div class="text-xs space-y-1 bg-white p-3 rounded-xl border border-slate-100">
                        <div class="flex justify-between">
                            <span class="text-slate-400">No. HP Kurir:</span>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cr->phone) }}" target="_blank" class="font-bold text-indigo-600 hover:underline">
                                {{ $cr->phone }}
                            </a>
                        </div>
                        @if($cr->emergency_phone)
                            <div class="flex justify-between text-rose-600 font-semibold">
                                <span class="text-rose-400">No. Darurat:</span>
                                <span>{{ $cr->emergency_phone }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-0.5">
                            <span class="text-slate-400">Armada / Plat:</span>
                            <span class="font-bold text-slate-800">{{ $cr->vehicle ? $cr->vehicle->plate_number . ' (' . $cr->vehicle->vehicle_type . ')' : 'Tanpa Armada' }}</span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-400">Komisi/Paket:</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($cr->commission_per_delivery ?? 5000, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Active Packages Being Carried -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                            <span class="text-slate-700 uppercase text-[10px] tracking-wider">Paket Aktif Ditangani:</span>
                            <span class="px-2 py-0.5 rounded-full bg-indigo-600 text-white text-[10px] font-extrabold">
                                {{ count($activePackages) }} Paket
                            </span>
                        </div>

                        @if(count($activePackages) > 0)
                            <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                @foreach($activePackages as $ap)
                                    <div class="p-2 bg-white rounded-lg border border-slate-200/60 text-[11px] flex items-center justify-between">
                                        <div>
                                            <a href="{{ route('shipments.show', $ap->id) }}" class="font-mono font-bold text-indigo-600 hover:underline">{{ $ap->tracking_number }}</a>
                                            <div class="text-slate-600 truncate max-w-[150px]">{{ $ap->recipient_name }} ({{ $ap->recipient_city }})</div>
                                        </div>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-100 text-amber-800">
                                            {{ str_replace('_', ' ', $ap->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-[11px] text-slate-400 italic bg-white p-2.5 rounded-lg border border-slate-100 text-center">
                                Kurir tidak membawa paket aktif saat ini.
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 text-center text-slate-400 text-xs">
                    Belum ada data armada kurir terdaftar.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Visual Analytics Section (Charts) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart 1: Status Breakdown (Donut) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-4 flex items-center justify-between">
                <span><i class="fa-solid fa-chart-pie mr-2 text-indigo-600"></i> Distribusi Status Pengiriman</span>
            </h3>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Top Destination Cities (Bar Chart) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-4 flex items-center justify-between">
                <span><i class="fa-solid fa-chart-simple mr-2 text-emerald-600"></i> Top 5 Kota Tujuan Utama</span>
            </h3>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="cityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Shipments Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-list-check mr-2 text-indigo-600"></i> Transaksi Resi Terbaru
            </h3>
            <a href="{{ route('shipments.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                Lihat Semua Resi <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Nomor Resi / AWB</th>
                        <th class="p-4">Pengirim</th>
                        <th class="p-4">Penerima & Tujuan</th>
                        <th class="p-4">Layanan</th>
                        <th class="p-4">Total Biaya</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($recentShipments as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('shipments.show', $s->id) }}">{{ $s->tracking_number }}</a>
                            </td>
                            <td class="p-4 text-slate-800">
                                <div class="font-bold">{{ $s->sender_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $s->sender_city }}</div>
                            </td>
                            <td class="p-4 text-slate-800">
                                <div class="font-bold">{{ $s->recipient_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $s->recipient_city }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold">{{ $s->service_type }}</span>
                            </td>
                            <td class="p-4 font-bold text-slate-900">
                                Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                @php
                                    $stColor = match($s->status) {
                                        'delivered' => 'bg-emerald-100 text-emerald-800',
                                        'out_for_delivery' => 'bg-amber-100 text-amber-800',
                                        'in_transit' => 'bg-sky-100 text-sky-800',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $stColor }}">
                                    {{ str_replace('_', ' ', $s->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Status Donut Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartStatusLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartStatusData) !!},
                    backgroundColor: [
                        '#94a3b8',
                        '#f59e0b',
                        '#3b82f6',
                        '#0284c7',
                        '#eab308',
                        '#10b981',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } }
                },
                cutout: '70%'
            }
        });

        // City Bar Chart
        const cityCtx = document.getElementById('cityChart').getContext('2d');
        new Chart(cityCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($topCities->pluck('recipient_city')) !!},
                datasets: [{
                    label: 'Jumlah Paket',
                    data: {!! json_encode($topCities->pluck('total')) !!},
                    backgroundColor: '#6366f1',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
@endsection
