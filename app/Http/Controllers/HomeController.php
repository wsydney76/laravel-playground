<?php

namespace App\Http\Controllers;

use App\Models\Homepage;

class HomeController extends Controller
{
    //
    public function show()
    {
        $page = Homepage::getSingleton();
        return view('home', compact('page'));
    }
}
