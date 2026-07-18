<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show'])->name('home');

Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
Route::livewire('articles/search', 'articles.search')->name('articles.search');

Route::middleware('auth')->group(function () {
    Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('articles/{article}/edit', [ArticleController::class, 'edit'])->name(
        'articles.edit',
    );
    Route::put('articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name(
        'articles.destroy',
    );
});

Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('test/{template}', function (string $template) {
    return view("test.{$template}", ['template' => $template]);
});

Route::middleware('auth')->group(function () {
    Route::livewire('dashboard/articles', 'pages::dashboard.articles')->name('dashboard.articles');
    Route::get('dashboard/users', [AdminController::class, 'users'])
        ->middleware('can:administer,App\Models\Article')
        ->name('dashboard.users');
});

require_once __DIR__ . '/settings.php';
