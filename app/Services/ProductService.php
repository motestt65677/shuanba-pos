<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ProductService
{
    public function newProductNo(){
        $numStr = "0001";
        $char = "P";

        $sql = "SELECT RIGHT(product_no,4) AS num FROM `products`
                WHERE product_no LIKE '{$char}%'
                ORDER BY product_no DESC
                LIMIT 1
        ";
        $rt = DB::select($sql);
        if(count($rt) == 1){
            $int = (int)$rt["0"]->num;
            $int += 1;
            $numStr = substr("00000".(string)$int, -4);
        }
        return $char . $numStr;
    }

    public function queryData($search = [], $order = []){
        $query = DB::table('products')
        ->select(
            "products.id AS product_id",
            "products.product_no AS product_no",
            "products.name AS product_name",
            "products.description AS product_description",
            "products.price AS product_price",
            "product_materials.material_count AS material_count",
            "materials.name AS material_name",
            "materials.material_no AS material_no",
        )
        ->leftJoin("product_materials", "products.id", "=", "product_materials.product_id")
        ->leftJoin("materials", "materials.id", "=", "product_materials.material_id");

        if(isset($search["product_id"]))
            $query->where("products.id", $search["product_id"]);

        foreach($order as $key=>$value){
            $query->orderBy($key, $value);
        }

        $items = $query->get();
        $productDict = [];

        foreach($items as $item){
            if(!isset($productDict[$item->product_id])){

                $productDict[$item->product_id] = [
                    "product_id" => $item->product_id,
                    "product_no" => $item->product_no,
                    "product_name" => $item->product_name,
                    "product_description" => $item->product_description,
                    "product_price" => $item->product_price,
                    "product_material_count" => 0,
                    "product_material_list" => ""
                ];
            }

            if(isset($item->material_count)){
                $productDict[$item->product_id]["product_material_count"]++;
                $productDict[$item->product_id]["product_material_list"] .= $item->material_no . '(' .$item->material_name . ')' . '  &times;  ' . round($item-> material_count,2) . "</br>";
            }
        }

        $returnArray = [];
        foreach($productDict as $key=>$value){
            array_push($returnArray, $value);
        }

        return $returnArray;
    }
    public function queryMaterialSet($search = []){
        $query = DB::table('material_sets')
        ->select(
            "material_sets.id AS set_id",
            "material_sets.material_id AS material_id",
            "material_sets.supplier_id AS supplier_id",
            "material_sets.name AS set_name",
            "material_sets.set_unit_price AS set_unit_price",
            "material_sets.material_count AS material_count",
            DB::raw("IFNULL((SELECT `name` FROM `suppliers` WHERE `id`=`material_sets`.`supplier_id` ), '') AS `supplier_name`"),
            DB::raw("IFNULL((SELECT `supplier_no` FROM `suppliers` WHERE `id`=`material_sets`.`supplier_id` ), '') AS `supplier_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`material_sets`.`material_id` ), '') AS `material_name`"),
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`material_sets`.`material_id` ), '') AS `material_no`"),
        );
        if(isset($search["set_id"]))
            $query->where("material_sets.id", $search["set_id"]);

        if(isset($search["supplier_id"]))
            $query->where("material_sets.supplier_id", $search["supplier_id"]);

        // if(isset($search["count"]))
        //     $query->take($search["count"]);

        // foreach($order as $key=>$value){
        //     $query->orderBy($key, $value);
        // }

        $items = $query->get();
        foreach($items as $item){
            $item->supplier_name_and_no = $item->supplier_no . ' - '. $item->supplier_name;
            $item->material_name_and_no = $item->material_no . ' - '. $item->material_name;
            $item->material_unit_price = round($item->set_unit_price / $item->material_count, 2);
        }
        return $items;
    }

}
