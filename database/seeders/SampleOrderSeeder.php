<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class SampleOrderSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->orderService = app()->make('OrderService');

        //$this->orderService->newOrderNo();
        $productIds = [2, 7, 8, 10];
        for($i = 0 ; $i < 5; $i++){
            
            $order = Order::create([
                "prep_by" => "1",
                "branch_id" => "1",
                "voucher_date" => date("Y-m-d"),
                "order_no" => $this->orderService->newOrderNo(),
                "payment_type" => "credit",
                "total" => "0",
            ]);
            $orderTotal = 0;
            
            for($j = 0; $j < rand(1, 5); $j++){
                $amount = rand(1,3);

                $productId = $productIds[rand(0, count($productIds) -1)];
                $product = Product::find($productId);
                OrderItem::create([
                    "product_id" => $productIds[rand(0, count($productIds) -1)],
                    "order_id" => $order->id,
                    "amount" => $amount,
                    "unit_price" => $product->price,
                    "total" => $amount * $product->price
                ]);

                $orderTotal += $amount * $product->price;
            }
            $order -> total = $orderTotal;
            $order -> save();

        }

    }
}
