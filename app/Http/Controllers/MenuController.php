<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('parent')->orderBy('sort_order', 'asc')->get();
        $parentMenus = Menu::whereNull('parent_id')->orderBy('title')->get();

        return view('menus.index', compact('menus', 'parentMenus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $menu = Menu::create([
            'title' => $validated['title'],
            'route' => $validated['route'] ?? null,
            'icon' => $validated['icon'] ?: 'fa-solid fa-circle',
            'parent_id' => $validated['parent_id'] ?? null,
            'sort_order' => $validated['sort_order'],
            'is_active' => $validated['is_active'],
        ]);

        // Auto-assign permissions to existing roles
        $roles = Role::all();
        foreach ($roles as $role) {
            RoleMenuPermission::create([
                'role_id' => $role->id,
                'menu_id' => $menu->id,
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
        }

        return redirect()->route('menus.index')->with('success', "Menu '{$menu->title}' berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $menu->update([
            'title' => $validated['title'],
            'route' => $validated['route'] ?? null,
            'icon' => $validated['icon'] ?: 'fa-solid fa-circle',
            'parent_id' => $validated['parent_id'] ?? null,
            'sort_order' => $validated['sort_order'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('menus.index')->with('success', "Menu '{$menu->title}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->route('menus.index')->with('success', "Menu '{$menu->title}' berhasil dihapus.");
    }
}
