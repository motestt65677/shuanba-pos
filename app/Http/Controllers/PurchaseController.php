<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{

    public function __construct()
	{
        $this->purchaseService = app()->make('PurchaseService');

	}
    //
    public function create(Request $request)
    {
        return view('purchases.create')->with([
            "suppliers" => Supplier::all(),
            // "materials" => Material::all(),

        ]);
    }

    public function store(Request $request){
        $user = $request->user;
        $purchase = Purchase::create([
            "prep_by" => $user->id,
            "branch_id" => $user->branch_id,
            "purchase_no" => $this->purchaseService->newPurchaseNo(),
            "payment_type" => $request->payment_type,
            "note1" => $request->note1,
            "note2" => $request->note2,
        ]);

        $puchase_total = 0;
        foreach($request->items as $item){
            if($item["amount"] == 0 || $item["unit_price"] == 0)
                continue;

            $item_total = $item["amount"] * $item["unit_price"];
            PurchaseItem::create([
                "purchase_id" => $purchase->id,
                "material_id" => $item["item_id"],
                "amount" => $item["amount"],
                "unit_price" => $item["unit_price"],
                "total" => $item_total
            ]);
            $puchase_total += $item_total;
        }

        $purchase->total = $puchase_total;
        $purchase->save();

        return \Response::json(["status"=> 200]);
    }

    public function queryPurchaseItems(Request $request){
        $items = $this->purchaseService->queryPurchaseItems($request["search"], $request["order"]);
        return \Response::json(["data"=> $items]);
    }
}
