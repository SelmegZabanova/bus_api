<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\RouteDirection;
use App\Models\RouteStop;
use App\Models\Schedule;
use App\Models\Stop;
use Illuminate\Database\Seeder;

class TransportationSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем 20 остановок
        $stops = Stop::factory(20)->create();

        // Создаем 5 маршрутов
        Route::factory(5)->create()->each(function ($route) use ($stops) {
            // Для каждого маршрута создаем 2 направления (прямое и обратное)
            RouteDirection::factory(2)->create([
                'route_id' => $route->id
            ])->each(function ($direction) use ($stops) {
                // Выбираем случайное количество остановок (от 5 до 10)
                $randomStops = $stops->random(rand(5, 10));

                // Создаем остановки для направления
                foreach ($randomStops as $index => $stop) {
                    $routeStop = RouteStop::query()->create([
                        'route_direction_id' => $direction->id,
                        'stop_id' => $stop->id,
                        'stop_order' => $index + 1
                    ]);

                    // Создаем 10 времен прибытия для каждой остановки
                    $baseTime = strtotime('06:00:00');
                    for ($i = 0; $i < 10; $i++) {
                        Schedule::create([
                            'route_stop_id' => $routeStop->id,
                            'arrival_time' => date('H:i:s', $baseTime + ($i * 3600)), // каждый час
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            });
        });
    }
}
