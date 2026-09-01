<?php

namespace Database\Seeders;

use App\Models\Subdistrict;
use App\Models\Tariff;
use Illuminate\Database\Seeder;

class TariffSeeder extends Seeder
{
    public function run(): void
    {
        // Coordinates (approx lat/lng) for Java regions to estimate distance
        $cityCoordinates = [
            'Jakarta Selatan' => ['lng' => 106.81, 'lat' => -6.27, 'prov' => 'DKI Jakarta'],
            'Jakarta Pusat' => ['lng' => 106.84, 'lat' => -6.18, 'prov' => 'DKI Jakarta'],
            'Jakarta Barat' => ['lng' => 106.75, 'lat' => -6.16, 'prov' => 'DKI Jakarta'],
            'Jakarta Timur' => ['lng' => 106.89, 'lat' => -6.22, 'prov' => 'DKI Jakarta'],
            'Kota Bandung' => ['lng' => 107.61, 'lat' => -6.91, 'prov' => 'Jawa Barat'],
            'Kota Bogor' => ['lng' => 106.79, 'lat' => -6.59, 'prov' => 'Jawa Barat'],
            'Kota Depok' => ['lng' => 106.82, 'lat' => -6.40, 'prov' => 'Jawa Barat'],
            'Kota Bekasi' => ['lng' => 106.99, 'lat' => -6.23, 'prov' => 'Jawa Barat'],
            'Kota Tangerang' => ['lng' => 106.63, 'lat' => -6.17, 'prov' => 'Banten'],
            'Kota Semarang' => ['lng' => 110.42, 'lat' => -6.99, 'prov' => 'Jawa Tengah'],
            'Kota Surakarta' => ['lng' => 110.82, 'lat' => -7.56, 'prov' => 'Jawa Tengah'],
            'Kota Yogyakarta' => ['lng' => 110.37, 'lat' => -7.79, 'prov' => 'DI Yogyakarta'],
            'Kota Surabaya' => ['lng' => 112.75, 'lat' => -7.25, 'prov' => 'Jawa Timur'],
            'Kota Malang' => ['lng' => 112.63, 'lat' => -7.98, 'prov' => 'Jawa Timur'],
        ];

        // Fetch sample subdistricts from DB across Java
        $subdistricts = Subdistrict::with(['district.city.province'])->get();

        if ($subdistricts->isEmpty()) {
            $this->command->info('Tidak ada data kelurahan di database. Harap jalankan import/seeder wilayah terlebih dahulu.');
            return;
        }

        $services = ['Regular', 'Express', 'SameDay'];
        $count = 0;

        // Shuffle or pick representative subdistricts to create inter-kelurahan tariffs
        $subdistArray = $subdistricts->toArray();

        foreach ($subdistArray as $origin) {
            $origCityName = $origin['district']['city']['name'] ?? 'Jakarta Selatan';
            $origProvName = $origin['district']['city']['province']['name'] ?? 'DKI Jakarta';
            $origDistName = $origin['district']['name'] ?? null;
            $origSubdistName = $origin['name'];

            $origCoord = $cityCoordinates[$origCityName] ?? ['lng' => 106.81, 'lat' => -6.27];

            // Pick 8-12 destinations per subdistrict across Java
            $randomDests = collect($subdistArray)->random(min(12, count($subdistArray)));

            foreach ($randomDests as $dest) {
                $destCityName = $dest['district']['city']['name'] ?? 'Kota Bandung';
                $destProvName = $dest['district']['city']['province']['name'] ?? 'Jawa Barat';
                $destDistName = $dest['district']['name'] ?? null;
                $destSubdistName = $dest['name'];

                $destCoord = $cityCoordinates[$destCityName] ?? ['lng' => 107.61, 'lat' => -6.91];

                // Calculate distance factor in kilometers (approx)
                $dx = ($origCoord['lng'] - $destCoord['lng']) * 111;
                $dy = ($origCoord['lat'] - $destCoord['lat']) * 111;
                $distanceKm = max(5, sqrt($dx * $dx + $dy * $dy));

                // Same kelurahan / same district check
                $isSameSubdist = ($origSubdistName === $destSubdistName && $origCityName === $destCityName);
                $isSameCity = ($origCityName === $destCityName);

                foreach ($services as $service) {
                    if ($service === 'SameDay' && !$isSameCity && $distanceKm > 60) {
                        continue; // Skip SameDay for long distance
                    }

                    // Base rate calculation based on distance & service type
                    if ($isSameSubdist) {
                        $baseRate = match ($service) {
                            'Express' => rand(12, 15) * 1000,
                            'SameDay' => rand(18, 22) * 1000,
                            default => rand(8, 10) * 1000,
                        };
                        $estDays = match ($service) {
                            'Express' => '1 Hari',
                            'SameDay' => 'Hari Ini Tiba',
                            default => '1-2 Hari',
                        };
                    } else if ($isSameCity) {
                        $baseRate = match ($service) {
                            'Express' => rand(18, 22) * 1000,
                            'SameDay' => rand(28, 35) * 1000,
                            default => rand(10, 14) * 1000,
                        };
                        $estDays = match ($service) {
                            'Express' => '1 Hari',
                            'SameDay' => 'Hari Ini Tiba',
                            default => '1-2 Hari',
                        };
                    } else {
                        // Inter-city / inter-province calculation
                        $multiplier = match ($service) {
                            'Express' => 250,
                            'SameDay' => 450,
                            default => 160,
                        };
                        $calculatedRate = (int) round(($distanceKm * $multiplier + rand(5000, 12000)) / 1000) * 1000;
                        $baseRate = match ($service) {
                            'Express' => max(20000, min(65000, $calculatedRate)),
                            'SameDay' => max(35000, min(90000, $calculatedRate)),
                            default => max(12000, min(45000, $calculatedRate)),
                        };

                        if ($distanceKm < 150) {
                            $estDays = match ($service) {
                                'Express' => '1 Hari',
                                'SameDay' => 'Hari Ini Tiba',
                                default => '2-3 Hari',
                            };
                        } else {
                            $estDays = match ($service) {
                                'Express' => '1-2 Hari',
                                default => '3-4 Hari',
                            };
                        }
                    }

                    Tariff::updateOrCreate([
                        'origin_city' => $origCityName,
                        'destination_city' => $destCityName,
                        'origin_subdistrict' => $origSubdistName,
                        'destination_subdistrict' => $destSubdistName,
                        'service_type' => $service,
                    ], [
                        'origin_province' => $origProvName,
                        'origin_district' => $origDistName,
                        'destination_province' => $destProvName,
                        'destination_district' => $destDistName,
                        'price_per_kg' => $baseRate,
                        'min_weight_kg' => 1.0,
                        'estimated_days' => $estDays,
                    ]);

                    $count++;
                }
            }
        }

        $this->command->info("Seeder tarif antar kelurahan se-Pulau Jawa berhasil memproses {$count} data tarif.");
    }
}
