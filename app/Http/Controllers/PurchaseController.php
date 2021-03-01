<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    //
    public function create(Request $request)
    {
        return view('purchases.create');
    }
}
