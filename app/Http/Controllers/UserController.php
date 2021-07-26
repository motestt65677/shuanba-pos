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
        return view('users.edit');
    }
    public function update(Request $request){
        $user = $request->user;
        $user = User::find($request->user_id);
        if($user){
            $user->name = $request->name;
            $user->save();
        }
        
        return \Response::json(["status"=> 200]);
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
        // $user = $request->user;
        $error = [];
        foreach($request->user_ids as $id){
            $user = User::find($id);
            $user->delete();
        }
        return \Response::json(["status"=> 200, "error"=>$error]);
    }
}
