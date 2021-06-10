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
        $this->supplierService = app()->make('SupplierService');
        $this->materialService = app()->make('MaterialService');

        //
        // "S20210310001" => [
        //     "name" => "金賀水產",
        //     "phone" => "",
        //     "cellphone" => "",
        //     "tax_id" => "",
        //     "address" => ""
        // ],
        $suppliers = [
            [
                "name" => "勤億(雞蛋)",
                "materials" => [
                    "M0001" => [
                        "name" => "機能蛋",
                        "unit" => "個",
                        "unit_price" => "6.2",
                    ]
                ]
            ],
            [
                "name" => "美食家",
                "materials" => [
                    [
                        "name" => "王子麵",
                        "unit" => "個",
                        "unit_price" => "4.25",
                    ],
                    [
                        "name" => "久代大角螺",
                        "unit" => "kg",
                        "unit_price" => "85",
                    ]
                ]
            ],
            [
                "name" => "金賀水產",
                "materials" => [
                    [
                        "name" => "海鯛雙背",
                        "unit" => "250/300",
                        "unit_price" => "250",
                    ],
                    [
                        "name" => "船凍小龍蝦",
                        "unit" => "300/350-15隻",
                        "unit_price" => "740",
                    ],
                    [
                        "name" => "七星鱸魚片",
                        "unit" => "300/400",
                        "unit_price" => "350",
                    ],
                    [
                        "name" => "蛤蜊-特特",
                        "unit" => "一盒",
                        "unit_price" => "95",
                    ],
                    [
                        "name" => "南美生白蝦#3",
                        "unit" => "一箱",
                        "unit_price" => "270",
                    ],
                    [
                        "name" => "七星鱸魚片300/400",
                        "unit" => "300/400",
                        "unit_price" => "350",
                    ],
                    [
                        "name" => "海鯛雙背250/300",
                        "unit" => "250/300",
                        "unit_price" => "250",
                    ],
                    [
                        "name" => "扇貝9/10",
                        "unit" => "9/10",
                        "unit_price" => "105",
                    ],
                    [
                        "name" => "船凍小龍蝦350/400",
                        "unit" => "350/400",
                        "unit_price" => "740",
                    ],
                    [
                        "name" => "現流鮮蚵",
                        "unit" => "一箱",
                        "unit_price" => "155",
                    ],
                    [
                        "name" => "黃金蜆",
                        "unit" => "一箱",
                        "unit_price" => "95",
                    ],
                    [
                        "name" => "日本干貝(生食)2S",
                        "unit" => "一箱",
                        "unit_price" => "990",
                    ],
                    [
                        "name" => "草蝦10P",
                        "unit" => "10P",
                        "unit_price" => "120",
                    ],
                    [
                        "name" => "帶皮鯰魚排220/300/kg",
                        "unit" => "220/300/kg",
                        "unit_price" => "140",
                    ]
                ]
            ]
        ];

        foreach($suppliers as $supplier){
            $supplierExists = Supplier::where("name", $supplier["name"])->first();
            $thisSupplier;
            if($supplierExists){
                $thisSupplier = $supplierExists;
            } else {
                $thisSupplier = Supplier::create(
                    [
                        "supplier_no" => $this->supplierService->newSupplierNo(),
                        "name" => $supplier["name"]
                    ]
                );
            }
            
            foreach($supplier["materials"] as $material){
                $materialExists = Material::where("name", $material["name"])->first();
                if($materialExists)
                    continue;
                Material::create([
                    "supplier_id" => $thisSupplier ->id,
                    "material_no" => $this->materialService->newMaterialNo(),
                    "name" => $material["name"],
                    "unit" => $material["unit"],
                    "unit_price" => $material["unit_price"],
                ]);
            }
        }
    }
}

