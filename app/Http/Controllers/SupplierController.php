<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function __construct()
	{
        $this->supplierService = app()->make('SupplierService');
	}
    //
    public function queryData(Request $request)
    {
        $suppliers = $this->supplierService->queryData($request["search"]);
        return \Response::json(["data"=> $suppliers]);
    }

    public function index(Request $request)
    {

        return view('suppliers.index');
    }

    public function create(Request $request)
    {
        return view('suppliers.create');
    }

    public function edit(Request $request, $id){
        return view('suppliers.edit');
    }
    public function update(Request $request){
        $user = $request->user;
        $supplier = Supplier::find($request->supplier_id);
        if($supplier){
            $supplier->name = $request->supplier_name;
            $supplier->phone = $request->supplier_phone;
            $supplier->cellphone = $request->supplier_cellphone;
            $supplier->address = $request->supplier_address;
            $supplier->note1 = $request->supplier_note1;
            $supplier->tax_id = $request->supplier_tax_id;
            $supplier->save();
        }
        return \Response::json(["status"=> 200]);
    }
    public function store(Request $request){
        $user = $request->user;
        
        $purchase = Supplier::create([
            'supplier_no' => $this->supplierService->newMaterialNo(),
            'name' => $request->supplier_name,
            'phone' => $request->supplier_phone,
            'cellphone' => $request->supplier_cellphone,
            'address' => $request->supplier_address,
            'note1' => $request->supplier_note1,
            'tax_id' => $request->supplier_tax_id,
        ]);

        return \Response::json(["status"=> 200]);
    }

    public function delete(Request $request){
        // $user = $request->user;
        $error = [];
        foreach($request->supplier_ids as $id){
            $supplier = Supplier::find($id);
            $material = Material::where("supplier_id", $id)->first();
            if($material){
                array_push($error, $supplier->name . '(' . $supplier->supplier_no . ')' .'已經有產品，無法被刪除');
                continue;
            } 
            $supplier -> delete();
        }
        
        return \Response::json(["status"=> 200, "error"=>$error]);
    }

}
