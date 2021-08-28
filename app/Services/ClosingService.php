<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Closing;
use App\Models\Material;
use App\Models\Purchase;
use App\Models\Adjustment;
use App\Jobs\CloseMonthJob;
use App\Models\ClosingItem;
use App\Models\PurchaseReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ClosingService
{

    public function closeMonth($thisYearMonth, $branchId){
        CloseMonthJob::dispatch($thisYearMonth, $branchId);
    }

    public function closableYearMonth($branchId){
        $orderQuery = Order::select('voucher_date')->where("branch_id", $branchId);
        $purchaseReturnQuery = PurchaseReturn::select('voucher_date')->where("branch_id", $branchId);
        $adjustmentQuery = Adjustment::select('voucher_date')->where("branch_id", $branchId);
        $purchaseQuery = Purchase::select('voucher_date')->where("branch_id", $branchId)->union($orderQuery)->union($purchaseReturnQuery)->union($adjustmentQuery);
        $queryData = $purchaseQuery->get();
        $yearMonthArray = [];
        foreach($queryData as $data){
            $thisYearMonth = substr($data->voucher_date, 0, 7);
            if(!in_array($thisYearMonth, $yearMonthArray)){
                array_push($yearMonthArray, $thisYearMonth);
            }
        }
        sort($yearMonthArray);
        return $yearMonthArray;
    }

    public function queryClosings($search = null, $order = []){
        $query = DB::table('closings')
        ->select(
            'id',
            'year_month',
            'created_at'
        );

        if(isset($search["branch_id"]))
            $query->where("closings.branch_id", $search["branch_id"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }
        $items = $query->get();
        // foreach($items as $item){
        //     $item->total = round($item->total);
        //     $item->payment_type_text = $item->payment_type == "monthly" ? "月結" : "現金";
        //     $item->is_paid_text = $item->is_paid == true ? "已付款" : "未付款";

        // }
        return $items;
    }

    public function queryClosingItems($search = [], $order = []){
        $query = DB::table('closings')
        ->leftJoin("closing_items", 'closings.id', '=', 'closing_items.closing_id')
        ->select(
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`closing_items`.`material_id` ), '') AS `material_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`closing_items`.`material_id` ), '') AS `material_name`"),
            'closings.id as closing_id',
            'closing_items.purchase_count as purchase_count',
            'closing_items.purchase_total as purchase_total',
            'closing_items.order_count as order_count',
            'closing_items.order_total as order_total',
            'closing_items.order_cost as order_cost',
            'closing_items.closing_count as closing_count',
            'closing_items.closing_total as closing_total'
        )->whereNotNull("closing_items.id");
        $query->where("closing_id", $search["closing_id"]);

        // if(isset($search["closing_id"])){
        //     $query->where("closing_id", $search["closing_id"]);
        // }
        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        foreach($items as $item){
            $item->material_name_and_no = $item->material_no . ' - '. $item->material_name;
        }
        return $items;
    }

    public function queryClosingWithItems($search = [], $order = []){
        $query = DB::table('closings')
        ->leftJoin("closing_items", 'closings.id', '=', 'closing_items.closing_id')
        ->select(
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`closing_items`.`material_id` ), '') AS `material_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`closing_items`.`material_id` ), '') AS `material_name`"),
            'closings.id as closing_id',
            DB::raw("LEFT(closings.year_month, 7) AS closing_year_month"),
            'closings.created_at as closing_created_at',
            'closing_items.purchase_count as purchase_count',
            'closing_items.purchase_total as purchase_total',
            'closing_items.purchase_return_count as purchase_return_count',
            'closing_items.purchase_return_total as purchase_return_total',
            'closing_items.order_count as order_count',
            'closing_items.order_total as order_total',
            'closing_items.order_cost as order_cost',
            'closing_items.adjustment_total as adjustment_total',
            'closing_items.adjustment_count as adjustment_count',
            'closing_items.closing_count as closing_count',
            'closing_items.closing_total as closing_total',
            'closing_items.starting_count as starting_count',
            'closing_items.starting_total as starting_total'

        )
        ->whereNotNull("closing_items.id")
        ->orderBy("closings.year_month", "DESC")
        ->orderBy("closing_items.material_id", "ASC");

        
        // $query->where("closing_id", $search["closing_id"]);
        if(isset($search["branch_id"])){
            $query->where("closings.branch_id", $search["branch_id"]);
        }


        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        $closings = [];
        foreach($items as $item){
            if(!isset($closings[$item->closing_year_month])){
                $closings[$item->closing_year_month] = [];
            }
            $thisYearMonth = &$closings[$item->closing_year_month];
            if(!isset($thisYearMonth[$item->closing_id])){
                $thisYearMonth[$item->closing_id] = 
                [
                    "closing_year_month" => $item->closing_year_month,
                    "closing_created_at" => $item->closing_created_at,
                    "items" => []
                ];
            }
            $thisClosing = &$closings[$item->closing_year_month][$item->closing_id];
            $thisItem = [
                "material_name_and_no" => $item->material_no . ' - '. $item->material_name,
                "purchase_count" => round($item->purchase_count,2),
                "purchase_total" => round($item->purchase_total,2),
                "purchase_return_count" => round($item->purchase_return_count,2),
                "purchase_return_total" => round($item->purchase_return_total,2),
                "order_count" => round($item->order_count,2),
                "order_total" => round($item->order_total,2),
                "order_cost" => round($item->order_cost,2),
                "adjustment_count" => round($item->adjustment_count,2),
                "adjustment_total" => round($item->adjustment_total,2),
                "closing_count" => round($item->closing_count,2),
                "closing_total" => round($item->closing_total,2),
                "starting_count" => round($item->starting_count,2),
                "starting_total" => round($item->starting_total,2)

                // "purchase_unit_price" => $item->purchase_unit_price,
            ];
            array_push($thisClosing["items"], $thisItem);
        }
        return $closings;
    }

}
