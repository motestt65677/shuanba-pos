<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;



class OrderService
{
    public function newOrderNo(){
        $char = "";
        $year = date("Y");
        $month = date("m");

        $numStr = "1000000";
        $char = "Q";

        $sql = "SELECT RIGHT(order_no,7) AS num FROM `orders`
                WHERE order_no LIKE '{$char}%'
                ORDER BY order_no DESC
                LIMIT 1
        ";
        $rt = DB::select($sql);
        if(count($rt) == 1){
            $int = (int)$rt["0"]->num;
            $int += 1;
            $numStr = substr("0000000".(string)$int, -7);
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
