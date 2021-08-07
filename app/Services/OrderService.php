<?php

namespace App\Services;

use DateTime;
use DateInterval;
use App\Models\Order;
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

    public function queryData($search = [], $order = []){
        $query = Order::
        select(
            'id',
            'order_no',
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

    public function queryOrderWithItems($search=[], $order=[]){
        $query = Order::
        leftJoin("order_items", 'orders.id', '=', 'order_items.order_id')
        ->select(
            "orders.order_no",
            "orders.voucher_date",
            "orders.total as order_total",
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`order_items`.`material_id` ), '') AS `material_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`order_items`.`material_id` ), '') AS `material_name`"),
            DB::raw("IFNULL((SELECT `unit` FROM `materials` WHERE `id`=`order_items`.`material_id` ), '') AS `material_unit`"),
            "order_items.amount",
            "order_items.unit_price",
            "order_items.total as order_item_total"
        );

        if(isset($search["order_id"]))
            $query->where("orders.id", $search["order_id"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        foreach($items as $item){
            $item->material_name_and_no = $item->material_no . ' - '. $item->material_name;
        }
        return $items;
    }

    public function getYearMonthSelect(){
        $first = Order::orderBy("voucher_date", "ASC")
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
