<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TopicController;
use App\Models\Article;
use App\Notifications\ArticleCreated;
use Illuminate\Support\Facades\Route;

Route::get('/notification', function () {
    $article = Article::first();
    return (new ArticleCreated($article))->toMail($article->user);
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::livewire('dashboard/articles', 'pages::dashboard.articles')->name('dashboard.articles');
    Route::livewire('dashboard/users', 'pages::dashboard.users')->name('dashboard.users');
    Route::livewire('dashboard/notifications', 'pages::dashboard.notifications')->name('dashboard.notifications');
    Route::livewire('dashboard/homepage', 'pages::dashboard.homepage')->name('dashboard.homepage');
    Route::livewire('dashboard/topics', 'pages::dashboard.topics')->name('dashboard.topics');
});

Route::get('/', function () {
    return redirect('/' . config('app.locale'));
});

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

Route::prefix('{locale}')
    ->middleware('locale')
    ->group(function () {
        Route::get('', [HomeController::class, 'show'])->name('home');
        Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('articles/{article}/{slug}', [ArticleController::class, 'show'])->name(
            'articles.show',
        );
        Route::get('articles/my', [ArticleController::class, 'my'])->name('articles.my');
        Route::livewire('articles/search', 'articles.search')->name('articles.search');
        Route::get('topics', [TopicController::class, 'index'])->name('topics.index');
        Route::get('topics/{topic}/{slug}', [TopicController::class, 'show'])->name('topics.show');
    });

Route::get('test/{template}', function (string $template) {
    return view("test.{$template}", ['template' => $template]);
});

require_once __DIR__ . '/settings.php';
