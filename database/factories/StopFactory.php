<?php

namespace Database\Factories;

use App\Models\Stop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StopFactory extends Factory
{
    protected $model = Stop::class;

    public function definition(): array
    {
        return [
        'name' => $this->faker->unique()->streetAddress(),
        'created_at' => now(),
        'updated_at' => now(),
        ];
    }
}
