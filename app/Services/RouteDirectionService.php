<?php

namespace App\Services;

use App\Models\RouteDirection;
use App\Models\RouteStop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RouteDirectionService
{
    public function updateDirection(RouteDirection $routeDirection, array $data): ?RouteDirection
    {
        try {
            DB::beginTransaction();

            // Обновляем направление если оно изменилось
            if (isset($data['direction'])) {
                $routeDirection->update([
                    'direction' => $data['direction']
                ]);
            }

            // Если переданы остановки, обновляем их
            if (isset($data['stops'])) {
                // Удаляем старые остановки
                $routeDirection->routeStops()->delete();

                // Создаем новые остановки с правильным порядком
               $this->updateStops($routeDirection, $data['stops']);
            }

            DB::commit();
            return $routeDirection;

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Ошибка при обновлении направления маршрута', ['exception' => $exception]);

            return null;
        }
    }
    public function updateStops(RouteDirection $routeDirection, array $stops): void
    {
        $routeStops = [];
        foreach ($stops as $index => $stopId) {
            $routeStops[] = [
                'stop_id' => $stopId,
                'stop_order' => $index + 1
            ];
        }
        $routeDirection->routeStops()->createMany($routeStops);
    }
}
