<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Closing;
use App\Models\Material;
use App\Models\Purchase;
use App\Models\Adjustment;
use App\Models\ClosingItem;
use Illuminate\Bus\Queueable;
use App\Models\PurchaseReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CloseMonthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $purchaseService;
    private $yearMonth;
    private $branchIds;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($yearMonth, $branchIds = [])
	{
        $this->purchaseService = app()->make('PurchaseService');
        $this->yearMonth = $yearMonth;
        $this->branchIds = $branchIds;

	}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->branchIds as $branchId){

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
                    "adjustment_count" => 0, 
                    "adjustment_total" => 0, 
                    // "purchase_unit_price" => $this->purchaseService-> getAveragePurchaseUnitPriceOfMaterial($material->id), 
                    "starting_count" => 0, 
                    "starting_total" => 0,
                    "closing_count" => 0, 
                    "closing_total" => 0
                ];

                //calculate closing values prior this month to get starting_count and starting_total
                $allPurchaseItems = Purchase::
                leftJoin("purchase_items", 'purchases.id', '=', 'purchase_items.purchase_id')
                ->where("purchases.voucher_date", "<", $this->yearMonth . "-01")
                ->where("purchase_items.material_id", $material->id)
                ->where("purchases.branch_id", $branchId)
                ->get();
                
                foreach($allPurchaseItems as $item){
                    $closing_item["starting_count"] += floatval($item->amount);
                    $closing_item["starting_total"] += floatval($item->total);
                }

                $allReturnItems = PurchaseReturn::
                leftJoin("purchase_return_items", 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                ->where("purchase_returns.voucher_date", "<", $this->yearMonth . "-01")
                ->where("purchase_return_items.material_id", $material->id)
                ->where("purchase_returns.branch_id", $branchId)
                ->get();
                foreach($allReturnItems as $item){
                    $closing_item["starting_count"] -= floatval($item->amount);
                    $closing_item["starting_total"] -= floatval($item->total);
                }

                $allOrderItems = Order::
                leftJoin("order_items", 'orders.id', '=', 'order_items.order_id')
                ->where("orders.voucher_date", "<", $this->yearMonth . "-01")
                ->where("order_items.material_id", $material->id)
                ->where("orders.branch_id", $branchId)
                ->get();

                foreach($allOrderItems as $item){
                    $closing_item["starting_count"] -= floatval($item->amount);
                    $closing_item["starting_total"] -= floatval($item->total);
                }

                $allAdjustmentItems = adjustment::
                select("adjustment_items.amount as amount", "adjustment_items.total as total", "adjustment_items.adjustment_type as adjustment_type")
                ->leftJoin("adjustment_items", 'adjustments.id', '=', 'adjustment_items.adjustment_id')
                ->where("adjustments.voucher_date", "<", $this->yearMonth . "-01")
                ->where("adjustment_items.material_id", $material->id)
                ->where("adjustments.branch_id", $branchId)
                ->get();

                foreach($allAdjustmentItems as $item){
                    if($item->adjustment_type == "increase"){
                        $closing_item["starting_count"] += floatval($item->amount);
                        $closing_item["starting_total"] += floatval($item->total);
                    } else if($item->adjustment_type == "decrease"){
                        $closing_item["starting_count"] -= floatval($item->amount);
                        $closing_item["starting_total"] -= floatval($item->total);
                    }
                }


                //calculate closing values of this month
                $purchase_items = Purchase::
                leftJoin("purchase_items", 'purchases.id', '=', 'purchase_items.purchase_id')
                ->where("purchases.voucher_date", "like", $this->yearMonth . "%")
                ->where("purchase_items.material_id", $material->id)
                ->where("purchases.branch_id", $branchId)
                ->get();

                foreach($purchase_items as $item){
                    $closing_item["purchase_count"] += floatval($item->amount);
                    $closing_item["purchase_total"] += floatval($item->total);
                }

                $purchase_return_items = PurchaseReturn::
                leftJoin("purchase_return_items", 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                ->where("purchase_returns.voucher_date", "like", $this->yearMonth . "%")
                ->where("purchase_return_items.material_id", $material->id)
                ->where("purchase_returns.branch_id", $branchId)
                ->get();

                foreach($purchase_return_items as $item){
                    $closing_item["purchase_return_count"] += floatval($item->amount);
                    $closing_item["purchase_return_total"] += floatval($item->total);
                }

                $order_items = Order::
                leftJoin("order_items", 'orders.id', '=', 'order_items.order_id')
                ->where("orders.voucher_date", "like", $this->yearMonth . "%")
                ->where("order_items.material_id", $material->id)
                ->where("orders.branch_id", $branchId)
                ->get();

                foreach($order_items as $item){
                    $closing_item["order_count"] += floatval($item->amount);
                    //temperarily use material_unit_price as unit_cost of material, should maybe use average purchase price of material
                    $closing_item["order_cost"] += floatval($item->total);
                }

                $adjustment_items = Adjustment::
                select(
                    "adjustment_items.amount as amount", 
                    "adjustment_items.total as total", 
                    "adjustment_items.adjustment_type as adjustment_type",
                    "adjustment_items.material_id"
                    )
                ->leftJoin("adjustment_items", 'adjustments.id', '=', 'adjustment_items.adjustment_id')
                ->where("adjustments.voucher_date", "like", $this->yearMonth . "%")
                ->where("adjustment_items.material_id", $material->id)
                ->where("adjustments.branch_id", $branchId)
                ->get();

                foreach($adjustment_items as $item){
                    if($item->adjustment_type == "increase"){
                        $closing_item["adjustment_count"] += floatval($item->amount);
                        $closing_item["adjustment_total"] += floatval($item->total);
                    } else if($item->adjustment_type == "decrease"){
                        $closing_item["adjustment_count"] -= floatval($item->amount);
                        $closing_item["adjustment_total"] -= floatval($item->total);
                    }
                }

                $closing_item["closing_count"] = $closing_item["starting_count"] + $closing_item["purchase_count"] - $closing_item["purchase_return_count"] - $closing_item["order_count"] + $closing_item["adjustment_count"];
                $closing_item["closing_total"] = $closing_item["starting_total"] + $closing_item["purchase_total"] - $closing_item["purchase_return_total"] - $closing_item["order_cost"] + $closing_item["adjustment_total"];

                $closing_item_dict[$material->id] = $closing_item;
            }

            $closing = Closing::create([
                "year_month" => $this->yearMonth . "-01",
                "branch_id" => $branchId
            ]);

            foreach($closing_item_dict as $key => $value){
                $value["closing_id"] = $closing->id;
                $value["material_id"] = $key;
                ClosingItem::create($value);
            }
        }


        
    }
}
