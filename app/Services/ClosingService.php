<?php

namespace App\Services;

use App\Models\Closing;
use App\Models\Material;
use App\Models\ClosingItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ClosingService
{
    public function closeMonth(){
        $thisYearMonth = "2021-06";

        $materials = Material::all();
        $closing_item_dict = []; //material_id => [purchase_count: 0, purchase_total: 0, order_count: 0, order_total: 0, order_cost: 0, closing_count: 0, closing_total: 0]
        foreach($materials as $material){
            $purchase_items = DB::table('purchases')
            ->leftJoin("purchase_items", 'purchases.id', '=', 'purchase_items.purchase_id')
            ->where("purchases.voucher_date", "like", $thisYearMonth . "%")
            ->where("purchase_items.material_id", $material->id)
            ->get();
            $purchase_count = 0;
            $purchase_total = 0;
            $closing_item = [
                "purchase_count" => 0, 
                "purchase_total"=> 0, 
                "order_count" => 0, 
                "order_total" => 0, 
                "order_cost" => 0, 
                "closing_count" => 0, 
                "closing_total" => 0
            ];
            foreach($purchase_items as $item){
                $closing_item["purchase_count"] += floatval($item->amount);
                $closing_item["purchase_total"] += floatval($item->total);
            }
            
            $order_items = DB::table('orders')
            ->select(
                "order_items.amount as order_item_amount", 
                "product_materials.material_count as material_count",
                "orders.total as order_total",
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
                $closing_item["order_total"] += floatval($item->order_total);
                $closing_item["order_cost"] += floatval($item->order_item_amount) * floatval($item->material_count) * $item->material_unit_price;
            }

            $closing_item["closing_count"] = $closing_item["purchase_count"] - $closing_item["order_count"];
            $closing_item["closing_total"] = $closing_item["purchase_total"] - $closing_item["order_cost"];

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

}
