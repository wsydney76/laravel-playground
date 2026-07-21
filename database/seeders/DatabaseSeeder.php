<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Homepage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Remove all previously uploaded media files so seeding always starts fresh.
        $this->command->call('media:clear-uploads');

        // Ensure the singleton Homepage record always exists.
        Homepage::firstOrCreate(
            [],
            [
                'sitename' => 'My Site',
                'copyright' => 'My Site. All rights reserved.',
                'homepagetext' => fake()->paragraphs(4, true),
            ],
        );

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => 'kirby-tutorial',
        ]);

        User::factory(5)->create();

        Article::factory(40)->create();
    }
}
