<?php

namespace App\Http\Controllers;

use App\Models\Import;
use Illuminate\Http\Request;
use App\Models\ImportMaterial;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public function __construct()
	{
        $this->importService = app()->make('ImportService');
	}

    public function index(Request $request)
    {
        return view('imports.index');
    }

    public function edit(Request $request, $id){
        return view('imports.edit');
    }
    public function update(Request $request){
        $user = $request->user;
        $import = Import::find($request->import_id);
        
        if($import){
            $import->name = $request->import_name;
            $import->description = $request->import_description;
            $import->price = $request->import_price;
            $import->save();
            if(count($request->items) > 0){
                ImportMaterial::where("import_id", $import->id)->delete();
                foreach($request->items as $item){
                    if(isset($item["material_id"]) && isset($item["material_count"])){
                       ImportMaterial::create([
                            "import_id" =>$import->id,
                            "material_id" => $item["material_id"],
                            "material_count" => $item["material_count"]
                        ]);
                    }
                }
            }
        }
        
        return \Response::json(["status"=> 200]);
    }
    public function create(Request $request){
        return view('imports.create');
    }

    public function store(Request $request){
        $user = $request->user;

        if(count($request->items) > 0){
            $import = Import::create([
                "import_no" => $this->importService->newImportNo(),
                "name" => $request->import_name,
                "description" => $request->import_description,
                "price" => $request->import_price
            ]);

            ImportMaterial::where("import_id", $import->id)->delete();
            foreach($request->items as $item){
                if(isset($item["material_id"]) && isset($item["material_count"])){
                    ImportMaterial::create([
                        "import_id" =>$import->id,
                        "material_id" => $item["material_id"],
                        "material_count" => $item["material_count"]
                    ]);
                }
            }
        }
        
        return \Response::json(["status"=> 200]);
    }


    public function delete(Request $request){
        $error = [];
        foreach($request->import_ids as $id){
            $import = Import::find($id)->delete();
            $importMaterials = ImportMaterial::where("import_id", $id)->delete();
        }
        
        return \Response::json(["status"=> 200, "error"=>$error]);
    }

    public function queryData(Request $request)
    {
        $imports = $this->importService->queryData($request->search);
        return \Response::json(["data"=> $imports]);
    }
}
