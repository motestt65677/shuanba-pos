<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MisController extends Controller
{
    //
    public function index(Request $request){
        return view('mis.index');
    }
}
