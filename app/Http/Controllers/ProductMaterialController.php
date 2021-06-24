<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductMaterialController extends Controller
{
    //
    public function __construct()
	{
        $this->productMaterialService = app()->make('ProductMaterialService');
	}

    public function queryData(Request $request){
        $items = $this->productMaterialService->queryData($request["search"]);
        return \Response::json(["data"=> $items]);
    }
}
