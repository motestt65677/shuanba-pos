<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportMaterialController extends Controller
{
    public function __construct()
	{
        $this->importMaterialService = app()->make('ImportMaterialService');
	}

    public function queryData(Request $request){
        $items = $this->importMaterialService->queryData($request["search"]);
        return \Response::json(["data"=> $items]);
    }
}
