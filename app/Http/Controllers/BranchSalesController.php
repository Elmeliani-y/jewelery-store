<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Sale;

class BranchSalesController extends Controller
{
    public function index(Request $request)
    {
        // Filter by date range and branch if provided
        $branches = Branch::all();
        $branchId = $request->get('branch_id');
        $from = $request->get('from') ?: now()->format('Y-m-d');
        $to = $request->get('to') ?: now()->format('Y-m-d');

        $query = Sale::query();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($from && $to) {
            $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }
        $sales = $query->with('branch')->get();

        // If no branch selected, show all branches (even those with no sales)
        $totals = [];
        if (!$branchId) {
            foreach ($branches as $branch) {
                $branchSales = $sales->where('branch_id', $branch->id);
                $totalAmount = $branchSales->sum('total_amount');
                $totalWeight = $branchSales->sum(function($sale) {
                    return is_numeric($sale->weight) ? $sale->weight : 0;
                });
                $avgGram = $totalWeight > 0 ? $totalAmount / $totalWeight : 0;
                $totals[$branch->id] = [
                    'branch' => $branch,
                    'total' => $totalAmount,
                    'weight' => $totalWeight,
                    'avg_gram' => $avgGram,
                    'count' => $branchSales->count(),
                ];
            }
        } else {
            // Only selected branch
            $grouped = $sales->groupBy('branch_id');
            foreach ($grouped as $branchId => $branchSales) {
                $totalAmount = $branchSales->sum('total_amount');
                $totalWeight = $branchSales->sum(function($sale) {
                    return is_numeric($sale->weight) ? $sale->weight : 0;
                });
                $avgGram = $totalWeight > 0 ? $totalAmount / $totalWeight : 0;
                $totals[$branchId] = [
                    'branch' => $branchSales->first()->branch,
                    'total' => $totalAmount,
                    'weight' => $totalWeight,
                    'avg_gram' => $avgGram,
                    'count' => $branchSales->count(),
                ];
            }
        }

        return view('branches.sales-summary', [
            'branches' => $branches,
            'totals' => $totals,
            'selectedBranch' => $branchId,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
