<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reports/comprehensive",
     *     tags={"Reports"},
     *     summary="Get comprehensive report",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="from", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="branch", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Comprehensive report data")
     * )
     */
    public function comprehensive(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/dashboard-stats",
     *     tags={"Reports"},
     *     summary="Get dashboard statistics",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Dashboard statistics")
     * )
     */
    public function dashboardStats(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
