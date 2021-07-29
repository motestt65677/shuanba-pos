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
        return view('closings.index')->with(["yearMonth"=>$this->closingService->closableYearMonth($request->user->branch_id)]);
    }

    public function queryClosings(Request $request){
        $search = $request["search"];
        $search["branch_id"] = $request->user->branch_id;
        $items = $this->closingService->queryClosings($search, $request["order"]);
        return \Response::json(["data"=> $items]);
    }
    public function queryClosingWithItems(Request $request){
        $search = $request["search"];
        $search["branch_id"] = $request->user->branch_id;
        $items = $this->closingService->queryClosingWithItems($search, $request["order"]);
        return \Response::json(["data"=> $items]);
    }

    public function create(Request $request){
        $this->closingService->closeMonth($request->year_month, [$request->user->branch_id]);
        return \Response::json(["status"=> "200"]);
    }
    
}
