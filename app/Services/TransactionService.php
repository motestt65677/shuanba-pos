<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class TransactionService
{

    public function queryData($search = [], $order = []){
        
        $purchaseItemsQuery = Purchase::
        select(
            DB::raw("'進貨' as type"),
            "purchases.purchase_no AS no",
            "purchases.voucher_date AS voucher_date",
            "purchase_items.unit_price AS unit_price",
            "purchase_items.amount AS amount",
            "purchase_items.total AS total",
            "materials.material_no AS material_no",
            "materials.name AS material_name",
            "materials.unit AS material_unit"
        )
        ->leftJoin("purchase_items", 'purchases.id', '=', 'purchase_items.purchase_id')
        ->leftJoin("materials", 'purchase_items.material_id', '=', 'materials.id')
        ->whereNotNull('materials.id');
        if(isset($search["branch_id"]))
            $purchaseItemsQuery->where("purchases.branch_id", $search["branch_id"]);

        $purchaseReturnItemsQuery = PurchaseReturn::
        select(
            DB::raw("'退貨' as type"),
            "purchase_returns.purchase_return_no AS no",
            "purchase_returns.voucher_date AS voucher_date",
            "purchase_return_items.unit_price AS unit_price",
            "purchase_return_items.amount AS amount",
            "purchase_return_items.total AS total",
            "materials.material_no AS material_no",
            "materials.name AS material_name",
            "materials.unit AS material_unit"
        )
        ->leftJoin("purchase_return_items", 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
        ->leftJoin("materials", 'purchase_return_items.material_id', '=', 'materials.id')
        ->whereNotNull('materials.id');
        if(isset($search["branch_id"]))
            $purchaseReturnItemsQuery->where("purchase_returns.branch_id", $search["branch_id"]);

        $orderItemsQuery = Order::
        select(
            DB::raw("'銷貨' as type"),
            "orders.order_no AS no",
            "orders.voucher_date AS voucher_date",
            "order_items.unit_price AS unit_price",
            "order_items.amount AS amount",
            "order_items.total AS total",
            "materials.material_no AS material_no",
            "materials.name AS material_name",
            "materials.unit AS material_unit"
        )
        ->leftJoin("order_items", 'orders.id', '=', 'order_items.order_id')
        ->leftJoin("materials", 'order_items.material_id', '=', 'materials.id')
        ->whereNotNull('materials.id');

        if(isset($search["branch_id"]))
            $orderItemsQuery->where("orders.branch_id", $search["branch_id"]);
        
        $query = $purchaseItemsQuery->union($orderItemsQuery)->union($purchaseReturnItemsQuery);
        // if(isset($search["material_id"]))
        //     $query->where("materials.id", $search["material_id"]);
        // if(isset($search["supplier_id"]))
        //     $query->where("materials.supplier_id", $search["supplier_id"]);

        // if(isset($search["count"]))
        //     $query->take($search["count"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        foreach($items as $item){
            // $item->supplier_name_and_no = $item->supplier_name . ' ('. $item->supplier_no . ')';
            $item->material_name_and_no = '('. $item->material_no . ') ' .$item->material_name;
            
        }
        return $items;
    }


}
