<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    //

    public function queryData(Request $request)
    {
        $materials = Material::where("supplier_id", $request->supplier_id)->select("name", "id")->get();
        echo json_encode($materials);
    }
}
