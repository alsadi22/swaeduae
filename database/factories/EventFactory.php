<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = \App\Models\Event::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'date' => $this->faker->dateTimeBetween('now', '+2 months'),
            'location' => $this->faker->city,
            'image' => 'https://picsum.photos/seed/' . rand(1000,9999) . '/600/400',
            'status' => 'active',
        ];
    }
}
