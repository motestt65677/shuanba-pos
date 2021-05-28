<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;



class SupplierService
{
    

    public function queryData($search = [], $order = []){
        $query = DB::table('suppliers')
        ->select(
            "suppliers.id AS supplier_id",
            "suppliers.name AS supplier_name",
            "suppliers.supplier_no AS supplier_no",
        );

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
