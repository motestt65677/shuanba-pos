<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



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
            "materials.material_no AS material_no",
            "materials.name AS material_name",
            "materials.unit AS material_unit",
            "materials.unit_price AS material_unit_price",
            DB::raw("IFNULL((SELECT `name` FROM `suppliers` WHERE `id`=`materials`.`supplier_id` ), '') AS `supplier_name`"),
            DB::raw("IFNULL((SELECT `supplier_no` FROM `suppliers` WHERE `id`=`materials`.`supplier_id` ), '') AS `supplier_no`"),
            "supplier_id AS supplier_id"
        );
        if(isset($search["material_id"]))
            $query->where("materials.id", $search["material_id"]);
        if(isset($search["supplier_id"]))
            $query->where("materials.supplier_id", $search["supplier_id"]);

        // if(isset($search["count"]))
        //     $query->take($search["count"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        foreach($items as $item){
            $item->supplier_name_and_no = $item->supplier_name . ' ('. $item->supplier_no . ')';
            $item->material_name_and_no = $item->material_name . ' ('. $item->material_no . ')';
            
        }
        return $items;
    }
    public function queryMaterialSet($search = []){
        $query = DB::table('material_sets')
        ->select(
            "material_sets.id AS set_id",
            "material_sets.material_id AS material_id",
            "material_sets.supplier_id AS supplier_id",
            "material_sets.name AS set_name",
            "material_sets.set_unit_price AS set_unit_price",
            "material_sets.material_count AS material_count",
            DB::raw("IFNULL((SELECT `name` FROM `suppliers` WHERE `id`=`material_sets`.`supplier_id` ), '') AS `supplier_name`"),
            DB::raw("IFNULL((SELECT `supplier_no` FROM `suppliers` WHERE `id`=`material_sets`.`supplier_id` ), '') AS `supplier_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`material_sets`.`material_id` ), '') AS `material_name`"),
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`material_sets`.`material_id` ), '') AS `material_no`"),
        );
        if(isset($search["set_id"]))
            $query->where("material_sets.id", $search["set_id"]);
        // if(isset($search["supplier_id"]))
        //     $query->where("materials.supplier_id", $search["supplier_id"]);

        // if(isset($search["count"]))
        //     $query->take($search["count"]);

        // foreach($order as $key=>$value){
        //     $query->orderBy($key, $value);
        // }

        $items = $query->get();
        foreach($items as $item){
            $item->supplier_name_and_no = $item->supplier_name . ' ('. $item->supplier_no . ')';
            $item->material_name_and_no = $item->material_name . ' ('. $item->material_no . ')';
            $item->material_unit_price = round($item->set_unit_price / $item->material_count, 2);
        }
        return $items;
    }

}
