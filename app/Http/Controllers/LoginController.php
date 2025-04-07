<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $req)
    {
        $user = User::where('email', '=', $req->email)->first();

        if ($user && $req->password && Hash::check($req->password, $user->password)) {
            $req->session()->forget('error');
            $req->session()->put('user', $user);
            return redirect("/");
        } else {
            $req->session()->put('error', 'ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง');
            return redirect("/login");
        }
    }

    public function logout(Request $req)
    {
        // ลบข้อมูล user ออกจาก session
        $req->session()->forget('user');
        // ล้าง session ทั้งหมด
        $req->session()->flush();
        // Redirect ไปหน้า login
        return redirect('/login')->with('logoutSuccess', 'ออกจากระบบสำเร็จ');
    }
}