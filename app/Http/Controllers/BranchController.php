<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BranchController extends Controller
{
    //
    public function index(){
        return view('branch.index');
    }
    public function create(){
        return view('branch.create');
    }
    public function edit(){
        return view('branch.edit');
    }
    public function manage(){
        return view('branch.manage.index');
    }


}
