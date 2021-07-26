<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class BranchService
{
    public function queryData($search = [], $order = []){
        $query = DB::table('branches')
        ->select(
            "branches.id AS id",
            "branches.name AS name",
        );
        if(isset($search["branch_id"]))
            $query->where("branches.id", $search["branch_id"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        // foreach($items as $item){
        //     $item->supplier_name_and_no = $item->supplier_name . ' ('. $item->supplier_no . ')';
        //     $item->material_name_and_no = $item->material_name . ' ('. $item->material_no . ')';
            
        // }
        return $items;
    }
}
