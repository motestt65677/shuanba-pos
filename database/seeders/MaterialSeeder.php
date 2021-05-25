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
            "F001" => [
                "name" => "勤億(雞蛋)",
                "materials" => [
                    "M0001" => [
                        "name" => "機能蛋",
                        "unit" => "個",
                        "unit_price" => "6.2",
                    ]
                ]
            ],
            "F002" => [
                "name" => "美食家",
                "materials" => [
                    "M0002" => [
                        "name" => "王子麵",
                        "unit" => "40/箱",
                        "unit_price" => "170",
                    ],
                    "M0003" => [
                        "name" => "久代大角螺",
                        "unit" => "3kg",
                        "unit_price" => "255",
                    ]
                ]
            ],
            "F003" => [
                "name" => "金賀水產",
                "materials" => [
                    "M0004" => [
                        "name" => "海鯛雙背",
                        "unit" => "250/300",
                        "unit_price" => "250",
                    ],
                    "M0005" => [
                        "name" => "船凍小龍蝦",
                        "unit" => "300/350-15隻",
                        "unit_price" => "740",
                    ],
                    "M0006" => [
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

