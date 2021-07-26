<?php

namespace App\Providers;

use App\Models\UserRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using Closure based composers...
        View::composer('layouts.navigation', function ($view) {
            $user = Auth::user();
            $roles = UserRole::select("user_roles.role_id as role_id", "roles.role as role_name")
            ->leftJoin("roles", "roles.id", "user_roles.role_id")
            ->where("user_roles.user_id", $user->id)
            ->pluck("role_name")
            ->toArray();
            $view->with(["appUser"=> $user, "appRoles"=>$roles]);
        });
    }
}
