<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClosingItemController extends Controller
{
    public function __construct()
    {
        $this->closingService = app()->make('ClosingService');

    }



    public function queryItems(Request $request){
        // $this->closingService->closeMonth();
        $items = $this->closingService->queryClosingItems($request["search"]);
        return \Response::json(["data"=> $items]);
    }
}
