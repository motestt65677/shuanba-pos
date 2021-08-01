<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class OrderService
{
    public function newOrderNo($branch_id){
        $char = "";
        $year = date("Y");
        $month = date("m");

        $numStr = "1000000";
        $char = "Q";

        $sql = "SELECT RIGHT(order_no,7) AS num FROM `orders`
                WHERE order_no LIKE '{$char}%'
                AND branch_id = '{$branch_id}'
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

}
