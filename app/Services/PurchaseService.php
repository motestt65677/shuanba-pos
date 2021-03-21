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
    

}
