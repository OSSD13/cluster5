<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    function view() {
        return view('test _login');
    }

    function login(Request $req){
        $user = User::where('email', $req->email)->first();
        if($user != null && Hash::check($req->password, $user->password)){
            $req->session()->put('user', $user);
            return redirect('/convert-link');
        
        }
        else{
            $req->session()->flash('error', 'ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง');
                    return redirect('/login');
        }
    }
}