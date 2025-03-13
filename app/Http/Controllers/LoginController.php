<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //เปิดหน้า login
    public function index(){
        return view('login');
    }

    public function login(Request $req)
    {
        $user = User::where('email', $req->email)->first();

        if ($user && $req->password && Hash::check($req->password, $user->password)) {
            $req->session()->forget('error');
            $req->session()->put('user', $user);
            return redirect("/user");
        } else {
            $req->session()->put('error', 'ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง');
            return view("login");
        }
    }
}
