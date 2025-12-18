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
            $totals[$branchId] = [
                'branch' => $branchSales->first()->branch,
                'total' => $branchSales->sum('total_amount'),
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
