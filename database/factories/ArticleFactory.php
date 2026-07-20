<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(
            fake()
                ->unique()
                ->sentence(fake()->numberBetween(4, 7), false),
            '.',
        );
        $slug = Str::slug($title);

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'creator_id' => fn (array $attributes) => $attributes['user_id'],
            'title' => ['en' => $title . ' (en)', 'de' => $title . ' (de)'],
            'slug' => ['en' => $slug, 'de' => $slug],
            'body' => ['en' => fake()->paragraphs(3, true), 'de' => fake('de_DE')->paragraphs(3, true)],
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
