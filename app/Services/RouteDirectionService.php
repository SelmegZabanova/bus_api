<?php

namespace App\Services;

use App\Models\RouteDirection;
use App\Models\RouteStop;
use Illuminate\Support\Facades\DB;

class RouteDirectionService
{
    public function updateDirection(RouteDirection $routeDirection, array $validatedData): array
    {
        try {
            DB::beginTransaction();

            // Обновляем направление если оно изменилось
            if (isset($validatedData['direction'])) {
                $routeDirection->update([
                    'direction' => $validatedData['direction']
                ]);
            }

            // Если переданы остановки, обновляем их
            if (isset($validatedData['stops'])) {
                // Удаляем старые остановки
                $routeDirection->routeStops()->delete();

                // Создаем новые остановки с правильным порядком
                $routeStops = [];
                foreach ($validatedData['stops'] as $index => $stopId) {
                    $routeStops[] = [
                        'stop_id' => $stopId,
                        'stop_order' => $index + 1
                    ];
                }
                $routeDirection->routeStops()->createMany($routeStops);
            }

            DB::commit();
            $routeDirection->load('routeStops.stop');

            return [
                'status' => 'success',
                'route_direction' => $routeDirection
            ];
        } catch (\Exception $exception) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => 'Ошибка при обновлении направления маршрута: ' . $exception->getMessage()
            ];
        }
    }
}
