<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function login(){
        return view('auth.login'); // Add the missing semicolon here
    }
    //branch
    public function branchIndex(){
        return view('branch.index');
    }
    public function branchCreate(){
        return view('branch.create');
    }
    public function branchEdit(){
        return view('branch.edit');
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
    //layout
    public function layoutDependency(){
        return view('layouts.dependency');
    }
    public function layoutMain(){
        return view('layouts.main');
    }
    public function layoutScreenr(){
        return view('layouts.screen');
    }
    //poi
    public function poiIndex(){
        return view('poi.index');
    }
    public function poiEdit(){
        return view('poi.edit');
    }
    public function poiCreate(){
        return view('poi.create');
    }
    //poi type
    public function poiTypeIndex(){
        return view('poi.type.index');
    }
    public function poiTypeEdit(){
        return view('poi.type.edit');
    }
    public function poiTypeCreate(){
        return view('poi.type.create');
    }
    //user
    public function userIndex(){
        return view('user.index');
    }

}
