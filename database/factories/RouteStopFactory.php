<?php

namespace Database\Factories;

use App\Models\RouteDirection;
use App\Models\RouteStop;
use App\Models\Stop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RouteStopFactory extends Factory
{
    protected $model = RouteStop::class;

    public function definition(): array
    {
        return [
            'route_direction_id' => RouteDirection::factory(),
            'stop_id' => Stop::factory(),
            'stop_order' => $this->faker->numberBetween(1, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
