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

    public function callbackGoogle()
    {
        // try {
            $google_user = Socialite::driver('google')->user();

            $user = User::where('email', '=', $google_user->getEmail())->first();


            if (!$user) {
                return redirect('/login');
            } else {
                // auth()->login($user, true);
                return redirect('/user');
            }
        // } catch (\Exception $e) {
        //     return redirect('/error');
        // }
    }
}
