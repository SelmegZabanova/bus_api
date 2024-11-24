<?php

namespace App\Services;

use App\Models\Route;
use App\Models\RouteDirection;
use App\Models\RouteStop;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class RouteService
{
    private RouteDirectionService $routeDirectionService;

    public function __construct(RouteDirectionService $routeDirectionService)
    {
        $this->routeDirectionService = $routeDirectionService;
    }
    public function updateRouteStops(Route $route, array $directions): ?Route
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
                $this->routeDirectionService->updateStops($direction, $directionData['stops']);
            }

            DB::commit();
            return $route;
        } catch (\Exception $exception) {
            DB::rollBack();

            return null;
        }
    }
}
