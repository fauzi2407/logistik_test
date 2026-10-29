@extends('layouts.app')

@section('title', 'Pesan & Follow Up Layanan Pelanggan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center space-x-2 text-xs font-extrabold uppercase tracking-wider text-orange-600 mb-1">
                <i class="fa-solid fa-headset"></i>
                <span>Customer Service & Lead Management</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black text-slate-900">Pesan & Follow Up Layanan Pelanggan AWX</h1>
            <p class="text-xs text-slate-500 mt-1">
                Menampung seluruh pesan, pertanyaan, dan permintaan kerjasama pengiriman dari form <strong>Hubungi Tim Layanan Pelanggan AWX</strong> di halaman depan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}#kontak" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition inline-flex items-center space-x-2">
                <i class="fa-solid fa-up-right-from-square"></i>
                <span>Lihat Form di Web Utama</span>
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Pesan Masuk</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</h3>
                <span class="text-[11px] text-slate-500">Semua riwayat form</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-inbox"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-rose-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500">Pesan Baru (Belum Follow Up)</p>
                <h3 class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['new']) }}</h3>
                <span class="text-[11px] text-rose-500 font-semibold">Perlu segera ditindaklanjuti</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-bell-concierge"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600">Sedang Di-Follow Up</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['in_progress']) }}</h3>
                <span class="text-[11px] text-slate-500">Dalam komunikasi CS</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-comments"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-emerald-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600">Selesai / Terkonversi</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['resolved']) }}</h3>
                <span class="text-[11px] text-slate-500">Follow up tuntas</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex flex-wrap items-center gap-1.5">
            @php
                $tabs = [
                    'all' => ['label' => 'Semua', 'count' => $stats['total']],
                    'new' => ['label' => 'Baru', 'count' => $stats['new']],
                    'in_progress' => ['label' => 'Sedang Follow Up', 'count' => $stats['in_progress']],
                    'resolved' => ['label' => 'Selesai', 'count' => $stats['resolved']],
                    'closed' => ['label' => 'Ditutup', 'count' => $stats['closed']],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ route('customer-inquiries.index', ['status' => $key, 'search' => $search]) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ $status === $key ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === $key ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Search Form -->
        <form action="{{ route('customer-inquiries.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-auto">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative flex-1 lg:w-72">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, perusahaan, WA, pesan..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-orange-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs transition">
                Cari
            </button>
            @if(!empty($search) || $status !== 'all')
                <a href="{{ route('customer-inquiries.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white text-[11px] font-extrabold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Waktu & Pengirim</th>
                        <th class="py-3.5 px-4">Kontak (WA / Email)</th>
                        <th class="py-3.5 px-4">Pesan / Kebutuhan Pengiriman</th>
                        <th class="py-3.5 px-4">Status & Catatan Follow Up</th>
                        <th class="py-3.5 px-4 text-right">Aksi Follow Up</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80 text-xs">
                    @forelse($inquiries as $item)
                        @php
                            $statusBadge = match($item->status) {
                                'new' => ['label' => 'BARU / BELUM FOLLOW UP', 'class' => 'bg-rose-100 text-rose-700 border-rose-200'],
                                'in_progress' => ['label' => 'SEDANG FOLLOW UP', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
                                'resolved' => ['label' => 'SELESAI / TERKONVERSI', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                'closed' => ['label' => 'DITUTUP', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
                                default => ['label' => strtoupper($item->status), 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/90 transition align-top {{ $item->status === 'new' ? 'bg-orange-50/30' : '' }}">
                            <!-- Pengirim -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="font-black text-slate-900 text-sm">{{ $item->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                    <i class="fa-regular fa-clock mr-1"></i>{{ $item->created_at->format('d M Y, H:i') }} WIB
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $item->created_at->diffForHumans() }}</div>
                            </td>

                            <!-- Kontak -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="space-y-1.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-5 h-5 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-[11px]">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </span>
                                        <a href="{{ $item->whatsapp_url }}" target="_blank" class="font-mono font-bold text-slate-800 hover:text-emerald-600 hover:underline">
                                            {{ $item->phone }}
                                        </a>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="w-5 h-5 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-[11px]">
                                            <i class="fa-regular fa-envelope"></i>
                                        </span>
                                        <a href="mailto:{{ $item->email }}?subject={{ rawurlencode('Tindak Lanjut Layanan Pengiriman AWX - ' . $item->name) }}" class="text-slate-600 hover:text-blue-600 hover:underline">
                                            {{ $item->email }}
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <!-- Pesan -->
                            <td class="py-4 px-4 max-w-md">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-slate-700 leading-relaxed whitespace-pre-line text-xs">{{ $item->message }}</div>
                            </td>

                            <!-- Status & Follow Up Notes -->
                            <td class="py-4 px-4 max-w-sm">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold border {{ $statusBadge['class'] }}">
                                    {{ $statusBadge['label'] }}
                                </span>

                                @if($item->follow_up_notes)
                                    <div class="mt-2 p-2.5 rounded-xl bg-amber-50/70 border border-amber-200/80 text-[11px] text-slate-700">
                                        <div class="font-bold text-amber-900 mb-0.5">
                                            <i class="fa-solid fa-note-sticky mr-1"></i> Catatan CS:
                                        </div>
                                        <div class="whitespace-pre-line">{{ $item->follow_up_notes }}</div>
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400 italic mt-1.5">Belum ada catatan follow up.</div>
                                @endif

                                @if($item->followedUpByUser || $item->followed_up_at)
                                    <div class="text-[10px] text-slate-400 mt-1.5">
                                        Diupdate oleh: <strong class="text-slate-600">{{ $item->followedUpByUser->name ?? 'CS AWX' }}</strong>
                                        @if($item->followed_up_at)
                                            &bull; {{ $item->followed_up_at->format('d M Y H:i') }}
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                <div class="flex flex-col sm:flex-row items-end sm:items-center justify-end gap-1.5">
                                    <!-- Chat WA Button -->
                                    <a href="{{ $item->whatsapp_url }}" target="_blank"
                                       class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition inline-flex items-center space-x-1.5 shadow-sm"
                                       title="Hubungi via WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                        <span>WA Follow Up</span>
                                    </a>

                                    <!-- Update Status Modal Trigger -->
                                    <button type="button"
                                            onclick="openFollowUpModal({{ json_encode([
                                                'id' => $item->id,
                                                'name' => $item->name,
                                                'email' => $item->email,
                                                'phone' => $item->phone,
                                                'message' => $item->message,
                                                'status' => $item->status,
                                                'follow_up_notes' => $item->follow_up_notes ?? '',
                                                'whatsapp_url' => $item->whatsapp_url,
                                                'update_url' => route('customer-inquiries.update', $item->id),
                                            ]) }})"
                                            class="px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition inline-flex items-center space-x-1.5 shadow-sm">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Update Follow Up</span>
                                    </button>

                                    @if(Auth::user()->hasPermission('customer-inquiries.index', 'delete'))
                                        <form action="{{ route('customer-inquiries.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data pesan dari {{ addslashes($item->name) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Hapus Pesan">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-600">Belum ada data pesan pelanggan</p>
                                <p class="text-xs text-slate-400 mt-1">Pesan yang dikirim melalui form Hubungi Tim Layanan Pelanggan AWX akan otomatis tampil di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inquiries->hasPages())
            <div class="p-4 border-t border-slate-200/80 bg-slate-50">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Update Status & Catatan Follow Up -->
