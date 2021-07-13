<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ImportService
{
    public function newImportNo(){
        $numStr = "0001";
        $char = "I";

        $sql = "SELECT RIGHT(import_no,4) AS num FROM `imports`
                WHERE import_no LIKE '{$char}%'
                ORDER BY import_no DESC
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

    public function queryData($search = [], $order = []){
        $query = DB::table('imports')
        ->select(
            "imports.id AS import_id",
            "imports.import_no AS import_no",
            "imports.name AS import_name",
            "imports.description AS import_description",
            "imports.price AS import_price",
            "import_materials.material_count AS material_count",
            "materials.name AS material_name",
            "materials.material_no AS material_no",
        )
        ->leftJoin("import_materials", "imports.id", "=", "import_materials.import_id")
        ->leftJoin("materials", "materials.id", "=", "import_materials.material_id");

        if(isset($search["import_id"]))
            $query->where("imports.id", $search["import_id"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        $importDict = [];

        foreach($items as $item){
            if(!isset($importDict[$item->import_id])){

                $importDict[$item->import_id] = [
                    "import_id" => $item->import_id,
                    "import_no" => $item->import_no,
                    "import_name" => $item->import_name,
                    "import_description" => $item->import_description,
                    "import_price" => $item->import_price,
                    "import_material_count" => 0,
                    "import_material_list" => ""
                ];
            }

            if(isset($item->material_count)){
                $importDict[$item->import_id]["import_material_count"]++;
                $importDict[$item->import_id]["import_material_list"] .= $item->material_no . '(' .$item->material_name . ') ' . '  &times;  ' . round($item-> material_count,2) . "</br>";
            }
        }

        $returnArray = [];
        foreach($importDict as $key=>$value){
            array_push($returnArray, $value);
        }

        return $returnArray;
    }

}
