<?php

namespace App\Services;

use App\Models\Route;
use App\Models\RouteDirection;
use App\Models\RouteStop;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class RouteService
{
    public function updateRouteStops(Route $route, array $directions): array
    {
        try {
            DB::beginTransaction();

            // Удаляем старые направления и их остановки
            $route->directions()->delete();

            //создаем новые нправления
            foreach ($directions as $directionData) {
                $direction = $route->directions()->create([
                    'direction' => $directionData['direction']
                ]);

                // Создаем остановки с правильным порядком
                $routeStops = [];
                foreach ($directionData['stops'] as $index => $stopId) {
                    $routeStops[] = [
                        'stop_id' => $stopId,
                        'stop_order' => $index + 1
                    ];
                }
                $direction->routeStops()->createMany($routeStops);
            }

            DB::commit();

            // Перезагружаем маршрут со всеми связями
            $route->load(['directions.routeStops.stop']);

            return [
                'status' => 'success',
                'route' => $route
            ];
        } catch (\Exception $exception) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => 'Ошибка при обновлении маршрута: ' . $exception->getMessage()
            ];
        }
    }
}
