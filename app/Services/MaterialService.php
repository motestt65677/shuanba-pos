<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;



class MaterialService
{
    

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
