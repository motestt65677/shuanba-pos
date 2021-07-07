<?php

namespace App\Services;

use App\Models\Closing;
use App\Models\Material;
use App\Models\ClosingItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ClosingService
{
    public function __construct()
	{
        $this->purchaseService = app()->make('PurchaseService');
	}
    public function closeMonth($thisYearMonth){

        $materials = Material::all();
        $closing_item_dict = []; //material_id => [purchase_count: 0, purchase_total: 0, order_count: 0, order_total: 0, order_cost: 0, closing_count: 0, closing_total: 0]
        foreach($materials as $material){
            $closing_item = [
                "purchase_count" => 0, 
                "purchase_total"=> 0, 
                "purchase_return_count"=> 0,
                "purchase_return_total"=> 0,
                "order_count" => 0, 
                "order_total" => 0, //cannot be calculated since order_items total cannot be caluclated
                "order_cost" => 0, 
                "purchase_unit_price" => $this->purchaseService-> getAveragePurchaseUnitPriceOfMaterial($material->id), 
                "starting_count" => 0, 
                "starting_total" => 0,
                "closing_count" => 0, 
                "closing_total" => 0
            ];

            //calculate closing values prior this month to get starting_count and starting_total
            $allPurchaseItems = DB::table('purchases')
            ->leftJoin("purchase_items", 'purchases.id', '=', 'purchase_items.purchase_id')
            ->where("purchases.voucher_date", "<", $thisYearMonth . "-01")
            ->where("purchase_items.material_id", $material->id)
            ->get();

            foreach($allPurchaseItems as $item){
                $closing_item["starting_count"] += floatval($item->amount);
                $closing_item["starting_total"] += floatval($item->total);
            }

            $allReturnItems = DB::table('purchase_returns')
            ->leftJoin("purchase_return_items", 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
            ->where("purchase_returns.voucher_date", "<", $thisYearMonth . "-01")
            ->where("purchase_return_items.material_id", $material->id)
            ->get();
            foreach($allReturnItems as $item){
                $closing_item["starting_count"] -= floatval($item->amount);
                $closing_item["starting_total"] -= floatval($item->total);
            }

            $allOrderItems = DB::table('orders')
            ->select(
                "order_items.amount as order_item_amount", 
                "product_materials.material_count as material_count",
                "materials.unit_price as material_unit_price"
            )
            ->leftJoin("order_items", 'orders.id', '=', 'order_items.order_id')
            ->leftJoin("products", 'products.id', '=', 'order_items.product_id')
            ->leftJoin("product_materials", 'product_materials.product_id', '=', 'products.id')
            ->leftJoin("materials", 'materials.id', '=', 'product_materials.material_id')
            ->where("orders.voucher_date", "<", $thisYearMonth . "-01")
            ->where("materials.id", $material->id)
            ->get();

            foreach($allOrderItems as $item){
                $closing_item["starting_count"] -= floatval($item->order_item_amount) * floatval($item->material_count);
                $closing_item["starting_total"] -= floatval($item->order_item_amount) * floatval($item->material_count) * $closing_item["purchase_unit_price"];
            }


            //calculate closing values of this month
            $purchase_items = DB::table('purchases')
            ->leftJoin("purchase_items", 'purchases.id', '=', 'purchase_items.purchase_id')
            ->where("purchases.voucher_date", "like", $thisYearMonth . "%")
            ->where("purchase_items.material_id", $material->id)
            ->get();

            foreach($purchase_items as $item){
                $closing_item["purchase_count"] += floatval($item->amount);
                $closing_item["purchase_total"] += floatval($item->total);
            }

            $purchase_return_items = DB::table('purchase_returns')
            ->leftJoin("purchase_return_items", 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
            ->where("purchase_returns.voucher_date", "like", $thisYearMonth . "%")
            ->where("purchase_return_items.material_id", $material->id)
            ->get();

            foreach($purchase_return_items as $item){
                $closing_item["purchase_return_count"] += floatval($item->amount);
                $closing_item["purchase_return_total"] += floatval($item->total);
            }

            $order_items = DB::table('orders')
            ->select(
                "order_items.amount as order_item_amount", 
                "product_materials.material_count as material_count",
                "materials.unit_price as material_unit_price"
            )
            ->leftJoin("order_items", 'orders.id', '=', 'order_items.order_id')
            ->leftJoin("products", 'products.id', '=', 'order_items.product_id')
            ->leftJoin("product_materials", 'product_materials.product_id', '=', 'products.id')
            ->leftJoin("materials", 'materials.id', '=', 'product_materials.material_id')
            ->where("orders.voucher_date", "like", $thisYearMonth . "%")
            ->where("materials.id", $material->id)
            ->get();

            foreach($order_items as $item){
                $closing_item["order_count"] += floatval($item->order_item_amount) * floatval($item->material_count);
                //temperarily use material_unit_price as unit_cost of material, should maybe use average purchase price of material
                $closing_item["order_cost"] += floatval($item->order_item_amount) * floatval($item->material_count) * $closing_item["purchase_unit_price"];
                // Log::info([floatval($item->order_item_amount), floatval($item->material_count), $materialAverageUnitPrice]);
            }

            $closing_item["closing_count"] = $closing_item["starting_count"] + $closing_item["purchase_count"] - $closing_item["purchase_return_count"] - $closing_item["order_count"];
            $closing_item["closing_total"] = $closing_item["starting_total"] + $closing_item["purchase_total"] - $closing_item["purchase_return_total"] - $closing_item["order_cost"];

            $closing_item_dict[$material->id] = $closing_item;
        }

        $closing = Closing::create([
            "year_month" => $thisYearMonth . "-01"
        ]);

        foreach($closing_item_dict as $key => $value){
            $value["closing_id"] = $closing->id;
            $value["material_id"] = $key;
            ClosingItem::create($value);
        }

    }

    public function closableYearMonth(){
        $orderQuery = DB::table('orders')->select('voucher_date');
        $purchaseQuery = DB::table('purchases')->select('voucher_date')->union($orderQuery);
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
            $item->material_name_and_no = $item->material_name . ' ('. $item->material_no . ')';
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
            'closings.year_month as closing_year_month',
            'closings.created_at as closing_created_at',
            'closing_items.purchase_count as purchase_count',
            'closing_items.purchase_total as purchase_total',
            'closing_items.purchase_return_count as purchase_return_count',
            'closing_items.purchase_return_total as purchase_return_total',
            'closing_items.order_count as order_count',
            'closing_items.order_total as order_total',
            'closing_items.order_cost as order_cost',
            'closing_items.closing_count as closing_count',
            'closing_items.closing_total as closing_total',
            'closing_items.starting_count as starting_count',
            'closing_items.starting_total as starting_total'

        )
        ->whereNotNull("closing_items.id")
        ->orderBy("closings.year_month", "DESC")
        ->orderBy("closing_items.material_id", "ASC");

        // $query->where("closing_id", $search["closing_id"]);
        // if(isset($search["closing_id"])){
        //     $query->where("closing_id", $search["closing_id"]);
        // }
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
                $thisYearMonth[$item->closing_id] = [
                                        "closing_year_month" => $item->closing_year_month,
                                        "closing_created_at" => $item->closing_created_at,
                                        "items" => []
                                    ];
            }
            $thisClosing = &$closings[$item->closing_year_month][$item->closing_id];
            $thisItem = [
                "material_name_and_no" => $item->material_no . ' ('. $item->material_name . ')',
                "purchase_count" => $item->purchase_count,
                "purchase_total" => $item->purchase_total,
                "purchase_return_count" => $item->purchase_return_count,
                "purchase_return_total" => $item->purchase_return_total,
                "order_count" => $item->order_count,
                "order_total" => $item->order_total,
                "order_cost" => $item->order_cost,
                "closing_count" => $item->closing_count,
                "closing_total" => $item->closing_total,
                "starting_count" => $item->starting_count,
                "starting_total" => $item->starting_total

                // "purchase_unit_price" => $item->purchase_unit_price,
            ];
            array_push($thisClosing["items"], $thisItem);
        }
        return $closings;
    }

}
