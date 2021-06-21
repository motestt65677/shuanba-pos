<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportConversionService
{
    public function queryData($search = null, $order = []){
        $query = DB::table('import_conversions')
        ->select(
            'id',
            'import_price',
            'import_unit',
            'import_count',
            'material_count'
        );

        if(isset($search["material_id"])){
            $query->where("material_id", $search["material_id"]);
        }


        // foreach($order as $key=>$value){
        //     $query->orderBy($key, $value);
        // }

        $items = $query->get();

        // foreach($items as $item){
        //     $item->total = round($item->total);
        //     $item->payment_type_text = $item->payment_type == "monthly" ? "月結" : "現金";
        //     $item->is_paid_text = $item->is_paid == true ? "已付款" : "未付款";
        // }

        return $items;
    }


}
