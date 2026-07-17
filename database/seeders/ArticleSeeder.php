<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    use WithoutModelEvents;

    // php artisan db:seed --class=ArticleSeeder
    public function run(): void
    {
        Article::factory(20)->create();
    }
}
