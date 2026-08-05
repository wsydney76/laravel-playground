<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $titles = ['Politics' => 'blue', 'Sport' => 'green', 'Entertainment' => 'red'];

        foreach ($titles as $title => $color) {
            Topic::firstOrCreate(['title' => $title, 'color' => $color]);
        }
    }
}
