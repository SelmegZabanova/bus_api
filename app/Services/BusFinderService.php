<?php
namespace App\Services;

use App\Models\RouteStop;
use App\Models\Stop;
use App\Models\RouteDirection;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BusFinderService
{
    public function findBuses(Stop $fromStop, Stop $toStop): array
    {
        // Получаем все направления маршрутов, проходящие через обе остановки
        $routeDirections = $this->findRouteDirections($fromStop, $toStop);

        $result = [];

        foreach ($routeDirections as $direction) {
            // Получаем RouteStop для начальной остановки
            $fromRouteStop = $direction->routeStops()
                ->where('stop_id', $fromStop->id)
                ->first();

            if (!$fromRouteStop) {
                continue;
            }

            // Получаем ближайшие времена прибытия
            $nextArrivals = $fromRouteStop->schedules()
                ->where('arrival_time', '>', Carbon::now()->format('H:i:s'))
                ->orderBy('arrival_time')
                ->limit(3)
                ->pluck('arrival_time')
                ->map(function ($time) {
                    return Carbon::parse($time)->format('H:i');
                })
                ->toArray();

            if (empty($nextArrivals)) {
                continue;
            }

            $lastStop = $direction->routeStops() //получаем последнюю остановку маршрута
                ->orderBy('stop_order', 'desc')
                ->first();

            $result[] = [
                'route' => sprintf(
                    'Автобус №%s в сторону ост. %s',
                    $direction->route->name,
                    $lastStop->stop->name
                ),
                'next_arrivals' => $nextArrivals
            ];
        }

        return $result;
    }

    private function findRouteDirections(Stop $fromStop, Stop $toStop): Collection
    {
        return RouteDirection::query()->whereHas('routeStops', function ($query) use ($fromStop, $toStop) {
            $query->where('stop_id', $fromStop->id)
                ->whereExists(function ($subQuery) use ($toStop) {
                    $subQuery->selectRaw(1)
                        ->from('route_stops as rs2')
                        ->whereColumn('rs2.route_direction_id', 'route_stops.route_direction_id')
                        ->where('rs2.stop_id', $toStop->id)
                        ->whereColumn('rs2.stop_order', '>', 'route_stops.stop_order');
                });
        })->with(['route', 'routeStops' => function ($query) {
            $query->orderBy('stop_order', 'desc');
        },
            'routeStops.stop'])->get();
    }

}
