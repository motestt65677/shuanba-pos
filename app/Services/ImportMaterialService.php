<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportMaterialService
{
    public function queryData($search = null, $order = []){
        $query = DB::table('import_materials')
        ->select(
            'id',
            'import_id',
            'material_id',
            DB::raw("IFNULL((SELECT `material_no` FROM `materials` WHERE `id`=`import_materials`.`material_id` ), '') AS `material_no`"),
            DB::raw("IFNULL((SELECT `name` FROM `materials` WHERE `id`=`import_materials`.`material_id` ), '') AS `material_name`"),
            'material_count'
        );

        if(!isset($search["import_id"])){
            return [];
        }
        $query->where("import_id", $search["import_id"]);
        $items = $query->get();

        foreach($items as $item){
            $item->material_name_and_no = $item->material_name . ' ('. $item->material_no . ')';
        }

        return $items;
    }
}
