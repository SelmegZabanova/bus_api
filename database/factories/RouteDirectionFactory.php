<?php

namespace Database\Factories;

use App\Enums\DirectionType;
use App\Models\Route;
use App\Models\RouteDirection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RouteDirectionFactory extends Factory
{

    protected $model = RouteDirection::class;

    public function definition(): array
    {
        return [
            'route_id' => Route::factory(),
            'direction' => $this->faker->randomElement([
                DirectionType::FORWARD->value,
                DirectionType::BACKWARD->value
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
