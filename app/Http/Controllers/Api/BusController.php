<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusFinderService;
use Illuminate\Http\JsonResponse;
use App\Http\Request\FindBusRequest;
use App\Models\Stop;

class BusController extends Controller
{
    private BusFinderService $busFinderService;

    public function __construct(BusFinderService $busFinderService)
    {
        $this->busFinderService = $busFinderService;
    }

    public function findBus(FindBusRequest $request): JsonResponse
    {
        $fromStop = Stop::query()->findOrFail($request->from);
        $toStop = Stop::query()->findOrFail($request->to);

        $result = $this->busFinderService->findBuses($fromStop, $toStop);

        return response()->json([
            'from' => $fromStop->name,
            'to' => $toStop->name,
            'buses' => $result,
        ]);
    }

}
