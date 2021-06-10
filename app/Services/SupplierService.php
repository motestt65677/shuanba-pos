<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class SupplierService
{
    
    public function newSupplierNo(){

        $numStr = "0001";
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

    public function queryData($search = [], $order = []){
        $query = DB::table('suppliers')
        ->select(
            "suppliers.id AS supplier_id",
            "suppliers.name AS supplier_name",
            "suppliers.supplier_no AS supplier_no",
            "suppliers.phone AS supplier_phone",
            "suppliers.cellphone AS supplier_cellphone",
            "suppliers.tax_id AS supplier_tax_id",
            "suppliers.address AS supplier_address",
            "suppliers.note1 AS supplier_note1"
        );


        if(isset($search["supplier_id"]))
            $query->where("suppliers.id", $search["supplier_id"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        foreach($items as $item){
            $item->supplier_name_and_no = $item->supplier_name . ' ('. $item->supplier_no . ')';

            // $item->item_amount = round($item->item_amount);
            // $item->item_unit_price = round($item->item_unit_price);
            // $item->item_total = round($item->item_total);
            // $item->created_date = substr($item->created_at, 0, 10);
        }
        return $items;
    }


}
