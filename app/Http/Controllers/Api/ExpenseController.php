<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ExpenseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/expenses",
     *     tags={"Expenses"},
     *     summary="Get all expenses",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="List of expenses")
     * )
     */
    public function index(Request $request)
    {
        $expenses = Expense::with(['branch', 'expenseType'])
            ->latest('expense_date')
            ->paginate(15);

        return response()->json($expenses);
    }

    /**
     * @OA\Post(
     *     path="/api/expenses",
     *     tags={"Expenses"},
     *     summary="Create a new expense",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"branch_id","expense_type_id","amount","expense_date"},
     *             @OA\Property(property="branch_id", type="integer"),
     *             @OA\Property(property="expense_type_id", type="integer"),
     *             @OA\Property(property="amount", type="number", format="float"),
     *             @OA\Property(property="expense_date", type="string", format="date"),
     *             @OA\Property(property="notes", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Expense created successfully")
     * )
     */
    public function store(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * @OA\Get(
     *     path="/api/expenses/{id}",
     *     tags={"Expenses"},
     *     summary="Get a specific expense",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Expense details"),
     *     @OA\Response(response=404, description="Expense not found")
     * )
     */
    public function show(Expense $expense)
    {
        return response()->json($expense->load(['branch', 'expenseType']));
    }
}