<div id="followUpModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider">Tindak Lanjut (Follow Up) Pelanggan</h3>
                    <p id="modalCustomerName" class="text-xs text-orange-400 font-bold"></p>
                </div>
            </div>
            <button type="button" onclick="closeFollowUpModal()" class="text-slate-400 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="followUpForm" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            @method('PUT')

            <!-- Customer Message Preview -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                <div class="flex items-center justify-between text-[11px] text-slate-500 font-bold">
                    <span id="modalCustomerContact"></span>
                    <a id="modalWhatsappBtn" href="#" target="_blank" class="text-emerald-600 hover:underline flex items-center">
                        <i class="fa-brands fa-whatsapp mr-1"></i> Chat WhatsApp
                    </a>
                </div>
                <div id="modalCustomerMessage" class="text-slate-800 font-medium whitespace-pre-line pt-1 border-t border-slate-200/70"></div>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1.5">Status Follow Up *</label>
                <select name="status" id="modalStatusSelect" required class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-slate-800 focus:outline-none focus:border-orange-500">
                    <option value="new">BARU (Belum Di-Follow Up)</option>
                    <option value="in_progress">SEDANG FOLLOW UP (Dalam Proses Komunikasi)</option>
                    <option value="resolved">SELESAI / TERKONVERSI (Deal / Terjawab)</option>
                    <option value="closed">DITUTUP (Tidak Valid / Batal)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1.5">Catatan Hasil Follow Up CS</label>
                <textarea name="follow_up_notes" id="modalNotesInput" rows="4" placeholder="Tuliskan hasil komunikasi dengan calon pelanggan, kebutuhan tarif korporasi, PIC yang dihubungi, dll..."
                          class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-orange-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeFollowUpModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-wider transition shadow-md">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Follow Up
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openFollowUpModal(data) {
        const modal = document.getElementById('followUpModal');
        const form = document.getElementById('followUpForm');
        document.getElementById('modalCustomerName').textContent = data.name;
        document.getElementById('modalCustomerContact').textContent = `${data.phone} • ${data.email}`;
        document.getElementById('modalCustomerMessage').textContent = data.message;
        document.getElementById('modalStatusSelect').value = data.status;
        document.getElementById('modalNotesInput').value = data.follow_up_notes || '';
        document.getElementById('modalWhatsappBtn').href = data.whatsapp_url;
        form.action = data.update_url;
        modal.classList.remove('hidden');
    }

    function closeFollowUpModal() {
        document.getElementById('followUpModal').classList.add('hidden');
    }
</script>
@endpush
@endsection

