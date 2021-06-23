<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Models\ImportConversion;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    //
    public function __construct()
	{
        $this->materialService = app()->make('MaterialService');
	}


    public function queryData(Request $request)
    {
        $materials = $this->materialService->queryData($request->search);
        return \Response::json(["data"=> $materials]);
    }

    public function index(Request $request)
    {
        return view('materials.index');
    }

    public function create(Request $request)
    {
        return view('materials.create');
    }

    public function edit(Request $request, $id){
        return view('materials.edit');
    }
    public function update(Request $request){
        $user = $request->user;
        $material = Material::find($request->material_id);
        if($material){
            $material->name = $request->material_name;
            $material->unit = $request->material_unit;
            $material->unit_price = $request->material_unit_price;
            $material->save();
        }
        if(count($request->items) > 0){
            ImportConversion::where("material_id", $material->id)->delete();
            foreach($request->items as $item){
                if(isset($item["import_price"]) && isset($item["import_unit"]) && isset($item["import_count"]) && isset($item["material_count"])){
                    ImportConversion::create([
                        "supplier_id" => $material->supplier_id,
                        "material_id" => $material->id,
                        "import_price" => $item["import_price"],
                        "import_unit" => $item["import_unit"],
                        "import_count" => $item["import_count"],
                        "material_count" => $item["material_count"]
                    ]);
                }
            }
        }
        
        return \Response::json(["status"=> 200]);
    }
    public function store(Request $request){
        $user = $request->user;
        $material = Material::create([
            'material_no' => $this->materialService->newMaterialNo(),
            'supplier_id' => $request->supplier_id,
            'name' => $request->material_name,
            'unit' => $request->material_unit,
            'unit_price' => $request->material_unit_price,
        ]);

        if(count($request->items) > 0){
            ImportConversion::where("material_id", $material->id)->delete();
            foreach($request->items as $item){
                ImportConversion::create([
                    "supplier_id" => $material->supplier_id,
                    "material_id" => $material->id,
                    "import_price" => $item["import_price"],
                    "import_unit" => $item["import_unit"],
                    "import_count" => $item["import_count"],
                    "material_count" => $item["material_count"]
                ]);
            }
        }

        return \Response::json(["status"=> 200]);
    }

    public function delete(Request $request){
        // $user = $request->user;
        $error = [];
        foreach($request->material_ids as $id){
            $material = Material::find($id);
            $purchaseItem = PurchaseItem::where("material_id", $id)->first();
            if($purchaseItem){
                array_push($error, $material->name . '(' . $material->material_no . ')' .'已經有採購紀錄，無法被刪除');
                continue;
            } 
            $material->delete();
        }
        
        return \Response::json(["status"=> 200, "error"=>$error]);
    }


}
