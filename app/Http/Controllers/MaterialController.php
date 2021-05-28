<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    //
    public function __construct()
	{
        $this->materialService = app()->make('MaterialService');
	}

    public function index(Request $request){
        return view('materials.index');
    }

    public function queryData(Request $request)
    {
        $materials = $this->materialService->queryData($request->search);
        echo json_encode($materials);
    }



}
