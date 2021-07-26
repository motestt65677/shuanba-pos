<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BranchController extends Controller
{
    //
    public function __construct()
	{
        $this->branchService = app()->make('BranchService');
	}


    public function queryData(Request $request)
    {
        $branches = $this->branchService->queryData($request->search);
        return \Response::json(["data"=> $branches]);
    }
}
