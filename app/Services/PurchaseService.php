<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseService
{
    public function newPurchaseNo(){
        $char = "";
        $year = date("Y");
        $month = date("m");

        $numStr = "10000";
        $char = "P";

        $sql = "SELECT RIGHT(purchase_no,5) AS num FROM `purchases`
                WHERE purchase_no LIKE '{$char}%'
                ORDER BY purchase_no DESC
                LIMIT 1
        ";
        $rt = DB::select($sql);
        if(count($rt) == 1){
            $int = (int)$rt["0"]->num;
            $int += 1;
            $numStr = substr("00000".(string)$int, -5);
        }
        return $char . $year . $month . $numStr;
    }

    public function queryPurchaseItems($search = null, $order = null){
        $query = DB::table('purchase_items')
        ->select(
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`purchase_items`.`material_id` ), '') AS `material_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`purchase_items`.`material_id` ), '') AS `material_name`"),
            DB::raw("IFNULL((SELECT `unit` FROM `materials` WHERE `id`=`purchase_items`.`material_id` ), '') AS `material_unit`"),
            'amount',
            'unit_price',
            'total',
            'created_at'
        );
        if(isset($search["count"]))
            $query->take($search["count"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        foreach($items as $item){
            $item->amount = round($item->amount);
            $item->unit_price = round($item->unit_price);
            $item->total = round($item->total);
            $item->created_date = substr($item->created_at, 0, 10);

        }
        return $items;
    }
    

}
