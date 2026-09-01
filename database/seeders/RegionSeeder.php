<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\Subdistrict;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            "DKI Jakarta" => [
                "Jakarta Selatan" => [
                    "Cilandak" => [
                        ["name" => "Cilandak Barat", "postal_code" => "12430"],
                        ["name" => "Cipete Selatan", "postal_code" => "12410"],
                        ["name" => "Lebak Bulus", "postal_code" => "12440"],
                        ["name" => "Gandaria Selatan", "postal_code" => "12420"],
                    ],
                    "Kebayoran Baru" => [
                        ["name" => "Senayan", "postal_code" => "12190"],
                        ["name" => "Gandaria Utara", "postal_code" => "12140"],
                        ["name" => "Kramat Pela", "postal_code" => "12130"],
                        ["name" => "Melawai", "postal_code" => "12160"],
                    ],
                    "Jagakarsa" => [
                        ["name" => "Ciganjur", "postal_code" => "12630"],
                        ["name" => "Srengseng Sawah", "postal_code" => "12640"],
                        ["name" => "Jagakarsa", "postal_code" => "12620"],
                    ]
                ],
                "Jakarta Barat" => [
                    "Kembangan" => [
                        ["name" => "Kembangan Selatan", "postal_code" => "11610"],
                        ["name" => "Meruya Utara", "postal_code" => "11620"],
                        ["name" => "Meruya Selatan", "postal_code" => "11650"],
                    ],
                    "Kebon Jeruk" => [
                        ["name" => "Duri Kepa", "postal_code" => "11510"],
                        ["name" => "Kedoya Selatan", "postal_code" => "11520"],
                        ["name" => "Kebon Jeruk", "postal_code" => "11530"],
                    ]
                ],
                "Jakarta Pusat" => [
                    "Tanah Abang" => [
                        ["name" => "Bendungan Hilir", "postal_code" => "10210"],
                        ["name" => "Gelora", "postal_code" => "10270"],
                        ["name" => "Karet Tengsin", "postal_code" => "10220"],
                    ],
                    "Menteng" => [
                        ["name" => "Cikini", "postal_code" => "10330"],
                        ["name" => "Gondangdia", "postal_code" => "10350"],
                        ["name" => "Menteng", "postal_code" => "10310"],
                    ]
                ]
            ],
            "Jawa Barat" => [
                "Kota Bandung" => [
                    "Coblong" => [
                        ["name" => "Dago", "postal_code" => "40135"],
                        ["name" => "Lebak Siliwangi", "postal_code" => "40132"],
                        ["name" => "Sadang Serang", "postal_code" => "40133"],
                        ["name" => "Sekeloa", "postal_code" => "40134"],
                    ],
                    "Sumur Bandung" => [
                        ["name" => "Babakan Ciamis", "postal_code" => "40111"],
                        ["name" => "Kebon Pisang", "postal_code" => "40112"],
                        ["name" => "Merdeka", "postal_code" => "40113"],
                    ],
                    "Cibeunying Kaler" => [
                        ["name" => "Cihaurgeulis", "postal_code" => "40122"],
                        ["name" => "Sukaluyu", "postal_code" => "40123"],
                        ["name" => "Cigadung", "postal_code" => "40124"],
                    ],
                    "Cicendo" => [
                        ["name" => "Pasirkaliki", "postal_code" => "40171"],
                        ["name" => "Arjuna", "postal_code" => "40172"],
                        ["name" => "Husein Sastranegara", "postal_code" => "40174"],
                    ],
                    "Lengkong" => [
                        ["name" => "Malabar", "postal_code" => "40262"],
                        ["name" => "Turangga", "postal_code" => "40264"],
                        ["name" => "Burangrang", "postal_code" => "40265"],
                    ]
                ],
                "Kota Bekasi" => [
                    "Bekasi Barat" => [
                        ["name" => "Kranji", "postal_code" => "17135"],
                        ["name" => "Bintara", "postal_code" => "17134"],
                        ["name" => "Kota Baru", "postal_code" => "17133"],
                    ],
                    "Bekasi Selatan" => [
                        ["name" => "Pekayon Jaya", "postal_code" => "17148"],
                        ["name" => "Kayuringin Jaya", "postal_code" => "17144"],
                        ["name" => "Jaka Setia", "postal_code" => "17147"],
                    ]
                ],
                "Kota Depok" => [
                    "Beji" => [
                        ["name" => "Beji Timur", "postal_code" => "16422"],
                        ["name" => "Pondok Cina", "postal_code" => "16424"],
                        ["name" => "Kukusan", "postal_code" => "16425"],
                    ],
                    "Pancoran Mas" => [
                        ["name" => "Depok", "postal_code" => "16431"],
                        ["name" => "Mampang", "postal_code" => "16433"],
                        ["name" => "Rangkapan Jaya", "postal_code" => "16435"],
                    ]
                ],
                "Kota Bogor" => [
                    "Bogor Tengah" => [
                        ["name" => "Paledang", "postal_code" => "16122"],
                        ["name" => "Babakan", "postal_code" => "16128"],
                        ["name" => "Gudang", "postal_code" => "16123"],
                    ]
                ],
                "Kab. Bogor" => [
                    "Cibinong" => [
                        ["name" => "Cirimekar", "postal_code" => "16911"],
                        ["name" => "Ciriung", "postal_code" => "16918"],
                        ["name" => "Pabuaran", "postal_code" => "16916"],
                    ],
                    "Cileungsi" => [
                        ["name" => "Cileungsi", "postal_code" => "16820"],
                        ["name" => "Pasir Angin", "postal_code" => "16825"],
                    ]
                ]
            ],
            "Jawa Timur" => [
                "Kota Surabaya" => [
                    "Genteng" => [
                        ["name" => "Embong Kaliasin", "postal_code" => "60271"],
                        ["name" => "Ketabang", "postal_code" => "60272"],
                        ["name" => "Kapasari", "postal_code" => "60273"],
                    ],
                    "Tegalsari" => [
                        ["name" => "Wonorejo", "postal_code" => "60263"],
                        ["name" => "Keputran", "postal_code" => "60265"],
                        ["name" => "Dr. Soetomo", "postal_code" => "60264"],
                    ],
                    "Gubeng" => [
                        ["name" => "Airlangga", "postal_code" => "60286"],
                        ["name" => "Kertajaya", "postal_code" => "60282"],
                        ["name" => "Mojo", "postal_code" => "60285"],
                    ]
                ],
                "Kota Malang" => [
                    "Klojen" => [
                        ["name" => "Oro-Oro Dowo", "postal_code" => "65119"],
                        ["name" => "Kauman", "postal_code" => "65119"],
                    ],
                    "Lowokwaru" => [
                        ["name" => "Jatimulyo", "postal_code" => "65141"],
                        ["name" => "Mojolangu", "postal_code" => "65142"],
                    ]
                ]
            ],
            "Jawa Tengah" => [
                "Kota Semarang" => [
                    "Semarang Tengah" => [
                        ["name" => "Pandansari", "postal_code" => "50139"],
                        ["name" => "Sekayu", "postal_code" => "50132"],
                    ],
                    "Banyumanik" => [
                        ["name" => "Srondol Kulon", "postal_code" => "50263"],
                        ["name" => "Ngesrep", "postal_code" => "50261"],
                    ]
                ],
                "Kota Surakarta (Solo)" => [
                    "Banjarsari" => [
                        ["name" => "Kadipiro", "postal_code" => "57136"],
                        ["name" => "Manahan", "postal_code" => "57139"],
                    ],
                    "Jebres" => [
                        ["name" => "Jebres", "postal_code" => "57126"],
                        ["name" => "Mojosongo", "postal_code" => "57127"],
                    ]
                ]
            ],
            "Banten" => [
                "Kota Tangerang" => [
                    "Tangerang" => [
                        ["name" => "Babakan", "postal_code" => "15118"],
                        ["name" => "Sukasari", "postal_code" => "15118"],
                    ],
                    "Karawaci" => [
                        ["name" => "Cimone", "postal_code" => "15114"],
                        ["name" => "Bojong Jaya", "postal_code" => "15115"],
                    ]
                ],
                "Kota Tangerang Selatan" => [
                    "Serpong" => [
                        ["name" => "BSD / Lengkong Gudang", "postal_code" => "15321"],
                        ["name" => "Serpong", "postal_code" => "15311"],
                    ],
                    "Ciputat" => [
                        ["name" => "Cipayung", "postal_code" => "15417"],
                        ["name" => "Ciputat", "postal_code" => "15411"],
                    ]
                ]
            ],
            "D.I. Yogyakarta" => [
                "Kota Yogyakarta" => [
                    "Gondokusuman" => [
                        ["name" => "Terban", "postal_code" => "55223"],
                        ["name" => "Kotabaru", "postal_code" => "55224"],
                    ],
                    "Umbulharjo" => [
                        ["name" => "Giwangan", "postal_code" => "55163"],
                        ["name" => "Pandeyan", "postal_code" => "55161"],
                    ]
                ]
            ],
            "Bali" => [
                "Kota Denpasar" => [
                    "Denpasar Selatan" => [
                        ["name" => "Sanur", "postal_code" => "80228"],
                        ["name" => "Renon", "postal_code" => "80226"],
                    ],
                    "Denpasar Barat" => [
                        ["name" => "Pemecutan", "postal_code" => "80119"],
                        ["name" => "Padangsambian", "postal_code" => "80117"],
                    ]
                ]
            ],
            "Sumatera Utara" => [
                "Kota Medan" => [
                    "Medan Kota" => [
                        ["name" => "Pasar Baru", "postal_code" => "20212"],
                        ["name" => "Teladan", "postal_code" => "20217"],
                    ],
                    "Medan Petisah" => [
                        ["name" => "Sekip", "postal_code" => "20113"],
                        ["name" => "Petisah Tengah", "postal_code" => "20112"],
                    ]
                ]
            ]
        ];

        $provIdx = 1;
        $cityIdx = 1;
        $distIdx = 1;

        foreach ($data as $provName => $cities) {
            $province = Province::firstOrCreate(
                ['name' => $provName],
                ['code' => 'PRV-' . str_pad($provIdx++, 3, '0', STR_PAD_LEFT)]
            );

            foreach ($cities as $cityName => $districts) {
                $cityType = str_contains($cityName, 'Kab') ? 'Kabupaten' : 'Kota';
                $city = City::firstOrCreate(
                    ['province_id' => $province->id, 'name' => $cityName],
                    ['code' => 'CTY-' . str_pad($cityIdx++, 3, '0', STR_PAD_LEFT), 'type' => $cityType]
                );

                foreach ($districts as $distName => $subdistricts) {
                    $district = District::firstOrCreate(
                        ['city_id' => $city->id, 'name' => $distName],
                        ['code' => 'DST-' . str_pad($distIdx++, 3, '0', STR_PAD_LEFT)]
                    );

                    foreach ($subdistricts as $sd) {
                        Subdistrict::firstOrCreate(
                            ['district_id' => $district->id, 'name' => $sd['name']],
                            ['postal_code' => $sd['postal_code']]
                        );
                    }
                }
            }
        }
    }
}
