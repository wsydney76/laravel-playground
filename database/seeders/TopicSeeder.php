<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $titles = ['Politics', 'Sport', 'Entertainment'];

        foreach ($titles as $title) {
            Topic::firstOrCreate(['title' => $title]);
        }
    }
}

