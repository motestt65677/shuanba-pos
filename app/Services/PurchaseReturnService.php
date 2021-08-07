<?php

namespace App\Services;

use DateTime;
use DateInterval;
use App\Models\PurchaseReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class PurchaseReturnService
{
    public function newPurchaseReturnNo($branch_id){
        $char = "";
        $year = date("Y");
        $month = date("m");

        $numStr = "1000";
        $char = "PR";

        $sql = "SELECT RIGHT(purchase_return_no,4) AS num FROM `purchase_returns`
                WHERE purchase_return_no LIKE '{$char}%'
                AND branch_id = '{$branch_id}'
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
        $query = PurchaseReturn::
        select(
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

        if(isset($search["branch_id"])){
            $query->where("branch_id", $search["branch_id"]);
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

    public function queryPurchaseReturnWithItems($search=[], $order=[]){
        $query = PurchaseReturn::
        leftJoin("purchase_return_items", 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
        ->select(
            "purchase_returns.purchase_return_no",
            "purchase_returns.voucher_date",
            "purchase_returns.total as purchase_return_total",
            DB::raw("IFNULL((SELECT `purchase_no` FROM `purchases` WHERE `id`=`purchase_returns`.`purchase_id` ), '') AS `purchase_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `suppliers` WHERE `id`=`purchase_returns`.`supplier_id` ), '') AS `supplier_name`"),
            DB::raw("IFNULL((SELECT `supplier_no` FROM `suppliers` WHERE `id`=`purchase_returns`.`supplier_id` ), '') AS `supplier_no`"),



            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`purchase_return_items`.`material_id` ), '') AS `material_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`purchase_return_items`.`material_id` ), '') AS `material_name`"),
            DB::raw("IFNULL((SELECT `unit` FROM `materials` WHERE `id`=`purchase_return_items`.`material_id` ), '') AS `material_unit`"),
            "purchase_return_items.amount",
            "purchase_return_items.unit_price",
            "purchase_return_items.total as purchase_return_item_total"
        );

        if(isset($search["purchase_return_id"]))
            $query->where("purchase_returns.id", $search["purchase_return_id"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        foreach($items as $item){
            $item->supplier_name_and_no = $item->supplier_no . ' - '. $item->supplier_name;
            $item->material_name_and_no = $item->material_no . ' - '. $item->material_name;
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
