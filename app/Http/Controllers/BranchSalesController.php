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
        $from = $request->get('from');
        $to = $request->get('to');

        $query = Sale::query();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($from && $to) {
            $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }
        $sales = $query->with('branch')->get();

        // Group by branch
        $grouped = $sales->groupBy('branch_id');
        $totals = [];
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

        return view('branches.sales-summary', [
            'branches' => $branches,
            'totals' => $totals,
            'selectedBranch' => $branchId,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
