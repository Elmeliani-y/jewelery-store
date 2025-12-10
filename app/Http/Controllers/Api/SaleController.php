<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class SaleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/sales",
     *     tags={"Sales"},
     *     summary="Get all sales",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="List of sales")
     * )
     */
    public function index(Request $request)
    {
        $sales = Sale::with(['branch', 'employee', 'caliber'])
            ->notReturned()
            ->latest()
            ->paginate(15);

        return response()->json($sales);
    }

    /**
     * @OA\Post(
     *     path="/api/sales",
     *     tags={"Sales"},
     *     summary="Create a new sale",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"branch_id","employee_id","products","payment_method"},
     *             @OA\Property(property="branch_id", type="integer"),
     *             @OA\Property(property="employee_id", type="integer"),
     *             @OA\Property(property="payment_method", type="string", enum={"cash", "network", "mixed"}),
     *             @OA\Property(property="cash_amount", type="number", format="float"),
     *             @OA\Property(property="network_amount", type="number", format="float"),
     *             @OA\Property(property="products", type="array", @OA\Items(
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="caliber_id", type="integer"),
     *                 @OA\Property(property="weight", type="number"),
     *                 @OA\Property(property="amount", type="number")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Sale created successfully")
     * )
     */
    public function store(Request $request)
    {
        // Implementation would go here
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * @OA\Get(
     *     path="/api/sales/{id}",
     *     tags={"Sales"},
     *     summary="Get a specific sale",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Sale details"),
     *     @OA\Response(response=404, description="Sale not found")
     * )
     */
    public function show(Sale $sale)
    {
        return response()->json($sale->load(['branch', 'employee', 'caliber']));
    }

    /**
     * @OA\Post(
     *     path="/api/sales/{id}/return",
     *     tags={"Sales"},
     *     summary="Return a sale",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Sale returned successfully")
     * )
     */
    public function returnSale(Sale $sale)
    {
        $sale->returnSale();
        return response()->json(['message' => 'Sale returned successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/sales/search/invoice",
     *     tags={"Sales"},
     *     summary="Search sales by invoice number",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Search results")
     * )
     */
    public function searchByInvoice(Request $request)
    {
        $query = $request->input('query');
        $sales = Sale::where('invoice_number', 'like', "%{$query}%")
            ->with(['branch', 'employee'])
            ->limit(10)
            ->get();

        return response()->json($sales);
    }
}
