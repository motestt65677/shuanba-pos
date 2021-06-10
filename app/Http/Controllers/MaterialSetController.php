<?php

namespace App\Http\Controllers;

use App\Models\MaterialSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaterialSetController extends Controller
{
    //
    public function __construct()
	{
        $this->materialService = app()->make('MaterialService');
	}

    public function queryData(Request $request)
    {
        $sets = $this->materialService->queryMaterialSet($request->search);
        return \Response::json(["data"=> $sets]);
    }

    public function index(Request $request)
    {
        return view('material_sets.index');
    }

    public function create(Request $request)
    {
        return view('material_sets.create');
    }

    public function edit(Request $request, $id){
        return view('material_sets.edit');
    }

    public function update(Request $request){
        $set = MaterialSet::find($request->set_id);
        if($set){
            $set->supplier_id = $request->supplier_id;
            $set->material_id = $request->material_id;
            $set->name = $request->set_name;
            $set->set_unit_price = $request->set_unit_price;
            $set->material_count = $request->material_count;
            $set->save();
        }
        return \Response::json(["status"=> 200]);
    }

    public function store(Request $request){
        $material = MaterialSet::create([
            'supplier_id' => $request->supplier_id,
            'material_id' => $request->material_id,
            'name' => $request->set_name,
            'set_unit_price' => $request->set_unit_price,
            'material_count' => $request->material_count,
        ]);
        return \Response::json(["status"=> 200]);
    }

    public function delete(Request $request){
        foreach($request->set_ids as $id){
            $set = MaterialSet::find($id)->delete();
        }
        return \Response::json(["status"=> 200]);
    }
}
