<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\CustomerInquiry;
use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CustomerInquiryController extends Controller
{
    private function ensureSchema(): void
    {
        if (!Schema::hasTable('customer_inquiries')) {
            Schema::create('customer_inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone', 50);
                $table->text('message');
                $table->string('status', 30)->default('new');
                $table->text('follow_up_notes')->nullable();
                $table->foreignId('followed_up_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('followed_up_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('menus')) {
            $menu = Menu::firstOrCreate(
                ['route' => 'customer-inquiries.index'],
                [
                    'title' => 'Pesan & Follow Up CS',
                    'route' => 'customer-inquiries.index',
                    'icon' => 'fa-solid fa-headset',
                    'sort_order' => 4,
                    'is_active' => true,
                ]
            );

            if (Schema::hasTable('roles') && Schema::hasTable('role_menu_permissions')) {
                $adminRole = Role::where('slug', 'admin')->first();
                $staffRole = Role::where('slug', 'staff')->first();

                if ($adminRole) {
                    RoleMenuPermission::firstOrCreate(
                        ['role_id' => $adminRole->id, 'menu_id' => $menu->id],
                        [
                            'can_view' => true,
                            'can_create' => true,
                            'can_edit' => true,
                            'can_delete' => true,
                            'can_view_salary' => true,
                            'can_edit_salary' => true,
                        ]
                    );
                }

                if ($staffRole) {
                    RoleMenuPermission::firstOrCreate(
                        ['role_id' => $staffRole->id, 'menu_id' => $menu->id],
                        [
                            'can_view' => true,
                            'can_create' => true,
                            'can_edit' => true,
                            'can_delete' => false,
                            'can_view_salary' => true,
                            'can_edit_salary' => true,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Public endpoint for "Hubungi Tim Layanan Pelanggan AWX" form on homepage.
     */
    public function storePublic(Request $request)
    {
        $this->ensureSchema();
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'message' => 'required|string|max:3000',
        ]);

        $inquiry = CustomerInquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        // Notify Admin & Staff CS immediately
        try {
            AppNotification::sendToStaff([
                'title' => 'Pesan Pelanggan Baru: ' . $inquiry->name,
                'message' => "Telp/WA: {$inquiry->phone} | Pesan: " . \Illuminate\Support\Str::limit($inquiry->message, 100),
                'type' => 'customer_inquiry',
                'icon' => 'fa-headset',
                'color' => 'amber',
                'url' => route('customer-inquiries.index'),
                'data' => ['inquiry_id' => $inquiry->id],
            ]);
        } catch (\Throwable $e) {
            // Ignore notification error so public submission still succeeds
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Terima kasih! Pesan Anda telah diterima oleh Tim Layanan Pelanggan AWX dan akan segera kami tindak lanjuti.',
                'data' => $inquiry,
            ]);
        }

        return redirect()->to(url()->previous() . '#kontak')
            ->with('contact_success', 'Terima kasih! Pesan Anda telah diterima oleh Tim Layanan Pelanggan AWX dan akan segera kami tindak lanjuti.');
    }

    /**
     * Internal management & follow-up list.
     */
    public function index(Request $request)
    {
        $this->ensureSchema();
        $status = $request->input('status', 'all');
        $search = trim($request->input('search', ''));

        $inquiries = CustomerInquiry::with('followedUpByUser')
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhere('follow_up_notes', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => CustomerInquiry::count(),
            'new' => CustomerInquiry::where('status', 'new')->count(),
            'in_progress' => CustomerInquiry::where('status', 'in_progress')->count(),
            'resolved' => CustomerInquiry::where('status', 'resolved')->count(),
            'closed' => CustomerInquiry::where('status', 'closed')->count(),
        ];

        return view('customer_inquiries.index', compact('inquiries', 'status', 'search', 'stats'));
    }

    /**
     * Update follow-up status and notes for an inquiry.
     */
    public function update(Request $request, $id)
    {
        $inquiry = CustomerInquiry::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,resolved,closed',
            'follow_up_notes' => 'nullable|string|max:3000',
        ]);

        $inquiry->update([
            'status' => $validated['status'],
            'follow_up_notes' => $validated['follow_up_notes'] ?? $inquiry->follow_up_notes,
            'followed_up_by' => Auth::id(),
            'followed_up_at' => now(),
        ]);

        return redirect()->back()->with('success', "Status follow up untuk {$inquiry->name} berhasil diperbarui.");
    }

    /**
     * Delete an inquiry record.
     */
    public function destroy($id)
    {
        $inquiry = CustomerInquiry::findOrFail($id);
        $name = $inquiry->name;
        $inquiry->delete();

        return redirect()->back()->with('success', "Data pesan dari {$name} berhasil dihapus.");
    }
}
