<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductMaterialService
{
    public function queryData($search = null, $order = []){
        $query = DB::table('product_materials')
        ->select(
            'id',
            'product_id',
            'material_id',
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`product_materials`.`material_id` ), '') AS `material_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`product_materials`.`material_id` ), '') AS `material_name`"),
            'material_count'
        );

        if(!isset($search["product_id"])){
            return [];
        }
        $query->where("product_id", $search["product_id"]);
        $items = $query->get();

        foreach($items as $item){
            $item->material_name_and_no = $item->material_name . ' ('. $item->material_no . ')';
        }

        return $items;
    }
}
