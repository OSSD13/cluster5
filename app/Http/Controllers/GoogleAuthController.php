<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;


class GoogleAuthController extends Controller
{
    //
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle(Request $req)
    {
        // try {
            $google_user = Socialite::driver('google')->user();

            $user = User::where('email', '=', $google_user->getEmail())->first();


            if (!$user) {
                $req->session()->put('error', 'ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง');
                return redirect()->route('loginGet');
            } else {
                $req->session()->forget('error');
                $req->session()->put(key: 'user', value: $user);
                return redirect()->route('dashboard');
            }
        // } catch (\Exception $e) {
        //     return redirect('/error');
        // }
    }
}
