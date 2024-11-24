<?php

namespace App\Http\Controllers\Api;

use App\Http\Request\UpdateRouteDirectionRequest;
use App\Http\Resources\RouteDirectionResource;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use App\Models\RouteDirection;
use App\Services\RouteDirectionService;
use Illuminate\Http\JsonResponse;

class RouteDirectionController
{
    private RouteDirectionService $routeDirectionService;

    public function __construct(RouteDirectionService $routeDirectionService)
    {
        $this->routeDirectionService = $routeDirectionService;
    }

    public function update(UpdateRouteDirectionRequest $request, RouteDirection $routeDirection): JsonResponse
    {
        $result = $this->routeDirectionService->updateDirection(
            $routeDirection,
            $request->validated()
        );

        if ($result === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка при обновлении направления маршрута'
            ], 500);
        }
        $result->load(['routeStops.stop']);

        return response()->json([
            'status' => 'success',
            'message' => 'Маршрут успешно обновлен',
            'route' => new RouteDirectionResource($result)
        ]);
    }

}
