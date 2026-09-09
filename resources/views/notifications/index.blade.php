@extends('layouts.app')

@section('title', 'Pusat Notifikasi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-100 text-indigo-800">
                    Notifikasi Sistem
                </span>
                @if($unreadCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-800 animate-pulse">
                        {{ $unreadCount }} Belum Dibaca
                    </span>
                @endif
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight mt-1">Pusat Notifikasi & Aktivitas</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pantau seluruh pembaruan status pengiriman, penugasan kurir, kasbon, dan penagihan invoice.</p>
        </div>

        <div class="flex items-center space-x-2 flex-wrap gap-2">
            @if(Auth::user()->isAdmin())
                <button type="button" onclick="openBroadcastModal()" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center">
                    <i class="fa-solid fa-bullhorn mr-1.5"></i> Kirim Broadcast
                </button>
            @endif

            @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition flex items-center">
                        <i class="fa-solid fa-check-double mr-1.5 text-emerald-400"></i> Tandai Semua Dibaca
                    </button>
                </form>
            @endif

            @if($readCount > 0)
                <form action="{{ route('notifications.delete-all-read') }}" method="POST" class="inline" onsubmit="return confirm('Hapus semua riwayat notifikasi yang telah dibaca?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition flex items-center">
                        <i class="fa-solid fa-trash-can mr-1.5"></i> Hapus yang Dibaca
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Filter Tabs & Search Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <!-- Tabs -->
        <div class="flex items-center space-x-1.5 bg-slate-200/70 p-1 rounded-2xl">
            <a href="{{ route('notifications.index', ['filter' => 'all', 'search' => $search]) }}" 
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Semua ({{ $totalCount }})
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread', 'search' => $search]) }}" 
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1 {{ $filter === 'unread' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Belum Dibaca</span>
                @if($unreadCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[10px] font-extrabold ml-1">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'read', 'search' => $search]) }}" 
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'read' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Sudah Dibaca ({{ $readCount }})
            </a>
        </div>

        <!-- Search Form -->
        <form action="{{ route('notifications.index') }}" method="GET" class="relative sm:w-64">
            @if($filter !== 'all') <input type="hidden" name="filter" value="{{ $filter }}"> @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari isi notifikasi..." 
                class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            @if($search)
                <a href="{{ route('notifications.index', ['filter' => $filter]) }}" class="absolute right-3 top-2 text-slate-400 hover:text-slate-600 text-xs" title="Clear">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Notifications List -->
    <div class="space-y-3">
        @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
                $color = $notif->color ?? 'indigo';
                $colorStyles = match($color) {
                    'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'iconBg' => 'bg-emerald-100 text-emerald-700'],
                    'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'iconBg' => 'bg-amber-100 text-amber-700'],
                    'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'iconBg' => 'bg-rose-100 text-rose-700'],
                    'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'iconBg' => 'bg-blue-100 text-blue-700'],
                    default => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'iconBg' => 'bg-indigo-100 text-indigo-700'],
                };
            @endphp
            <div class="p-4 rounded-2xl border transition shadow-sm {{ $isUnread ? 'bg-white border-indigo-200/90 ring-1 ring-indigo-500/10' : 'bg-white/80 border-slate-200/80 opacity-90' }} flex flex-col sm:flex-row items-start justify-between gap-4">
                <div class="flex items-start space-x-3.5 flex-1">
                    <!-- Icon -->
                    <div class="w-10 h-10 rounded-xl {{ $colorStyles['iconBg'] }} flex items-center justify-center shrink-0 text-base shadow-sm">
                        <i class="fa-solid {{ $notif->icon ?? 'fa-bell' }}"></i>
                    </div>

                    <!-- Message Body -->
                    <div class="space-y-1 flex-1">
                        <div class="flex items-center space-x-2">
                            <h4 class="text-xs font-black text-slate-900 {{ $isUnread ? 'font-extrabold' : 'font-bold' }}">
                                {{ $notif->title }}
                            </h4>
                            @if($isUnread)
                                <span class="w-2 h-2 rounded-full bg-indigo-600 shrink-0" title="Belum dibaca"></span>
                            @endif
                            <span class="text-[10px] font-semibold text-slate-400">
                                • {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            {{ $notif->message }}
                        </p>
                        <div class="text-[10px] text-slate-400 font-mono pt-0.5">
                            {{ $notif->created_at->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-2 shrink-0 self-end sm:self-center">
                    @if(!empty($notif->url))
                        <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition flex items-center">
                                <span>Buka Dokumen</span>
                                <i class="fa-solid fa-arrow-up-right-from-square ml-1.5 text-[10px]"></i>
                            </button>
                        </form>
                    @elseif($isUnread)
                        <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center" title="Tandai telah dibaca">
                                <i class="fa-solid fa-check mr-1"></i> Dibaca
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus notifikasi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition text-xs" title="Hapus Notifikasi">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-12 text-center bg-white rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                    <i class="fa-regular fa-bell-slash"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Tidak ada notifikasi</h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if($filter === 'unread')
                            Semua pemberitahuan telah dibaca.
                        @elseif($search)
                            Tidak ada notifikasi yang cocok dengan kata kunci "{{ $search }}".
                        @else
                            Belum ada riwayat notifikasi atau aktivitas baru untuk akun Anda.
                        @endif
                    </p>
                </div>
                @if($search || $filter !== 'all')
                    <div class="pt-1">
                        <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination Footer -->
    @if($notifications->hasPages())
        <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
            <div class="text-xs text-slate-500 font-medium">
                Menampilkan {{ $notifications->firstItem() }} - {{ $notifications->lastItem() }} dari {{ $notifications->total() }} Notifikasi
            </div>
            <div>
                {{ $notifications->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modal Broadcast (Admin Only) -->
@if(Auth::user()->isAdmin())
<div id="broadcastModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center">
                <i class="fa-solid fa-bullhorn text-indigo-600 mr-2"></i> Kirim Broadcast Notifikasi
            </h3>
            <button type="button" onclick="closeBroadcastModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('notifications.broadcast') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Target Penerima Notifikasi *</label>
                <select name="target_role" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    <option value="all">Semua Pengguna (Kurir, Customer, Staf & Admin)</option>
                    <option value="courier">Khusus Kurir / Driver</option>
                    <option value="customer">Khusus Customer / Klien</option>
                    <option value="staff">Khusus Staf Operasional & Gudang</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-0.5">*Admin secara otomatis selalu menerima setiap notifikasi broadcast.</p>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Judul Notifikasi *</label>
                <input type="text" name="title" required placeholder="Contoh: Pengumuman Operasional Libur Nasional" 
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Isi Pesan Notifikasi *</label>
                <textarea name="message" rows="3" required placeholder="Tuliskan pesan pemberitahuan yang ingin disampaikan..." 
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Warna Aksen</label>
                    <select name="color" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                        <option value="indigo">Indigo (Standar)</option>
                        <option value="emerald">Hijau (Sukses/Info)</option>
                        <option value="amber">Kuning (Peringatan)</option>
                        <option value="rose">Merah (Penting/Urgent)</option>
                        <option value="blue">Biru (Operasional)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori</label>
                    <input type="text" name="type" value="pengumuman" placeholder="pengumuman" 
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="closeBroadcastModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md shadow-indigo-600/30">
                    Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBroadcastModal() {
        document.getElementById('broadcastModal').classList.remove('hidden');
    }
    function closeBroadcastModal() {
        document.getElementById('broadcastModal').classList.add('hidden');
    }
</script>
@endif
@endsection
