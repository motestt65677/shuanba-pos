<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
	{
        $this->supplierService = app()->make('SupplierService');
	}
    //
    public function queryData(Request $request)
    {
        $suppliers = $this->supplierService->queryData();
        echo json_encode($suppliers);
    }

}
