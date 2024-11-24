<?php

namespace Tests\Feature;

use App\Enums\DirectionType;
use App\Models\Route;
use App\Models\RouteDirection;
use App\Models\Stop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_routes(): void
    {
        // Создаем тестовые маршруты с направлениями и остановками
        Route::factory()->count(3)->create()->each(function ($route) {
            // Создаем прямое и обратное направление
            RouteDirection::factory()
                ->create([
                    'route_id' => $route->id,
                    'direction' => DirectionType::FORWARD->value
                ])
                ->routeStops()
                ->create([
                    'stop_id' => Stop::factory()->create()->id,
                    'stop_order' => 1
                ]);

            RouteDirection::factory()
                ->create([
                    'route_id' => $route->id,
                    'direction' => DirectionType::BACKWARD->value
                ])
                ->routeStops()
                ->create([
                    'stop_id' => Stop::factory()->create()->id,
                    'stop_order' => 1
                ]);
        });

        $response = $this->getJson('/api/routes');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'directions' => [
                        '*' => [
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
                    ]
                ]
            ]);
    }

    public function test_can_update_route(): void
    {
        // Arrange
        $route = Route::factory()->create();
        $stops = Stop::factory()->count(3)->create();

        $updateData = [
            'directions' => [
                [
                    'direction' => DirectionType::FORWARD->value,
                    'stops' => $stops->pluck('id')->toArray()
                ],
                [
                    'direction' => DirectionType::BACKWARD->value,
                    'stops' => $stops->reverse()->pluck('id')->toArray()
                ]
            ]
        ];

        // Act
        $response = $this->putJson("/api/routes/{$route->id}", $updateData);

        // Assert Response
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Маршрут успешно обновлен',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'route' => [
                    'id',
                    'name',
                    'directions' => [
                        '*' => [
                            'id',
                            'direction',
                            'route_stops'
                        ]
                    ]
                ]
            ]);

        // Assert Database
        $this->assertDatabaseCount('route_directions', 2);
        $this->assertDatabaseCount('route_stops', 6); // 3 остановки * 2 направления

        // Assert Direction Types
        $this->assertDatabaseHas('route_directions', [
            'route_id' => $route->id,
            'direction' => DirectionType::FORWARD->value
        ]);
        $this->assertDatabaseHas('route_directions', [
            'route_id' => $route->id,
            'direction' => DirectionType::BACKWARD->value
        ]);

        // Assert Each Stop is Created
        $forwardStops = $stops->pluck('id')->toArray();
        $backwardStops = array_reverse($forwardStops);

        foreach ($forwardStops as $index => $stopId) {
            $this->assertDatabaseHas('route_stops', [
                'stop_id' => $stopId,
                'stop_order' => $index + 1,
            ]);
        }

        foreach ($backwardStops as $index => $stopId) {
            $this->assertDatabaseHas('route_stops', [
                'stop_id' => $stopId,
                'stop_order' => $index + 1,
            ]);
        }
    }

    public function test_update_route_with_invalid_data(): void
    {
        $route = Route::factory()->create();

        $response = $this->putJson("/api/routes/{$route->id}", [
            'directions' => [] // Пустой массив направлений
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['directions']);
    }

    public function test_update_route_with_invalid_direction_type(): void
    {
        $route = Route::factory()->create();
        $stops = Stop::factory()->count(3)->create();

        $response = $this->putJson("/api/routes/{$route->id}", [
            'directions' => [
                [
                    'direction' => 'НЕВЕРНОЕ_НАПРАВЛЕНИЕ',
                    'stops' => $stops->pluck('id')->toArray()
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['directions.0.direction']);
    }
}
