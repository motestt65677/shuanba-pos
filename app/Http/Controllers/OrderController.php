<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\ProductMaterial;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    //
    public function __construct()
	{
        $this->orderService = app()->make('OrderService');
        $this->productService = app()->make('ProductService');
        $this->purchaseService = app()->make('PurchaseService');
	}
    public function qlieerImport(Request $request)
    {
        return view('orders.qlieerImport');
    }
    public function bulkImportQlieer(Request $request){

        $purchaseDict = [];
        $order = Order::create([
            "prep_by" => $request->user->id,
            "branch_id" => $request->user->branch_id,
            "voucher_date" => $request->voucher_date,
            "order_no" => $this->orderService->newOrderNo($request->user->branch_id),
            "payment_type" => "credit",
            "total" => $request->qlieer_order_total,
        ]);
        $orderItemCount = 0;
        $returnItems = $request->qlieer_order_items;

        foreach($returnItems as $key=>$item){
            $productName = $item["product_name"];
            if (str_contains($item["product_name"], 'oz') || str_contains($item["product_name"], 'g')) { 
                $productName = $item["set_name"]. "-" .$item["product_name"];
                $returnItems[$key]["product_name"] = $productName;
            }

            $product = Product::where("name", $item["product_name"])->first();

            if(!$product)
                continue;

            $productMaterials = ProductMaterial::where("product_id", $product->id)->get();

            if(count($productMaterials) == 0){
                $returnItems[$key]["message"] = "產品無配對庫存材料";
                continue;
            } 

            foreach($productMaterials as $productMaterial){
                $purchaseUnitPrice = $this->purchaseService-> getAveragePurchaseUnitPriceOfMaterial($productMaterial -> material_id, $request->user->branch_id);
                OrderItem::create([
                    "material_id" => $productMaterial -> material_id,
                    "product_id" => $product->id,
                    "order_id" => $order->id,
                    "amount" => floatval($item["product_count"]) * floatval($productMaterial -> material_count),
                    "unit_price" => floatval($purchaseUnitPrice),
                    "total" => floatval($purchaseUnitPrice) * floatval($item["product_count"]) * floatval($productMaterial -> material_count)
                ]);
                $returnItems[$key]["message"] = "Success";
                $orderItemCount++;
            }
        }
        if($orderItemCount == 0)
            $order->delete();
        return \Response::json(["status"=> "200", "order_items"=>$returnItems]);
    }

    public function bulkImportProductCheck(Request $request){
        $returnItems = [
            "product_material_exists" => [],
            "product_material_not_exists" => []
        ];
        foreach($request->qlieer_order_items as $item){
            $product = Product::where("name", $item["product_name"])->first();
            if(!$product){
                $product = Product::create([
                    "product_no" => $this->productService -> newProductNo(),
                    "name" => $item["product_name"],
                    "price" => 0
                ]);
            }
            //include product_id for update product with product_id in later ajax call
            $item["product_id"] = $product->id;
            $productMaterial = ProductMaterial::where("product_id", $product->id)->first();
            if($productMaterial){
                array_push($returnItems["product_material_exists"], $item);
            } else {
                array_push($returnItems["product_material_not_exists"], $item);
            }
        }

        return \Response::json(["status"=> "200", "qlieer_order_items"=>$returnItems]);

    }
}
