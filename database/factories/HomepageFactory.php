<?php

namespace Database\Factories;

use App\Models\Homepage;
use Illuminate\Database\Eloquent\Factories\Factory;

class HomepageFactory extends Factory
{
    protected $model = Homepage::class;

    public function definition(): array
    {
        return [
            'sitename' => $this->faker->company(),
            'homepagetext' => $this->faker->paragraph(),
            'copyright' => '© ' . date('Y') . ' ' . $this->faker->company(),
        ];
    }
}

