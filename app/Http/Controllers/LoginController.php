<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    //
    function login(){
        return view('login');
    }
}
