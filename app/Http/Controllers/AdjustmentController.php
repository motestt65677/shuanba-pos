<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Adjustment;
use Illuminate\Http\Request;
use App\Models\AdjustmentItem;
use Illuminate\Support\Facades\Log;

class AdjustmentController extends Controller
{
    //
    public function __construct()
	{
        $this->adjustmentService = app()->make('AdjustmentService');
        $this->purchaseService = app()->make('PurchaseService');
    }
    

    public function index(Request $request)
    {
        return view('adjustments.index')->with([
            "yearMonthSelect" => $this->adjustmentService->getYearMonthSelect(),
            "nowMonthYear" => (new DateTime())->format('Y-m'),
        ]);
    }

    public function create(Request $request)
    {
        return view('adjustments.create');
    }

    public function store(Request $request)
    {
        $user = $request->user;
        if(count($request->items) == 0)
            return \Response::json(["status"=> 200, "message"=> "無調增/調減材料"]);
        
        $adjustment = Adjustment::create([
            "prep_by" => $user->id,
            "branch_id" => $user->branch_id,
            "voucher_date" => $request->voucher_date,
            "adjustment_no" => $this->adjustmentService->newAdjustmentNo($user->branch_id),
            "note" => $request->note,
        ]);

        foreach($request->items as $item){
            if($item["amount"] == "" || $item["adjustment_type"] == "" || $item["material_id"] == "")
                continue;
            $averagePurchaseUnitPrice = $this->purchaseService-> getAveragePurchaseUnitPriceOfMaterial($item["material_id"], $request->user->branch_id);
            AdjustmentItem::create([
                "adjustment_id" => $adjustment->id,
                "material_id" => $item["material_id"],
                "amount" => floatval($item["amount"]),
                "unit_price" => $averagePurchaseUnitPrice,
                "total" => $averagePurchaseUnitPrice * floatval($item["amount"]),
                "adjustment_type" => $item["adjustment_type"]
            ]);
        }

        if(count($adjustment->adjustmentItems) == 0){
            $adjustment->delete();
            return \Response::json(["status"=> 200, "message"=> "無調增/調減材料"]);
        }

        return \Response::json(["status"=> 200]);

    }

    public function delete(Request $request){
        $error = [];
        foreach($request->adjustment_ids as $id){
            Adjustment::where("id", $id)->delete();
            AdjustmentItem::where("adjustment_id", $id)->delete();
        }
        return \Response::json(["status"=> 200, "error"=>$error]);
    }

    public function queryData(Request $request){
        $order = isset($request["order"]) ? $request["order"] : [];
        $search = isset($request["search"]) ? $request["search"] : [];
        $search["branch_id"] = $request->user->branch_id;
        $items = $this->adjustmentService->queryData($search, $order);
        return \Response::json(["data"=> $items]);
    }

    public function queryAdjustmentWithItems(Request $request){
        $order = isset($request["order"]) ? $request["order"] : [];
        $items = $this->adjustmentService->queryAdjustmentWithItems($request["search"], $order);
        return \Response::json(["data"=> $items]);
    }


}
