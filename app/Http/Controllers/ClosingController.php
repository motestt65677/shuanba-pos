<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClosingController extends Controller
{
    //
    public function __construct()
	{
        $this->closingService = app()->make('ClosingService');

	}

    public function index(Request $request)
    {
        return view('closings.index');
    }

    public function queryClosings(Request $request){
        $this->closingService->closeMonth();
        $items = $this->closingService->queryClosings($request["search"], $request["order"]);
        return \Response::json(["data"=> $items]);
    }
}
