<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\Subdistrict;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    // Admin Management View with Filter & Search
    public function index(Request $request)
    {
        $search = $request->input('search');
        $provinceId = $request->input('province_id');

        $provinces = Province::withCount('cities')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        $cities = City::with(['province'])
            ->withCount('districts')
            ->when($provinceId, function ($q) use ($provinceId) {
                $q->where('province_id', $provinceId);
            })
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15, ['*'], 'cities_page');

        $districts = District::with(['city.province'])
            ->withCount('subdistricts')
            ->when($provinceId, function ($q) use ($provinceId) {
                $q->whereHas('city', function ($cq) use ($provinceId) {
                    $cq->where('province_id', $provinceId);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15, ['*'], 'districts_page');

        $subdistricts = Subdistrict::with(['district.city.province'])
            ->when($provinceId, function ($q) use ($provinceId) {
                $q->whereHas('district.city', function ($cq) use ($provinceId) {
                    $cq->where('province_id', $provinceId);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('postal_code', 'like', "%{$search}%")
                    ->orWhereHas('district', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('name')
            ->paginate(15, ['*'], 'subdistricts_page');

        return view('regions.index', compact('provinces', 'cities', 'districts', 'subdistricts', 'search', 'provinceId'));
    }

    // Export Master Wilayah & Kode Pos to CSV
    public function exportCsv(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="master_wilayah_kodepos_indonesia.csv"',
        ];

        $columns = ['Nama Provinsi', 'Nama Kota/Kabupaten', 'Jenis Kota/Kab', 'Nama Kecamatan', 'Nama Kelurahan', 'Kode Pos'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Subdistrict::with(['district.city.province'])
                ->chunk(500, function ($rows) use ($file) {
                    foreach ($rows as $sd) {
                        fputcsv($file, [
                            $sd->district && $sd->district->city && $sd->district->city->province ? $sd->district->city->province->name : 'N/A',
                            $sd->district && $sd->district->city ? $sd->district->city->name : 'N/A',
                            $sd->district && $sd->district->city ? $sd->district->city->type : 'Kota',
                            $sd->district ? $sd->district->name : 'N/A',
                            $sd->name,
                            $sd->postal_code,
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Download CSV Import Template
    public function templateCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_master_wilayah.csv"',
        ];

        $columns = ['Nama Provinsi', 'Nama Kota/Kabupaten', 'Jenis (Kota/Kabupaten)', 'Nama Kecamatan', 'Nama Kelurahan', 'Kode Pos'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $sampleRows = [
                ['Jawa Barat', 'Kota Bandung', 'Kota', 'Coblong', 'Dago', '40135'],
                ['Jawa Barat', 'Kota Bandung', 'Kota', 'Coblong', 'Lebak Siliwangi', '40132'],
                ['DKI Jakarta', 'Jakarta Selatan', 'Kota', 'Cilandak', 'Cilandak Barat', '12430'],
                ['Jawa Timur', 'Kota Surabaya', 'Kota', 'Genteng', 'Embong Kaliasin', '60271'],
            ];

            foreach ($sampleRows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Import Massal Master Wilayah from CSV
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle, 1000, ',');
        $insertedCount = 0;

        $provCache = [];
        $cityCache = [];
        $distCache = [];

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($data) >= 5 && !empty(trim($data[0]))) {
                $provName = trim($data[0]);
                $cityName = trim($data[1]);
                $cityType = trim($data[2] ?? 'Kota') ?: 'Kota';
                $distName = trim($data[3]);
                $subdistName = trim($data[4]);
                $postalCode = trim($data[5] ?? '') ?: '00000';

                // Get or create Province
                if (!isset($provCache[$provName])) {
                    $prov = Province::firstOrCreate(
                        ['name' => $provName],
                        ['code' => 'PRV-' . str_pad(Province::count() + 1, 3, '0', STR_PAD_LEFT)]
                    );
                    $provCache[$provName] = $prov->id;
                }
                $provId = $provCache[$provName];

                // Get or create City
                $cityKey = $provId . '_' . $cityName;
                if (!isset($cityCache[$cityKey])) {
                    $city = City::firstOrCreate(
                        ['province_id' => $provId, 'name' => $cityName],
                        ['code' => 'CTY-' . str_pad(City::count() + 1, 3, '0', STR_PAD_LEFT), 'type' => $cityType]
                    );
                    $cityCache[$cityKey] = $city->id;
                }
                $cityId = $cityCache[$cityKey];

                // Get or create District
                $distKey = $cityId . '_' . $distName;
                if (!isset($distCache[$distKey])) {
                    $dist = District::firstOrCreate(
                        ['city_id' => $cityId, 'name' => $distName],
                        ['code' => 'DST-' . str_pad(District::count() + 1, 3, '0', STR_PAD_LEFT)]
                    );
                    $distCache[$distKey] = $dist->id;
                }
                $distId = $distCache[$distKey];

                // Create or update Subdistrict
                Subdistrict::firstOrCreate(
                    ['district_id' => $distId, 'name' => $subdistName],
                    ['postal_code' => $postalCode]
                );

                $insertedCount++;
            }
        }
        fclose($handle);

        return redirect()->route('regions.index')->with('success', "Import massal wilayah berhasil! {$insertedCount} data wilayah & kode pos telah diproses.");
    }

    // Store & Update & Delete Province
    public function storeProvince(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:provinces,name',
            'code' => 'nullable|string|max:20|unique:provinces,code',
        ]);

        $code = $validated['code'] ?? 'PRV-' . str_pad(Province::count() + 1, 3, '0', STR_PAD_LEFT);

        Province::create([
            'name' => $validated['name'],
            'code' => $code,
        ]);

        return redirect()->route('regions.index')->with('success', 'Provinsi baru berhasil ditambahkan.');
    }

    public function updateProvince(Request $request, $id)
    {
        $province = Province::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:provinces,name,' . $id,
        ]);

        $province->update(['name' => $validated['name']]);
        return redirect()->route('regions.index')->with('success', 'Nama Provinsi berhasil diperbarui.');
    }

    public function destroyProvince($id)
    {
        $province = Province::findOrFail($id);
        $province->delete();
        return redirect()->route('regions.index')->with('success', 'Provinsi dan data kota di dalamnya telah dihapus.');
    }

    // Store & Update & Delete City
    public function storeCity(Request $request)
    {
        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string|max:150',
            'type' => 'required|in:Kota,Kabupaten',
            'code' => 'nullable|string|max:20',
        ]);

        $code = $validated['code'] ?? 'CTY-' . str_pad(City::count() + 1, 3, '0', STR_PAD_LEFT);

        City::create([
            'province_id' => $validated['province_id'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'code' => $code,
        ]);

        return redirect()->route('regions.index')->with('success', 'Kota / Kabupaten baru berhasil ditambahkan.');
    }

    public function updateCity(Request $request, $id)
    {
        $city = City::findOrFail($id);
        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string|max:150',
            'type' => 'required|in:Kota,Kabupaten',
        ]);

        $city->update($validated);
        return redirect()->route('regions.index')->with('success', 'Data Kota / Kabupaten berhasil diperbarui.');
    }

    public function destroyCity($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        return redirect()->route('regions.index')->with('success', 'Kota / Kabupaten beserta kecamatannya telah dihapus.');
    }

    // Store & Update & Delete District (Kecamatan)
    public function storeDistrict(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:20',
        ]);

        $code = $validated['code'] ?? 'DST-' . str_pad(District::count() + 1, 3, '0', STR_PAD_LEFT);

        District::create([
            'city_id' => $validated['city_id'],
            'name' => $validated['name'],
            'code' => $code,
        ]);

        return redirect()->route('regions.index')->with('success', 'Kecamatan baru berhasil ditambahkan.');
    }

    public function updateDistrict(Request $request, $id)
    {
        $district = District::findOrFail($id);
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:150',
        ]);

        $district->update($validated);
        return redirect()->route('regions.index')->with('success', 'Data Kecamatan berhasil diperbarui.');
    }

    public function destroyDistrict($id)
    {
        $district = District::findOrFail($id);
        $district->delete();
        return redirect()->route('regions.index')->with('success', 'Kecamatan beserta kelurahannya telah dihapus.');
    }

    // Store & Update & Delete Subdistrict (Kelurahan & Kode Pos)
    public function storeSubdistrict(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:150',
            'postal_code' => 'required|string|max:20',
        ]);

        Subdistrict::create([
            'district_id' => $validated['district_id'],
            'name' => $validated['name'],
            'postal_code' => $validated['postal_code'],
        ]);

        return redirect()->route('regions.index')->with('success', 'Kelurahan & Kode Pos baru berhasil ditambahkan.');
    }

    public function updateSubdistrict(Request $request, $id)
    {
        $subdistrict = Subdistrict::findOrFail($id);
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:150',
            'postal_code' => 'required|string|max:20',
        ]);

        $subdistrict->update($validated);
        return redirect()->route('regions.index')->with('success', 'Data Kelurahan & Kode Pos berhasil diperbarui.');
    }

    public function destroySubdistrict($id)
    {
        $subdistrict = Subdistrict::findOrFail($id);
        $subdistrict->delete();
        return redirect()->route('regions.index')->with('success', 'Data Kelurahan & Kode Pos telah dihapus.');
    }

    // JSON API Endpoints for Cascading Dropdowns
    public function getProvinces()
    {
        $provinces = Province::orderBy('name')->get(['id', 'name', 'code']);
        return response()->json($provinces);
    }

    public function getCities($province_id)
    {
        $cities = City::where('province_id', $province_id)->orderBy('name')->get(['id', 'name', 'type', 'code']);
        return response()->json($cities);
    }

    public function getDistricts($city_id)
    {
        $districts = District::where('city_id', $city_id)->orderBy('name')->get(['id', 'name', 'code']);
        return response()->json($districts);
    }

    public function getSubdistricts($district_id)
    {
        $subdistricts = Subdistrict::where('district_id', $district_id)->orderBy('name')->get(['id', 'name', 'postal_code']);
        return response()->json($subdistricts);
    }
}
