<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    public function __construct()
	{
        $this->materialService = app()->make('MaterialService');
	}
    
    //
    public function index(Request $request)
    {
        return view('purchase_items.index')->with([
            "materials" => $this->materialService->getHasPurchasedMaterials($request->user->branch_id)
        ]);;
    }
}
