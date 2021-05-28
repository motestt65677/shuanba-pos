<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;



class MaterialService
{
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

    public function queryData($search = [], $order = []){
        $query = DB::table('materials')
        ->select(
            "materials.id AS material_id",
            "materials.name AS material_name",
            "materials.unit_price AS material_unit_price"

        );
        if(isset($search["supplier_id"]))
            $query->where("materials.supplier_id", $search["supplier_id"]);
        // if(isset($search["count"]))
        //     $query->take($search["count"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        // foreach($items as $item){
        //     $item->item_amount = round($item->item_amount);
        //     $item->item_unit_price = round($item->item_unit_price);
        //     $item->item_total = round($item->item_total);
        //     // $item->created_date = substr($item->created_at, 0, 10);
        // }
        return $items;
    }


}
