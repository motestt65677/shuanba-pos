<?php

namespace App\Services;

use DateTime;
use DateInterval;
use Illuminate\Support\Facades\DB;
use App\Models\Adjustment;
use Illuminate\Support\Facades\Log;


class AdjustmentService
{
    public function newAdjustmentNo($branch_id){
        $char = "";
        $year = date("Y");
        $month = date("m");

        $numStr = "1000000";
        $char = "A";

        $sql = "SELECT RIGHT(adjustment_no,7) AS num FROM `adjustments`
                WHERE adjustment_no LIKE '{$char}%'
                AND branch_id = '{$branch_id}'
                ORDER BY adjustment_no DESC
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
        $query = Adjustment::
        select(
            'id',
            'adjustment_no',
            'voucher_date',
            'note'
        );
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
    public function queryAdjustmentWithItems($search=[], $order=[]){
        $query = Adjustment::
        leftJoin("adjustment_items", 'adjustments.id', '=', 'adjustment_items.adjustment_id')
        ->select(
            "adjustments.adjustment_no",
            "adjustments.voucher_date",
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`adjustment_items`.`material_id` ), '') AS `material_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`adjustment_items`.`material_id` ), '') AS `material_name`"),
            DB::raw("IFNULL((SELECT `unit` FROM `materials` WHERE `id`=`adjustment_items`.`material_id` ), '') AS `material_unit`"),
            "adjustment_items.amount",
            "adjustment_items.unit_price",
            "adjustment_items.total as adjustment_item_total"
        );

        if(isset($search["adjustment_id"]))
            $query->where("adjustments.id", $search["adjustment_id"]);

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
        $first = Adjustment::orderBy("voucher_date", "ASC")
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
