<?php
namespace Database\Factories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->numberBetween(1, 100), // номера маршрутов
            'created_at' => now(),
            'updated_at' => now(),
            ];
    }
}
