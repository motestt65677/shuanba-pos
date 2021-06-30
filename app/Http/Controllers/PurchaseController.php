<?php

namespace App\Http\Controllers;

use DateTime;
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
        $this->materialService = app()->make('MaterialService');
        $this->supplierService = app()->make('SupplierService');
	}
    
    public function index(Request $request)
    {
        return view('purchases.index')->with([
            "yearMonthSelect" => $this->purchaseService->getYearMonthSelect(),
            "nowMonthYear" => (new DateTime())->format('Y-m'),
        ]);
    }
    public function create(Request $request)
    {
        return view('purchases.create')->with([
            "suppliers" => Supplier::all(),
            // "materials" => Material::all(),

        ]);
    }

    public function store(Request $request){
        $user = $request->user;

        if(count($request->items) == 0)
            return \Response::json(["status"=> 200, "message"=> "no item"]);
            
        $purchase = Purchase::create([
            "prep_by" => $user->id,
            "branch_id" => $user->branch_id,
            "supplier_id" => $request->supplier,
            "voucher_date" => $request->voucher_date,
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


    public function delete(Request $request){
        $error = [];
        foreach($request->purchase_ids as $id){
            Purchase::find($id)->delete();
            PurchaseItem::where("purchase_id", $id)->delete();
        }
        return \Response::json(["status"=> 200, "error"=>$error]);
    }

    public function queryPurchases(Request $request){
        $items = $this->purchaseService->queryPurchases($request["search"], $request["order"]);
        return \Response::json(["data"=> $items]);
    }

    public function queryPurchaseItems(Request $request){
        $items = $this->purchaseService->queryPurchaseItems($request["search"], $request["order"]);
        return \Response::json(["data"=> $items]);
    }

    public function queryPurchaseItemsWithSupplier(Request $request){
        $items = $this->purchaseService->queryPurchaseItemsWithSupplier($request["search"], $request["order"]);
        return \Response::json(["data"=> $items]);
    }

    public function queryPurchaseItemsWithReturns(Request $request){
        $items = $this->purchaseService->queryPurchaseItemsWithReturns($request["search"], $request["order"]);
        return \Response::json(["data"=> $items]);
    }
    

    public function paid(Request $request){

        foreach($request->ids as $id){
            Purchase::where("id", $id)->update(["is_paid" => true]);
        }
        return \Response::json(["status"=> "200"]);
    }

    public function bulk_import(Request $request){
        $purchaseDict = [];
        if(count($request->purchases) <= 0)
            return \Response::json(["status"=> "200", "error"=> "no purchases"]);



        $supplier = Supplier::where("name", $request->purchases[0]["supplier"])->first();
        if(!$supplier){
            $supplier = Supplier::create([
                'supplier_no' => $this->supplierService->newSupplierNo(),
                'name' => $request->purchases[0]["supplier"]
            ]);
        }

        foreach($request->purchases as $purchase){
            if(!isset($purchaseDict[$purchase["purchase_date"]])){
                $purchaseDict[$purchase["purchase_date"]] = [];
            } 
            array_push($purchaseDict[$purchase["purchase_date"]], $purchase);
        }

        $user = $request->user;
        $returnPurchaseItems = [];
        foreach($purchaseDict as $key => $purchase){
            if (DateTime::createFromFormat('m/d', $key) === false) {
                return \Response::json(["status"=> "200", "error"=> "date format error"]);
            }
            $voucherDate = DateTime::createFromFormat('m/d', $key);
            $thisPurchase = Purchase::create([
                "prep_by" => $user->id,
                "branch_id" => $user->branch_id,
                "supplier_id" => $supplier->id,
                "voucher_date" => $voucherDate,
                "purchase_no" => $this->purchaseService->newPurchaseNo(),
                "payment_type" => "monthly",
                "note1" => "",
                "note2" => "",
            ]);

            $puchase_total = 0;
            $keepPurchase = false;
            foreach($purchase as $item){
                $material = Material::where("name", $item["material_name"])->first();
                if(!$material){
                    $material = Material::create([
                        'material_no' => $this->materialService->newMaterialNo(),
                        'supplier_id' => $supplier->id,
                        'name' => $item["material_name"],
                        'unit' => '個'
                    ]);
                }
                if($item["amount"] == 0 || $item["unit_price"] == 0){
                    $item["status"] = "amount or unit price is 0";
                } else if (!$material){
                    $item["status"] = "material not found";
                }else {
                    $purchaseItem = PurchaseItem::create([
                        "purchase_id" => $thisPurchase->id,
                        "material_id" => $material->id,
                        "amount" => $item["amount"],
                        "unit_price" => $item["unit_price"],
                        "total" => $item["total"]
                    ]);
                    $puchase_total += $item["total"];
                    $item["status"] = "success";
                    $keepPurchase = true;
                }
                array_push($returnPurchaseItems, $item);
            }

            if($keepPurchase){
                $thisPurchase->total = $puchase_total;
                $thisPurchase->save();
            } else {
                $thisPurchase->delete();
            }
        }
        

        return \Response::json(["status"=> "200", "purchase_items"=>$returnPurchaseItems]);
    }
}
