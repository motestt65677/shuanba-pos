<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;



class PurchaseReturnService
{
    public function newPurchaseReturnNo(){
        $char = "";
        $year = date("Y");
        $month = date("m");

        $numStr = "1000";
        $char = "PR";

        $sql = "SELECT RIGHT(purchase_return_no,4) AS num FROM `purchase_returns`
                WHERE purchase_return_no LIKE '{$char}%'
                ORDER BY purchase_return_no DESC
                LIMIT 1
        ";
        $rt = DB::select($sql);
        if(count($rt) == 1){
            $int = (int)$rt["0"]->num;
            $int += 1;
            $numStr = substr("0000".(string)$int, -4);
        }
        return $char . $year . $month . $numStr;
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
        }
        return $items;
    }


}
