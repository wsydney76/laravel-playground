<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            ['title' => ['en' => 'Politics',      'de' => 'Politik'],      'slug' => ['en' => 'politics',      'de' => 'politik'],      'color' => 'blue'],
            ['title' => ['en' => 'Sport',          'de' => 'Sport'],         'slug' => ['en' => 'sport',          'de' => 'sport'],         'color' => 'green'],
            ['title' => ['en' => 'Entertainment',  'de' => 'Unterhaltung'],  'slug' => ['en' => 'entertainment',  'de' => 'unterhaltung'],  'color' => 'red'],
        ];

        foreach ($topics as $data) {
            Topic::create($data);
        }
    }
}
