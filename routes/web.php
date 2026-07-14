<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show'])->name('home');

Route::get('test/{template}', function (string $template) {
    return view("test.{$template}", ['template' => $template]);
});

require_once __DIR__ . '/settings.php';
