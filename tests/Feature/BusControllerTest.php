<?php

namespace Tests\Feature;

use App\Enums\DirectionType;
use App\Models\Route;
use App\Models\RouteDirection;
use App\Models\Stop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_find_bus()
    {
        // Создаем тестовые данные
        $fromStop = Stop::factory()->create();
        $toStop = Stop::factory()->create();

        // Создаем маршрут с остановками
        $route = Route::factory()->create(['name' => '42']);
        $direction = RouteDirection::factory()->create([
            'route_id' => $route->id,
            'direction' => DirectionType::FORWARD->value
        ]);


        // Связываем остановки с маршрутом
        $direction->routeStops()->create([
            'stop_id' => $fromStop->id,
            'stop_order' => 1
        ]);
        $direction->routeStops()->create([
            'stop_id' => $toStop->id,
            'stop_order' => 2
        ]);

        // Выполняем запрос
        $response = $this->getJson("/api/find-bus?from={$fromStop->id}&to={$toStop->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'from',
                'to',
                'buses' => [
                    '*' => [
                        'route',
                        'next_arrivals'
                    ]
                ]
            ]);
    }

    public function test_returns_validation_error_for_invalid_stops()
    {
        $response = $this->getJson('/api/find-bus?from=999&to=998');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['from', 'to'])
            ->assertJson([
                'errors' => [
                    'from' => ['Выбранная начальная остановка не существует'],
                    'to' => ['Выбранная конечная остановка не существует']
                ]
            ]);
    }

    public function test_returns_validation_error_for_missing_parameters()
    {
        $response = $this->getJson('/api/find-bus');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['from', 'to']);
    }

    public function test_returns_validation_error_for_same_stops()
    {
        $stop = Stop::factory()->create();

        $response = $this->getJson("/api/find-bus?from={$stop->id}&to={$stop->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['to'])
            ->assertJson([
                'errors' => [
                    'to' => ['Конечная остановка должна отличаться от начальной']
                ]
            ]);
    }
}
