<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Homepage;

class HomeController extends Controller
{
    //
    public function show()
    {
        $page = Homepage::getSingleton();
        $articlesCount = Article::count();
        return view('home', compact('page', 'articlesCount'));
    }
}
