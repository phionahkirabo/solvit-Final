<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class project extends Controller
{
    //list of all projects
    public function index(){
        $project = Peoject::all();
    }
    //show 
}
