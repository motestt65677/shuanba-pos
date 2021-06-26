<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClosingController extends Controller
{
    //
    public function __construct()
	{
        $this->closingService = app()->make('ClosingService');

	}

    public function index(Request $request)
    {
        return view('closings.index')->with(["yearMonth"=>$this->closingService->closableYearMonth()]);
    }

    public function queryClosings(Request $request){
        $items = $this->closingService->queryClosings($request["search"], $request["order"]);
        return \Response::json(["data"=> $items]);
    }
    public function queryClosingWithItems(Request $request){
        $items = $this->closingService->queryClosingWithItems($request["search"], $request["order"]);
        return \Response::json(["data"=> $items]);
    }

    public function create(Request $request){
        $this->closingService->closeMonth($request->year_month);
        return \Response::json(["status"=> "200"]);
    }
    
}
