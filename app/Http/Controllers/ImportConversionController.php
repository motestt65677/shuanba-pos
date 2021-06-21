<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportConversionController extends Controller
{
    //
    public function __construct()
	{
        $this->importConversionService = app()->make('ImportConversionService');
	}

    public function queryData(Request $request){
        $items = $this->importConversionService->queryData($request["search"]);
        return \Response::json(["data"=> $items]);
    }

}
