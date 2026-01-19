<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produkte;
class HomeController extends Controller
{
    public function index(){



        $produkte = Produkte::all();
        return view('home',compact('produkte'));
    }
}

