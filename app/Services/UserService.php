<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class UserService
{
    public function queryData($search = [], $order = []){
        $query = DB::table('users')
        ->select(
            "users.id AS user_id",
            "users.username AS username",
            "users.name AS name",
        );
        if(isset($search["user_id"]))
            $query->where("users.id", $search["user_id"]);

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
