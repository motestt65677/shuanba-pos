<?php

namespace App\Services;

use DateTime;
use DateInterval;
use App\Models\PurchaseReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class PurchaseReturnService
{
    public function newPurchaseReturnNo(){
        $char = "";
        $year = date("Y");
        $month = date("m");

        $numStr = "1000";
        $char = "PR";

        $sql = "SELECT RIGHT(purchase_return_no,4) AS num FROM `purchase_returns`
                WHERE purchase_return_no LIKE '{$char}%'
                ORDER BY purchase_return_no DESC
                LIMIT 1
        ";
        $rt = DB::select($sql);
        if(count($rt) == 1){
            $int = (int)$rt["0"]->num;
            $int += 1;
            $numStr = substr("0000".(string)$int, -4);
        }
        return $char . $year . $month . $numStr;
    }

    public function queryData($search = [], $order = []){
        $query = DB::table('purchase_returns')
        ->select(
            'id',
            'purchase_return_no',
            DB::raw("IFNULL((SELECT `supplier_no` FROM `suppliers` WHERE `id`=`purchase_returns`.`supplier_id` ), '') AS `supplier_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `suppliers` WHERE `id`=`purchase_returns`.`supplier_id` ), '') AS `supplier_name`"),
            'voucher_date',
            'total'
        )->where("total", ">", 0);

        if(isset($search["voucher_year_month"])){
            $query->whereRaw("LEFT(`voucher_date`, 7) = '" . $search["voucher_year_month"] . "'");
        }
        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();

        foreach($items as $item){
            $item->total = round($item->total,2);
        }
        return $items;
    }

    public function getYearMonthSelect(){
        $first = PurchaseReturn::orderBy("voucher_date", "ASC")
        -> first();
        $retAry = [];
        if($first){
            $firstDateTime = new DateTime($first->voucher_date);
            $now = new DateTime();
            $diff = $now->diff($firstDateTime);
            $interval1Y = new DateInterval('P1Y');
            $interval1M = new DateInterval('P1M');

            if($diff->days > 365){
                $oneYearAgo = $now->sub($interval1Y);
                $firstDay = new DateTime($oneYearAgo->format('Y-m-01'));
                for($i = 0; $i<13; $i++){
                    array_push($retAry, $firstDay->format('Y-m'));
                    $firstDay->add($interval1M);
                }
            } else {
                $firstDay = new DateTime($firstDateTime->format('Y-m-01'));
                $i=0;
                while($firstDay < $now){
                    if($i> 12){
                        break;
                    }
                    array_push($retAry, $firstDay->format('Y-m'));
                    $firstDay->add($interval1M);
                    $i++;
                }
            }
        } else {
            array_push($retAry, (new DateTime()) -> format('Y-m'));
        }
        return $retAry;
        
    }

}
