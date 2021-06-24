<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    //
    public function __construct()
	{
        $this->orderService = app()->make('OrderService');
        $this->productService = app()->make('ProductService');
	}

    public function bulkImportQlieer(Request $request){

        // array (
        //     'product_name' => '烏龍麵',
        //     'product_count' => '39',
        //     'set_name' => '活力梅花豬肉鍋',
        //   ), DATEEEEEE
            //DATEEEEEEDATEEEEEEDATEEEEEE
        $purchaseDict = [];
        $order = Order::create([
            "prep_by" => $request->user->id,
            "branch_id" => $request->user->branch_id,
            "voucher_date" => $request->voucher_date,
            "order_no" => $this->orderService->newOrderNo(),
            "payment_type" => "credit",
            "total" => $request->qlieer_order_total,
        ]);
        $returnItems = $request->qlieer_order_items;
        foreach($returnItems as $key=>$item){
            $productName = $item["product_name"];
            if (str_contains($item["product_name"], 'oz') || str_contains($item["product_name"], 'g')) { 
                $productName = $item["set_name"]. "-" .$item["product_name"];
                $returnItems[$key]["product_name"] = $productName;
            }

            $product = Product::where("name", $item["product_name"])->first();
            if(!$product){
                $product = Product::create([
                    "product_no" => $this->productService -> newProductNo(),
                    "name" => $productName,
                    "price" => 0
                ]);
            }

            OrderItem::create([
                "product_id" => $product->id,
                "order_id" => $order->id,
                "amount" => $item["product_count"],
                "unit_price" => 0,
                "total" => 0
            ]);
            $returnItems[$key]["message"] = "Success";
        }

        return \Response::json(["status"=> "200", "order_items"=>$returnItems]);
    }
}
