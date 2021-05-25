<?php

namespace App\Services;

use DateTime;
use DateInterval;
use App\Models\Purchase;
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

    public function queryPurchases($search = null, $order = []){
        $query = DB::table('purchases')
        ->select(
            'id',
            'purchase_no',
            DB::raw("IFNULL((SELECT `supplier_no` FROM `suppliers` WHERE `id`=`purchases`.`supplier_id` ), '') AS `supplier_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `suppliers` WHERE `id`=`purchases`.`supplier_id` ), '') AS `supplier_name`"),
            'payment_type',
            'voucher_date',
            'total',
            'is_paid'
        );

        if(isset($search["payment_type"])){
            $query->where("payment_type", $search["payment_type"]);
        }

        if(isset($search["voucher_year_month"])){
            $query->whereRaw("LEFT(`voucher_date`, 7) = '" . $search["voucher_year_month"] . "'");
        }

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();

        foreach($items as $item){
            $item->total = round($item->total);
            $item->payment_type_text = $item->payment_type == "monthly" ? "月結" : "現金";
            $item->is_paid_text = $item->is_paid == true ? "已付款" : "未付款";

        }
        return $items;
    }

    public function queryPurchaseItems($search = null, $order = []){
        $query = DB::table('purchase_items')
        ->select(
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`purchase_items`.`material_id` ), '') AS `material_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`purchase_items`.`material_id` ), '') AS `material_name`"),
            DB::raw("IFNULL((SELECT `unit` FROM `materials` WHERE `id`=`purchase_items`.`material_id` ), '') AS `material_unit`"),
            'amount',
            'unit_price',
            'total',
            'created_at'
        );
        if(isset($search["count"]))
            $query->take($search["count"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        foreach($items as $item){
            $item->amount = round($item->amount);
            $item->unit_price = round($item->unit_price);
            $item->total = round($item->total);
            $item->created_date = substr($item->created_at, 0, 10);
        }
        return $items;
    }
    public static function getYearMonthSelect(){
        $first = Purchase::orderBy("voucher_date", "ASC")
        -> first();
        Log::info($first);
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
        Log::info($retAry);
        return $retAry;
        
    }

}
