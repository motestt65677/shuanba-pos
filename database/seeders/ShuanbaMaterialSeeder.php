<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShuanbaMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $materials = array(
            0 => array('supplierName' => '金賀水產', 'materialName' => '南美白蝦 4/5 #3', 'materialUnit' => '隻'),
            1 => array('supplierName' => '金賀水產', 'materialName' => '南美白蝦5/6 #4', 'materialUnit' => '隻'),
            2 => array('supplierName' => '金賀水產', 'materialName' => '草蝦', 'materialUnit' => '隻'),
            3 => array('supplierName' => '金賀水產', 'materialName' => '蛤蜊', 'materialUnit' => 'g'),
            4 => array('supplierName' => '金賀水產', 'materialName' => '小卷', 'materialUnit' => '隻'),
            5 => array('supplierName' => '金賀水產', 'materialName' => '船凍小龍蝦350-400', 'materialUnit' => '隻'),
            6 => array('supplierName' => '金賀水產', 'materialName' => '海鯛雙背250-300', 'materialUnit' => 'g'),
            7 => array('supplierName' => '金賀水產', 'materialName' => '鱸魚片', 'materialUnit' => 'g'),
            8 => array('supplierName' => '金賀水產', 'materialName' => '帶皮鯰魚排220/300/kg', 'materialUnit' => 'g'),
            9 => array('supplierName' => '金賀水產', 'materialName' => '日本干貝2s', 'materialUnit' => '隻'),
            10 => array('supplierName' => '金賀水產', 'materialName' => '現流鮮蚵', 'materialUnit' => 'g'),
            11 => array('supplierName' => '金賀水產', 'materialName' => '黃金蜆', 'materialUnit' => 'g'),
            12 => array('supplierName' => '扇貝9/10', 'materialName' => '扇貝9/10', 'materialUnit' => '隻'),
            13 => array('supplierName' => '雞鴨林', 'materialName' => '去骨雞腿肉', 'materialUnit' => '隻'),
            14 => array('supplierName' => '聯碩食品', 'materialName' => '美國培根牛', 'materialUnit' => 'g'),
            15 => array('supplierName' => '聯碩食品', 'materialName' => '1855ch板腱', 'materialUnit' => 'g'),
            16 => array('supplierName' => '聯碩食品', 'materialName' => '1855ch翼板', 'materialUnit' => 'g'),
            17 => array('supplierName' => '聯碩食品', 'materialName' => '台灣豬松坂層疊', 'materialUnit' => 'g'),
            18 => array('supplierName' => '傑恩 嘉禾食品', 'materialName' => '去皮豬五花', 'materialUnit' => 'g'),
            19 => array('supplierName' => '傑恩 嘉禾食品', 'materialName' => '梅花豬', 'materialUnit' => 'g'),
            20 => array('supplierName' => '傑恩 嘉禾食品', 'materialName' => '小羔羊', 'materialUnit' => 'g'),
            21 => array('supplierName' => '傑恩 嘉禾食品', 'materialName' => '進口羊肉卷', 'materialUnit' => 'g'),
            22 => array('supplierName' => '傑恩 嘉禾食品', 'materialName' => 'ch牛小排', 'materialUnit' => 'g'),
            23 => array('supplierName' => '傑恩 嘉禾食品', 'materialName' => 'pr牛小排', 'materialUnit' => 'g'),
            24 => array('supplierName' => '新德里', 'materialName' => '日本和牛冷凍休清胸腹肉', 'materialUnit' => 'g'),
            25 => array('supplierName' => '新德里', 'materialName' => '澳洲和牛板腱', 'materialUnit' => 'g'),
            26 => array('supplierName' => '新德里', 'materialName' => '澳洲和牛板腱', 'materialUnit' => 'g'),
            27 => array('supplierName' => '美福', 'materialName' => '板腱-極黑和牛金牌', 'materialUnit' => 'g'),
            28 => array('supplierName' => '美福', 'materialName' => '無骨牛小排-極黑和牛', 'materialUnit' => 'g'),
            29 => array('supplierName' => '美福', 'materialName' => 'US-板腱-PRIM', 'materialUnit' => 'g'),
            30 => array('supplierName' => '美福', 'materialName' => '雪花牛-極黑和牛', 'materialUnit' => 'g'),
            31 => array('supplierName' => '美福', 'materialName' => 'US莎朗CHOICE', 'materialUnit' => 'g'),
            32 => array('supplierName' => '統賀', 'materialName' => '伊比利豬', 'materialUnit' => 'g'),
            33 => array('supplierName' => '品高', 'materialName' => '麻辣汁', 'materialUnit' => 'g'),
            34 => array('supplierName' => '品高', 'materialName' => '大骨高湯', 'materialUnit' => 'g'),
            35 => array('supplierName' => '品高', 'materialName' => '素食高湯', 'materialUnit' => 'g'),
            36 => array('supplierName' => '麻辣先生', 'materialName' => '麻辣醬', 'materialUnit' => 'g'),
            37 => array('supplierName' => '麻辣先生', 'materialName' => '酸菜', 'materialUnit' => 'g'),
            38 => array('supplierName' => '麻辣先生', 'materialName' => '青花椒', 'materialUnit' => 'g'),
            39 => array('supplierName' => '麻辣先生', 'materialName' => '紅花椒', 'materialUnit' => 'g'),
            40 => array('supplierName' => '麻辣先生', 'materialName' => '椒香紅油', 'materialUnit' => 'g'),
            41 => array('supplierName' => '佳信食品', 'materialName' => '胡椒雞湯', 'materialUnit' => 'g'),
            42 => array('supplierName' => '佳信食品', 'materialName' => '濃口豚骨白湯', 'materialUnit' => 'g'),
            43 => array('supplierName' => '鮮味嘉', 'materialName' => '純肉貢丸', 'materialUnit' => '個'),
            44 => array('supplierName' => '鮮味嘉', 'materialName' => '手工魚繳', 'materialUnit' => '個'),
            45 => array('supplierName' => '鮮味嘉', 'materialName' => '雪魚豆腐', 'materialUnit' => '個'),
            46 => array('supplierName' => '鮮味嘉', 'materialName' => '魯條', 'materialUnit' => '個'),
            47 => array('supplierName' => '鮮味嘉', 'materialName' => '非基改凍豆腐', 'materialUnit' => '個'),
            48 => array('supplierName' => '鮮味嘉', 'materialName' => '九層塔花枝', 'materialUnit' => '個'),
            49 => array('supplierName' => '鮮味嘉', 'materialName' => '黃金魚蛋', 'materialUnit' => '個'),
            50 => array('supplierName' => '鮮味嘉', 'materialName' => '三角油豆腐', 'materialUnit' => '個'),
            51 => array('supplierName' => '合毅', 'materialName' => '米血糕', 'materialUnit' => '個'),
            52 => array('supplierName' => '合毅', 'materialName' => '芋頭角', 'materialUnit' => '個'),
            53 => array('supplierName' => '合毅', 'materialName' => '手工土雞蛋餃', 'materialUnit' => '個'),
            54 => array('supplierName' => '合毅', 'materialName' => '冰淇淋', 'materialUnit' => '加侖'),
            55 => array('supplierName' => '有賀', 'materialName' => '鴨血', 'materialUnit' => '個'),
            56 => array('supplierName' => '有賀', 'materialName' => '秦檜油條', 'materialUnit' => '個'),
            57 => array('supplierName' => '美食家', 'materialName' => '特選粉絲', 'materialUnit' => 'g'),
            58 => array('supplierName' => '美食家', 'materialName' => '王子麵', 'materialUnit' => '包'),
            59 => array('supplierName' => '美食家', 'materialName' => '久代大角螺', 'materialUnit' => '個'),
            60 => array('supplierName' => '美食家', 'materialName' => '大橋台灣越光米', 'materialUnit' => 'g'),
            61 => array('supplierName' => '美食家', 'materialName' => '工研白醋', 'materialUnit' => 'g'),
            62 => array('supplierName' => '美食家', 'materialName' => '工研味林', 'materialUnit' => 'g'),
            63 => array('supplierName' => '美食家', 'materialName' => '統一龜甲萬原味醬油', 'materialUnit' => 'g'),
            64 => array('supplierName' => '美食家', 'materialName' => '昆布', 'materialUnit' => 'g'),
            65 => array('supplierName' => '美食家', 'materialName' => '土平熟花生角', 'materialUnit' => 'g'),
            66 => array('supplierName' => '美食家', 'materialName' => '雲耳', 'materialUnit' => 'g'),
            67 => array('supplierName' => '美食家', 'materialName' => '元氣白胡麻調味料', 'materialUnit' => 'g'),
            68 => array('supplierName' => '美食家', 'materialName' => '大漢非基改小板豆腐', 'materialUnit' => 'g'),
            69 => array('supplierName' => '美食家', 'materialName' => '紅標料理米酒', 'materialUnit' => 'g'),
            70 => array('supplierName' => '美食家', 'materialName' => '鰹魚烹大師', 'materialUnit' => 'g'),
            71 => array('supplierName' => '美食家', 'materialName' => '皇冠丹麥梅花豬', 'materialUnit' => 'g'),
            72 => array('supplierName' => '美食家', 'materialName' => '竹葉', 'materialUnit' => '片'),
            73 => array('supplierName' => '美食家', 'materialName' => '牛寶沙茶醬', 'materialUnit' => 'g'),
            74 => array('supplierName' => '美食家', 'materialName' => '昌泰熟白芝麻粒', 'materialUnit' => 'g'),
            75 => array('supplierName' => '美食家', 'materialName' => '鴻運冷凍豆包', 'materialUnit' => 'g'),
        );

        foreach($materials as $material){
            $supplier = Supplier::where("name", $material["supplierName"])->first();
            if(!$supplier){
                $supplier = Supplier::create([
                    'supplier_no' => $this->newSupplierNo(),
                    'name' => $material["supplierName"],
                ]);
            }
            $thisMaterial = Material::where("name", $material["materialName"])->first();
            if(!$thisMaterial){
                Material::create([
                    'material_no' => $this->newMaterialNo(),
                    'supplier_id' => $supplier->id,
                    'name' => $material["materialName"],
                    'unit' => $material["materialUnit"]
                ]);
            }
        }
    }
    public function newMaterialNo(){
        $year = date("Y");
        $month = date("m");

        $numStr = "0001";
        $char = "M";

        $sql = "SELECT RIGHT(material_no,4) AS num FROM `materials`
                WHERE material_no LIKE '{$char}%'
                ORDER BY material_no DESC
                LIMIT 1
        ";
        $rt = DB::select($sql);
        if(count($rt) == 1){
            $int = (int)$rt["0"]->num;
            $int += 1;
            $numStr = substr("00000".(string)$int, -4);
        }
        return $char . $numStr;
    }

    public function newSupplierNo(){

        $numStr = "001";
        $char = "F";

        $sql = "SELECT RIGHT(supplier_no,3) AS num FROM `suppliers`
                WHERE supplier_no LIKE '{$char}%'
                ORDER BY supplier_no DESC
                LIMIT 1
        ";
        $rt = DB::select($sql);
        if(count($rt) == 1){
            $int = (int)$rt["0"]->num;
            $int += 1;
            $numStr = substr("0000".(string)$int, -3);
        }
        return $char . $numStr;
    }
}
