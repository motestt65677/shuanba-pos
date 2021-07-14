<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductMaterial;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    //
    public function __construct()
	{
        $this->productService = app()->make('ProductService');
	}

    public function index(Request $request)
    {
        return view('products.index');
    }

    public function edit(Request $request, $id){
        return view('products.edit');
    }

    public function update(Request $request){
        $product = Product::find($request->product_id);
        
        if($product){
            if(isset($request->name))
                $product->name = $request->product_name;
            if(isset($request->description))
                $product->description = $request->product_description;
            if(isset($request->price))
                $product->price = $request->product_price;
            $product->save();
            if(count($request->items) > 0){
                ProductMaterial::where("product_id", $product->id)->delete();
                foreach($request->items as $item){
                    if(isset($item["material_id"]) && isset($item["material_count"])){
                        ProductMaterial::create([
                            "product_id" =>$product->id,
                            "material_id" => $item["material_id"],
                            "material_count" => $item["material_count"]
                        ]);
                    }
                }
            }
        }
        
        return \Response::json(["status"=> 200]);
    }

    public function delete(Request $request){
        // $user = $request->user;
        $error = [];
        foreach($request->product_ids as $id){
            $product = Product::find($id)->delete();
            $productMaterials = ProductMaterial::where("product_id", $product->id)->delete();
        }
        
        return \Response::json(["status"=> 200, "error"=>$error]);
    }

    public function queryData(Request $request)
    {
        $products = $this->productService->queryData($request->search);
        return \Response::json(["data"=> $products]);
    }
}
