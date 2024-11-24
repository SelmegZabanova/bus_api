<?php

namespace Database\Factories;

use App\Models\RouteStop;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            'route_stop_id' => RouteStop::factory(),
            'arrival_time' => $this->faker->time('H:i:s'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
