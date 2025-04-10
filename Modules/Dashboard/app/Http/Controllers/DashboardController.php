<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Dashboard\Services\DashboardService;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' =>$this->dashboardService->getDashboard(),
            'message' => 'Dashboard retrieved successfully'
        ]);
    }
}
