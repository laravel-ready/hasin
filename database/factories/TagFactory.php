<?php

namespace LaravelReady\Hasin\Database\Factories;

use LaravelReady\Hasin\Tests\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name
        ];
    }
}
