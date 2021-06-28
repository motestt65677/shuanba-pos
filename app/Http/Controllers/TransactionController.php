<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    //
    public function __construct()
	{
        $this->transactionService = app()->make('TransactionService');
	}


    public function queryData(Request $request)
    {
        $materials = $this->transactionService->queryData($request->search, $request->order);
        return \Response::json(["data"=> $materials]);
    }

    public function index(Request $request)
    {
        return view('transactions.index');
    }
}
