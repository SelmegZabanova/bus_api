<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Request\UpdateRouteRequest;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use App\Models\RouteDirection;
use App\Services\RouteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RouteController extends Controller
{
    private RouteService $routeService;

    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

   //получить все маршруты с их остановками
    public function index(): AnonymousResourceCollection
    {
        $routes = Route::with(['directions.routeStops.stop'])->get();

        return RouteResource::collection($routes);
    }

    //редактировать остановки маршрута
    public function update(UpdateRouteRequest $request, Route $route): JsonResponse
    {

        $result = $this->routeService->updateRouteStops(
            $route,
            $request->directions
        );

        if ($result === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка при обновлении маршрута'],
                500);
        }
        $result->load(['directions.routeStops.stop']);

        return response()->json([
            'status' => 'success',
            'message' => 'Маршрут успешно обновлен',
            'route' => new RouteResource($result)
        ]);
    }

}
