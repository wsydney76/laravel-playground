<?php

use App\Facades\Articles;
use App\Models\Article;
use App\Models\Homepage;
use App\Models\User;

beforeEach(function () {
    Homepage::factory()->create();
});

it('returns redirect without URI', function () {
    $response = $this->get('/');
    $response->assertRedirect('/en');
});

it('returns a successful response', function () {
    $response = $this->get('/en');
    $response->assertOk();
});

it('returns a successful response (de)', function () {
    $response = $this->get('/de');
    $response->assertOk();
});

it('returns a 404 response for nonexistent locale', function () {
    $response = $this->get('/nonexistent');
    $response->assertNotFound();
});

it('passes correct articles count to view, acting as guest', function () {
    Article::factory()->count(5)->create();

    $response = $this->get('/en');
    $response->assertViewHas('articlesCount', 5);
});

it('passes correct articles count to view, acting as user', function () {
    Article::factory()->count(5)->create();
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this->actingAs($user)->get('/en');
    $response->assertViewHas('articlesCount', 5);
});

it('shows correct button caption (en)', function () {
    Article::factory()->count(5)->create();

    $response = $this->get('/en');
    $response->assertSee('View 5 Articles');
});

it('shows correct button caption (de)', function () {
    Article::factory()->count(5)->create();

    $response = $this->get('/de');
    $response->assertSee('5 Artikel zeigen');
});

it('resolves a featured article as guest', function () {
    $article = Article::factory()->create();

    $response = $this->get('/en');
    $response->assertViewHas('featuredArticle', function ($viewArticle) use ($article) {
        return $viewArticle instanceof Article && $viewArticle->title === $article->title;
    });
});

it('resolves a featured article as user', function () {
    $article = Article::factory()->create();
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this->actingAs($user)->get('/en');
    $response->assertViewHas('featuredArticle', function ($viewArticle) use ($article) {
        return $viewArticle instanceof Article && $viewArticle->title === $article->title;
    });
});

it('refreshes a featured article after deletion', function () {
    $article = Article::factory()->create(['title' => 'The first article']);

    $response = $this->get('/en');
    $response->assertViewHas('featuredArticle', function ($viewArticle) {
        return $viewArticle instanceof Article && $viewArticle->title === 'The first article';
    });

    $article->delete();
    Article::factory()->create(['title' => 'The second article']);

    $response = $this->get('/en');
    $response->assertViewHas('featuredArticle', function ($viewArticle) {
        return $viewArticle instanceof Article && $viewArticle->title === 'The second article';
    });
});
