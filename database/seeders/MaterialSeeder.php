<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // "S20210310001" => [
        //     "name" => "金賀水產",
        //     "phone" => "",
        //     "cellphone" => "",
        //     "tax_id" => "",
        //     "address" => ""
        // ],
        $suppliers = [
            "S20210310001" => [
                "name" => "勤億(雞蛋)",
                "materials" => [
                    "M20210310001" => [
                        "name" => "機能蛋",
                        "unit" => "個",
                        "unit_price" => "6.2",
                    ]

                ]
            ],
            "S20210310002" => [
                "name" => "美食家",
                "materials" => [
                    "M20210310002" => [
                        "name" => "王子麵",
                        "unit" => "40/箱",
                        "unit_price" => "170",
                    ],
                    "M20210310004" => [
                        "name" => "久代大角螺",
                        "unit" => "3kg",
                        "unit_price" => "255",
                    ]
                ]
            ],
            "S20210310003" => [
                "name" => "金賀水產",
                "materials" => [
                    "M20210310005" => [
                        "name" => "海鯛雙背",
                        "unit" => "250/300",
                        "unit_price" => "250",
                    ],
                    "M20210310006" => [
                        "name" => "船凍小龍蝦",
                        "unit" => "300/350-15隻",
                        "unit_price" => "740",
                    ],
                    "M20210310007" => [
                        "name" => "七星鱸魚片",
                        "unit" => "300/400",
                        "unit_price" => "350",
                    ]
                ]
            ]
        ];

        foreach($suppliers as $key => $supplier){
            $thisSupplier = Supplier::create(
                [
                    "supplier_no" => $key,
                    "name" => $supplier["name"]
                ]
            );
            foreach($supplier["materials"] as $key => $material){
                Material::create([
                    "supplier_id" => $thisSupplier ->id,
                    "material_no" => $key,
                    "name" => $material["name"],
                    "unit" => $material["unit"],
                    "unit_price" => $material["unit_price"],
                ]);
            }
        }
    }
}
