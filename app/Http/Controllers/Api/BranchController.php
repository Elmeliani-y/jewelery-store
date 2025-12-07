<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class BranchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/branches",
     *     tags={"Branches"},
     *     summary="Get all branches",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of branches")
     * )
     */
    public function index()
    {
        return response()->json(Branch::active()->get());
    }

    /**
     * @OA\Get(
     *     path="/api/branches/{id}",
     *     tags={"Branches"},
     *     summary="Get a specific branch",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Branch details")
     * )
     */
    public function show(Branch $branch)
    {
        return response()->json($branch);
    }

    /**
     * @OA\Post(
     *     path="/api/branches/{id}/toggle-status",
     *     tags={"Branches"},
     *     summary="Toggle branch status",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Status toggled successfully")
     * )
     */
    public function toggleStatus(Branch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);
        return response()->json($branch);
    }
}
