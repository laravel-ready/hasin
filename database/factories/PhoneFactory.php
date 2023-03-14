<?php

namespace LaravelReady\Hasin\Database\Factories;

use LaravelReady\Hasin\Tests\Models\Phone;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneFactory extends Factory
{
    protected $model = Phone::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'phone_number' => $this->faker->phoneNumber
        ];
    }
}
