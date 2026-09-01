<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('id', 'asc')->get();
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
        ]);

        $slug = Str::slug($validated['name']);
        if (Role::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        // Auto-initialize permissions for all existing menus
        $menus = Menu::all();
        foreach ($menus as $menu) {
            RoleMenuPermission::create([
                'role_id' => $role->id,
                'menu_id' => $menu->id,
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => false,
                'can_view_salary' => true,
                'can_edit_salary' => true,
            ]);
        }

        return redirect()->route('roles.permissions', $role->id)->with('success', "Role '{$role->name}' berhasil dibuat! Silakan atur matriks hak akses CRUD.");
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:500',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_system) {
            return redirect()->route('roles.index')->with('error', "Role bawaan sistem ('{$role->name}') tidak dapat dihapus.");
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' berhasil dihapus.");
    }

    public function permissions($id)
    {
        $role = Role::findOrFail($id);
        $menus = Menu::with('parent')->orderBy('sort_order', 'asc')->get();

        $existingPermissions = RoleMenuPermission::where('role_id', $role->id)
            ->get()
            ->keyBy('menu_id');

        return view('roles.permissions', compact('role', 'menus', 'existingPermissions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = $request->input('permissions', []);

        $menus = Menu::all();
        foreach ($menus as $menu) {
            $permData = $permissions[$menu->id] ?? [];

            RoleMenuPermission::updateOrCreate(
                [
                    'role_id' => $role->id,
                    'menu_id' => $menu->id,
                ],
                [
                    'can_view' => isset($permData['view']),
                    'can_create' => isset($permData['create']),
                    'can_edit' => isset($permData['edit']),
                    'can_delete' => isset($permData['delete']),
                    'can_view_salary' => isset($permData['view_salary']),
                    'can_edit_salary' => isset($permData['edit_salary']),
                ]
            );
        }

        return redirect()->route('roles.index')->with('success', "Matriks Hak Akses CRUD untuk Role '{$role->name}' berhasil diperbarui.");
    }
}
