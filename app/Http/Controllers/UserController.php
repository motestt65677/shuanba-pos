<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    //
    public function __construct()
	{
        $this->userService = app()->make('UserService');
	}


    public function queryData(Request $request)
    {
        $users = $this->userService->queryData($request->search);
        return \Response::json(["data"=> $users]);
    }

    public function index(Request $request)
    {
        return view('users.index');
    }

    public function create(Request $request)
    {
        return view('users.create')->with(["roles" => Role::all()]);
    }

    public function edit(Request $request, $id){
        $userRoles = UserRole::where("user_id", $id)->pluck('role_id')->toArray();
        return view('users.edit')->with(
            [
                "roles" => Role::all(), 
                "userRoles" => $userRoles,
                "user" => User::find($id),
            ]
        );
    }
    public function update(Request $request){
        $error = [];

        $user = User::find($request->user_id);
        if(!$user)
            array_push($error, "查無帳號");
            
        if(count($error) == 0){
            $user->name = $request->name;
            $user->branch_id = $request->branch;
            $user->save();
    
            UserRole::where("user_id", $user->id)->delete();
            foreach($request->roles as $role){
                UserRole::create([
                    "user_id" => $user->id, 
                    "role_id" => $role
                ]);
            }
        }
        
        return \Response::json(["status"=> 200, "error"=>$error]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $error = [];
        $exists = User::where("username", $request->username)->first();
        if($exists){
            array_push($error, "帳號已經存在");
        }

        if(count($error) == 0){
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'branch_id' => $request->branch
            ]);

            foreach($request->roles as $role){
                UserRole::create([
                    "user_id" => $user->id, 
                    "role_id" => $role
                ]);
            }
        }

        return \Response::json(["status"=> 200, "error"=>$error]);
    }

    public function delete(Request $request){
        $error = [];
        foreach($request->user_ids as $id){
            
            $user = User::find($id);
            UserRole::where("user_id", $user->id)->delete();
            $user->delete();

        }
        return \Response::json(["status"=> 200, "error"=>$error]);
    }
}
