<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class DatabaseTestController extends Controller
{
    function createUser()
    {
        User::create([
            'name' => "tester",
            'email' => "test@example.com",
            'password' => "asd",
            "user_status" => "normal",
            "role_name" => "ceo",
        ]);
        return view('ok');
    }

    function displayUser()
    {
        $value = User::where('email', '=', 'test@example.com')->first();
        return view('displayDatabase', ['value' => $value]);
    }
}
