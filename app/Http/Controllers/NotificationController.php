<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('filter', 'all');
        $search = $request->input('search');

        $query = AppNotification::where('user_id', $user->id)
            ->when($filter === 'unread', function ($q) {
                $q->whereNull('read_at');
            })
            ->when($filter === 'read', function ($q) {
                $q->whereNotNull('read_at');
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->latest();

        $notifications = $query->paginate(15)->withQueryString();

        $unreadCount = AppNotification::where('user_id', $user->id)->whereNull('read_at')->count();
        $readCount = AppNotification::where('user_id', $user->id)->whereNotNull('read_at')->count();
        $totalCount = $unreadCount + $readCount;

        return view('notifications.index', compact(
            'notifications',
            'filter',
            'search',
            'unreadCount',
            'readCount',
            'totalCount'
        ));
    }

    public function read(Request $request, $id)
    {
        $user = Auth::user();
        $notification = AppNotification::where('user_id', $user->id)->findOrFail($id);
        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if (!empty($notification->url)) {
            return redirect($notification->url);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai telah dibaca.');
    }

    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        AppNotification::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $notification = AppNotification::where('user_id', $user->id)->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function deleteAllRead()
    {
        $user = Auth::user();
        AppNotification::where('user_id', $user->id)->whereNotNull('read_at')->delete();

        return redirect()->back()->with('success', 'Semua riwayat notifikasi yang telah dibaca berhasil dihapus.');
    }

    public function unreadCount()
    {
        $user = Auth::user();
        $count = AppNotification::where('user_id', $user->id)->whereNull('read_at')->count();
        return response()->json(['unread_count' => $count]);
    }

    public function broadcast(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses ditolak: Hanya Admin yang dapat mengirimkan notifikasi broadcast.');
        }

        $validated = $request->validate([
            'target_role' => 'required|in:all,courier,customer,staff',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
        ]);

        $roles = $validated['target_role'] === 'all' 
            ? ['admin', 'staff', 'courier', 'customer'] 
            : [$validated['target_role']];

        AppNotification::send([
            'roles' => $roles,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'] ?? 'broadcast',
            'icon' => 'fa-bullhorn',
            'color' => $validated['color'] ?? 'indigo',
            'url' => route('notifications.index'),
        ]);

        return redirect()->back()->with('success', 'Pemberitahuan broadcast berhasil dikirimkan.');
    }
}
