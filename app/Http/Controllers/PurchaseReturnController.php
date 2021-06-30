<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use Illuminate\Support\Facades\Log;

class PurchaseReturnController extends Controller
{
    //

    public function __construct()
	{
        $this->purchaseService = app()->make('PurchaseService');
        $this->purchaseReturnService = app()->make('PurchaseReturnService');
	}

    public function index(Request $request)
    {
        return view('purchase_returns.index')->with([
            "yearMonthSelect" => $this->purchaseReturnService->getYearMonthSelect(),
            "nowMonthYear" => (new DateTime())->format('Y-m'),
        ]);
    }

    public function create(Request $request)
    {
        return view('purchase_returns.create');
    }

    public function store(Request $request){
        $purchase = Purchase::find($request->purchase_id);
        if($purchase){

            $purchaseReturn = PurchaseReturn::create([
                "prep_by" => $request->user->id,
                "branch_id" => $request->user->branch_id,
                "purchase_id" => $purchase->id,
                "supplier_id" => $purchase->supplier_id,
                "voucher_date" => $request->voucher_date,
                "purchase_return_no" => $this->purchaseReturnService->newPurchaseReturnNo()
            ]);

            $total = 0;
            foreach($request->items as $item){
                if($item["data_amount"] == 0)
                    continue;
                if($item["data_unit_price"] == 0)
                    continue;
                $itemTotal = floatval($item["data_amount"]) * floatval($item["data_unit_price"]);
                $total += $itemTotal;
                PurchaseReturnItem::create([
                    "purchase_return_id" => $purchaseReturn->id,
                    "purchase_item_id" => $item["data_purchase_item_id"],
                    "amount" => $item["data_amount"],
                    "unit_price" => $item["data_unit_price"],
                    "total" => $itemTotal
                ]);
            }

            $purchaseReturn->total = $total;
            $purchaseReturn->save();
        }


        return \Response::json(["status"=> 200]);
    }

    public function delete(Request $request){
        $error = [];
        foreach($request->purchase_return_ids as $id){
            PurchaseReturn::find($id)->delete();
            PurchaseReturnItem::where("purchase_return_id", $id)->delete();
        }
        return \Response::json(["status"=> 200, "error"=>$error]);
    }

    public function queryData(Request $request){
        $order = isset($request["order"]) ? $request["order"] : [];
        $items = $this->purchaseReturnService->queryData($request["search"], $order);
        return \Response::json(["data"=> $items]);
    }
}
