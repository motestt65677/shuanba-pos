<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    //
    public function index(Request $request)
    {
        return view('purchase_items.index')->with([
            "materials" => Material::all()
        ]);;
    }
}
