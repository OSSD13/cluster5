<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class RegisterController extends Controller
{
    //
    function index()
    {
        return view('register');
    }

    function create()
    {
        //$obj_user =  new User;
        //$obj_user->name =  $req->input('name');
        //$obj_user->email =  $req-> email;
        //$obj_user->password =$req->password;
        //$obj->_user ->save();

        // if there is no name generate one
        User::create([
            'name' => "tester",
            'email' => "test@example.com",
            'password' => "kuy",
            "user_status" => "normal",
            "role_name" => "ceo",

        ]);

        // User::create([
        //     'name' => $req->name,
        //     'email' => $req->email,
        //     'password' => $req->password
        // ]);
        return view('ok');
        // return redirect('/user');
    }

    function displayLogin()
    {
        $user = User::where('email', '=', 'test@example.com')->first();
        return view('userInfoDisplay', ['user' => $user]);
    }
}
