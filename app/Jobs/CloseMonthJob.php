<?php

namespace App\Jobs;

use App\Models\Closing;
use App\Models\Material;
use App\Models\ClosingItem;
use Illuminate\Bus\Queueable;
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
                    // "purchase_unit_price" => $this->purchaseService-> getAveragePurchaseUnitPriceOfMaterial($material->id), 
                    "starting_count" => 0, 
                    "starting_total" => 0,
                    "closing_count" => 0, 
                    "closing_total" => 0
                ];

                //calculate closing values prior this month to get starting_count and starting_total
                $allPurchaseItems = DB::table('purchases')
                ->leftJoin("purchase_items", 'purchases.id', '=', 'purchase_items.purchase_id')
                ->where("purchases.voucher_date", "<", $this->yearMonth . "-01")
                ->where("purchase_items.material_id", $material->id)
                ->where("purchases.branch_id", $branchId)
                ->get();

                foreach($allPurchaseItems as $item){
                    $closing_item["starting_count"] += floatval($item->amount);
                    $closing_item["starting_total"] += floatval($item->total);
                }

                $allReturnItems = DB::table('purchase_returns')
                ->leftJoin("purchase_return_items", 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                ->where("purchase_returns.voucher_date", "<", $this->yearMonth . "-01")
                ->where("purchase_return_items.material_id", $material->id)
                ->where("purchase_returns.branch_id", $branchId)
                ->get();
                foreach($allReturnItems as $item){
                    $closing_item["starting_count"] -= floatval($item->amount);
                    $closing_item["starting_total"] -= floatval($item->total);
                }

                $allOrderItems = DB::table('orders')
                ->leftJoin("order_items", 'orders.id', '=', 'order_items.order_id')
                ->where("orders.voucher_date", "<", $this->yearMonth . "-01")
                ->where("order_items.material_id", $material->id)
                ->where("orders.branch_id", $branchId)
                ->get();

                foreach($allOrderItems as $item){
                    $closing_item["starting_count"] -= floatval($item->amount);
                    $closing_item["starting_total"] -= floatval($item->total);
                }


                //calculate closing values of this month
                $purchase_items = DB::table('purchases')
                ->leftJoin("purchase_items", 'purchases.id', '=', 'purchase_items.purchase_id')
                ->where("purchases.voucher_date", "like", $this->yearMonth . "%")
                ->where("purchase_items.material_id", $material->id)
                ->where("purchases.branch_id", $branchId)
                ->get();

                foreach($purchase_items as $item){
                    $closing_item["purchase_count"] += floatval($item->amount);
                    $closing_item["purchase_total"] += floatval($item->total);
                }

                $purchase_return_items = DB::table('purchase_returns')
                ->leftJoin("purchase_return_items", 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                ->where("purchase_returns.voucher_date", "like", $this->yearMonth . "%")
                ->where("purchase_return_items.material_id", $material->id)
                ->where("purchase_returns.branch_id", $branchId)
                ->get();

                foreach($purchase_return_items as $item){
                    $closing_item["purchase_return_count"] += floatval($item->amount);
                    $closing_item["purchase_return_total"] += floatval($item->total);
                }

                $order_items = DB::table('orders')
                ->leftJoin("order_items", 'orders.id', '=', 'order_items.order_id')
                ->where("orders.voucher_date", "<", $this->yearMonth . "-01")
                ->where("order_items.material_id", $material->id)
                ->where("orders.branch_id", $branchId)
                ->get();

                foreach($order_items as $item){
                    $closing_item["order_count"] += floatval($item->amount);
                    //temperarily use material_unit_price as unit_cost of material, should maybe use average purchase price of material
                    $closing_item["order_cost"] += floatval($item->total);
                }

                $closing_item["closing_count"] = $closing_item["starting_count"] + $closing_item["purchase_count"] - $closing_item["purchase_return_count"] - $closing_item["order_count"];
                $closing_item["closing_total"] = $closing_item["starting_total"] + $closing_item["purchase_total"] - $closing_item["purchase_return_total"] - $closing_item["order_cost"];

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
