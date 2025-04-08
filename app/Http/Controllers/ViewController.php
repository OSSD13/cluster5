<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    //login
    public function login(){
        return view('auth.login');
    }
    //branch
    public function branchIndex(){
        return view('branch.index');
    }
    
    //branch manage
    public function branchManageIndex(){
        return view('branch.manage.index');
    }
    //dashboard
    public function dashbordIndex(){
        return view('dashboard.index');
    }
    //map
    public function mapIndex(){
        return view('map.index');
    }
    
    //poi
    public function poiIndex(){
        return view('poi.index');
    }
    
    //poi type
    public function poiTypeIndex(){
        return view('poi.type.index');
    }
    //user
    public function userIndex(){
        return view('user.index');
    }

}
