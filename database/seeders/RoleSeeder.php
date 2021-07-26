<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // //
        // $roles = ["dashboard",
        // "purchases",
        // "purchase_returns",
        // "purchase_items",
        // "closings",
        // 'transactions',
        // "suppliers",
        // "materials",
        // "users",
        // "products",
        // "imports",
        // "mis"];

        $roles = ["儀表板",
        "廠商進貨分析",
        "廠商退貨分析",
        "材料進貨分析",
        "進耗存別關帳",
        "單據異動分析",
        "廠商管理",
        "材料管理",
        "帳號管理",
        "銷貨產品管理(Qlieer)",
        "進貨產品管理(Google)",
        "維護工具"];
        

        foreach($roles as $role){
            $exists = Role::where("role", $role)->first();
            if(!$exists){
                Role::create([
                    "role" => $role
                ]);
            }
        }
        

    }
}
