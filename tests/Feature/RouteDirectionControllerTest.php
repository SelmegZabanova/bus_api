<?php

namespace Tests\Feature;

use App\Enums\DirectionType;
use App\Models\Route;
use App\Models\RouteDirection;
use App\Models\RouteStop;
use App\Models\Stop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteDirectionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_direction_type(): void
    {
        // Создаем маршрут с направлением
        $route = Route::factory()->create();
        $routeDirection = RouteDirection::factory()->create([
            'route_id' => $route->id,
            'direction' => DirectionType::FORWARD->value
        ]);

        $updateData = [
            'direction' => DirectionType::BACKWARD->value
        ];

        $response = $this->putJson("/api/route-directions/{$routeDirection->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'route' => [
                    'id',
                    'direction',
                    'route_stops'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Маршрут успешно обновлен'
            ]);

        $this->assertDatabaseHas('route_directions', [
            'id' => $routeDirection->id,
            'direction' => DirectionType::BACKWARD->value
        ]);
    }

    public function test_can_update_stops(): void
    {
        // Создаем маршрут с направлением
        $route = Route::factory()->create();
        $routeDirection = RouteDirection::factory()->create([
            'route_id' => $route->id
        ]);

        // Создаем новые остановки
        $stops = Stop::factory()->count(3)->create();

        $updateData = [
            'stops' => $stops->pluck('id')->toArray()
        ];

        $response = $this->putJson("/api/route-directions/{$routeDirection->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'route' => [
                    'id',
                    'direction',
                    'route_stops' => [
                        '*' => [
                            'id',
                            'stop_order',
                            'stop' => [
                                'id',
                                'name'
                            ]
                        ]
                    ]
                ]
            ]);

        // Проверяем, что остановки созданы с правильным порядком
        foreach ($stops as $index => $stop) {
            $this->assertDatabaseHas('route_stops', [
                'route_direction_id' => $routeDirection->id,
                'stop_id' => $stop->id,
                'stop_order' => $index + 1
            ]);
        }
    }

    public function test_can_update_both_direction_and_stops(): void
    {
        $route = Route::factory()->create();
        $routeDirection = RouteDirection::factory()->create([
            'route_id' => $route->id,
            'direction' => DirectionType::FORWARD->value
        ]);

        $stops = Stop::factory()->count(3)->create();

        $updateData = [
            'direction' => DirectionType::BACKWARD->value,
            'stops' => $stops->pluck('id')->toArray()
        ];

        $response = $this->putJson("/api/route-directions/{$routeDirection->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Маршрут успешно обновлен'
            ]);

        $this->assertDatabaseHas('route_directions', [
            'id' => $routeDirection->id,
            'direction' => DirectionType::BACKWARD->value
        ]);

        foreach ($stops as $index => $stop) {
            $this->assertDatabaseHas('route_stops', [
                'route_direction_id' => $routeDirection->id,
                'stop_id' => $stop->id,
                'stop_order' => $index + 1
            ]);
        }
    }

    public function test_validation_fails_with_invalid_direction(): void
    {
        $route = Route::factory()->create();
        $routeDirection = RouteDirection::factory()->create([
            'route_id' => $route->id
        ]);

        $response = $this->putJson("/api/route-directions/{$routeDirection->id}", [
            'direction' => 'НЕВЕРНОЕ_НАПРАВЛЕНИЕ'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['direction']);
    }

    public function test_validation_fails_with_nonexistent_stops(): void
    {
        $route = Route::factory()->create();
        $routeDirection = RouteDirection::factory()->create([
            'route_id' => $route->id
        ]);

        $response = $this->putJson("/api/route-directions/{$routeDirection->id}", [
            'stops' => [99999, 99998]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['stops.0', 'stops.1']);
    }
}
