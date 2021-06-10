<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Material;
use App\Models\ProductMaterial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //[]
       $products = [
            "多利魚" => 180,
            "鮮先活蛤蜊" => 180,
            "澎湖軟絲" => 180,
            "南澳甜蝦" => 180,
            "頂級鯛魚" => 180,
            "東石鮮蚵(210g)" => 150,
            "10P草蝦" => 230, 
            "七星鱸魚" => 180,
            "汶萊藍鑽蝦" => 160,
            "日本A級干貝" => 220,
            "三文魚" => 180,
            "北海小卷" =>150,
            "霸王海鮮盤單點(龍蝦1·干貝2·扇貝2·蛤蜊30 白蝦20)" => 1688
        ];

        foreach($products as $product => $price){
            $exists = Product::where("name", $product)->first();
            if($exists)
                continue;

            Product::create([
                "name" => $product,
                "price" => $price,
            ]);
        }

        $productMaterials = [
            "多利魚" => [],
            "鮮先活蛤蜊" => [["name" => "蛤蜊-特特", "material_count" => 10]],
            "澎湖軟絲" => [],
            "南澳甜蝦" => [],
            "頂級鯛魚" => [],
            "東石鮮蚵(210g)" => [],
            "10P草蝦" => [["name" => "南美生白蝦#3", "material_count" => 5]],
            "七星鱸魚" => [["name" => "七星鱸魚片300/400", "material_count" => 1]],
            "汶萊藍鑽蝦" => [],
            "日本A級干貝" => [["name" => "日本干貝(生食)2S", "material_count" => 2]],
            "三文魚" => [],
            "北海小卷" =>[],
            "霸王海鮮盤單點(龍蝦1·干貝2·扇貝2·蛤蜊30 白蝦20)" => []
        ];
        foreach($productMaterials as $productName => $pms){
            $product = Product::where("name", $productName)->first();
            if(!$product)
                continue;
            foreach($pms as $pm){
                $material = Material::where("name", $pm["name"])->first();
                if(!$material)
                    continue;

                ProductMaterial::create([
                    "product_id" =>$product->id,
                    "material_id" => $material->id,
                    "material_count" => $pm["material_count"]
                ]);
            }
        }
    }
}
