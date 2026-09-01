<?php

namespace App\Http\Controllers;

use App\Models\BranchHub;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchHubController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $hubs = BranchHub::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('person_in_charge', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15);

        return view('branch_hubs.index', compact('hubs', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:branch_hubs,code',
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:150',
            'address' => 'required|string',
            'phone' => 'required|string|max:50',
            'person_in_charge' => 'required|string|max:150',
        ]);

        $code = $validated['code'] ?? 'HUB-' . strtoupper(substr($validated['city'], 0, 3)) . str_pad(BranchHub::count() + 1, 2, '0', STR_PAD_LEFT);

        BranchHub::create([
            'code' => strtoupper($code),
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'person_in_charge' => $validated['person_in_charge'],
        ]);

        return redirect()->route('branch-hubs.index')->with('success', 'Transit Hub baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $hub = BranchHub::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('branch_hubs')->ignore($hub->id)],
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:150',
            'address' => 'required|string',
            'phone' => 'required|string|max:50',
            'person_in_charge' => 'required|string|max:150',
        ]);

        $hub->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'person_in_charge' => $validated['person_in_charge'],
        ]);

        return redirect()->route('branch-hubs.index')->with('success', 'Data Transit Hub berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $hub = BranchHub::findOrFail($id);
        $hub->delete();

        return redirect()->route('branch-hubs.index')->with('success', 'Transit Hub berhasil dihapus.');
    }
}
