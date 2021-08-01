<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Import;
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
            "purchase_no" => $this->purchaseService->newPurchaseNo($user->branch_id),
            "payment_type" => $request->payment_type,
            "note1" => $request->note1,
            "note2" => $request->note2,
        ]);
        $puchase_total = 0;
        foreach($request->items as $item){
            if($item["amount"] == 0 || $item["unit_price"] == 0)
                continue;

            PurchaseItem::create([
                "branch_id" => $user->branch_id,
                "purchase_id" => $purchase->id,
                "material_id" => $item["item_id"],
                "amount" => $item["amount"],
                "unit_price" => $item["unit_price"],
                "total" => $item["total"]
            ]);
            $puchase_total += $item["total"];
        }

        $purchase->total = $puchase_total;
        $purchase->save();

        return \Response::json(["status"=> 200]);
    }


    public function delete(Request $request){
        $error = [];
        foreach($request->purchase_ids as $id){
            $purchase = Purchase::find($id);
            if(!$purchase)
                continue;
            if($purchase->is_paid){
                array_push($error, "單據編號:".$purchase->purchase_no."，已付款無法刪除");
                continue;
            }
            PurchaseItem::where("purchase_id", $id)->delete();
            PurchaseReturn::where("purchase_id", $id) ->delete();
            Purchase::find($id)->delete();
        }
        return \Response::json(["status"=> 200, "error"=>$error]);
    }

    public function queryPurchases(Request $request){
        $search = $request["search"];
        $search["branch_id"] = $request->user->branch_id;
        $items = $this->purchaseService->queryPurchases($search, $request["order"]);
        return \Response::json(["data"=> $items]);
    }

    public function queryPurchaseItems(Request $request){
        $search = $request["search"];
        $search["branch_id"] = $request->user->branch_id;
        $items = $this->purchaseService->queryPurchaseItems($search, $request["order"]);
        return \Response::json(["data"=> $items]);
    }

    public function queryPurchaseItemsWithSupplier(Request $request){
        $order = isset($request["order"]) ? $request["order"] : [];
        $search = $request["search"];
        $search["branch_id"] = $request->user->branch_id;
        $items = $this->purchaseService->queryPurchaseItemsWithSupplier($search, $order);
        return \Response::json(["data"=> $items]);
    }

    public function queryPurchaseItemsWithReturns(Request $request){
        $order = isset($request["order"]) ? $request["order"] : [];
        $search = isset($request["search"]) ? $request["search"] : [];
        $search["branch_id"] = $request->user->branch_id;
        $items = $this->purchaseService->queryPurchaseItemsWithReturns($search, $order);
        return \Response::json(["data"=> $items]);
    }
    

    public function paid(Request $request){
        foreach($request->ids as $id){
            Purchase::where("id", $id)->update(["is_paid" => true]);
        }
        return \Response::json(["status"=> "200"]);
    }

    public function unpay(Request $request){
        foreach($request->ids as $id){
            Purchase::where("id", $id)->update(["is_paid" => false]);
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
                "purchase_no" => $this->purchaseService->newPurchaseNo($user->branch_id),
                "payment_type" => "monthly",
                "note1" => "",
                "note2" => "",
            ]);

            $puchase_total = 0;
            $keepPurchase = false;
            foreach($purchase as $item){
                $import = Import::where("name",$item["material_name"])->first();
                if(!$import){
                    $item["status"] = "進貨產品不存在";
                } else {
                    $importMaterials = $import->importMaterials;
                    foreach($importMaterials as $importMaterial){
                        $material = Material::where("id", $importMaterial["material_id"])->first();
                        if($material){
                            $purchaseItem = PurchaseItem::create([
                                "purchase_id" => $thisPurchase->id,
                                "branch_id" => $user->branch_id,
                                "material_id" => $material->id,
                                "amount" => floatval($importMaterial->material_count) * floatval($item["amount"]),
                                "unit_price" => floatval($item["total"]) / (floatval($importMaterial->material_count) * floatval($item["amount"])),
                                "total" => $item["total"]
                            ]);
                            $puchase_total += $item["total"];
                            $item["status"] = "success";
                            $keepPurchase = true;
                        }
                    }
                }
                array_push($returnPurchaseItems, $item);
            }

            if($keepPurchase){
                $thisPurchase->total = $puchase_total;
                $thisPurchase->save();
            } else {
                $thisPurchase->forceDelete();
            }
        }
        

        return \Response::json(["status"=> "200", "purchase_items"=>$returnPurchaseItems]);
    }
}
