<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Sale;
use Illuminate\Http\Request;

class BranchSalesController extends Controller
{
    public function index(Request $request)
    {
        $this->enforceDeviceToken($request);
        // Filter by date range and branch if provided
        $branches = Branch::all();
        $branchId = $request->get('branch_id');
        // Default 'from' to first day of current month if not set
        $from = $request->get('from') ?: now()->startOfMonth()->format('Y-m-d');
        // Default 'to' to today if not set
        $to = $request->get('to') ?: now()->format('Y-m-d');

        $query = Sale::query()->where('is_returned', false);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($from && $to) {
            $query->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);
        }
        $sales = $query->with('branch')->get();

        // If no branch selected, show all branches (even those with no sales)
        $totals = [];
        if (! $branchId) {
            foreach ($branches as $branch) {
                $branchSales = $sales->where('branch_id', $branch->id);
                $totalAmount = $branchSales->sum('total_amount');
                $totalWeight = $branchSales->sum(function ($sale) {
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
                $totalWeight = $branchSales->sum(function ($sale) {
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
        // Calculate sales per caliber for the filtered sales
        $calibers = \App\Models\Caliber::active()->get();
        // For each branch, calculate sales per caliber
        $branchCaliberSales = [];
        foreach ($totals as $branchId => $data) {
            $branchSales = $sales->where('branch_id', $branchId);
            $perCaliber = [];
            foreach ($calibers as $caliber) {
                $caliberTotal = 0;
                $caliberWeight = 0;
                foreach ($branchSales as $sale) {
                    $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                    if ($products) {
                        foreach ($products as $product) {
                            if (isset($product['caliber_id']) && $product['caliber_id'] == $caliber->id) {
                                $caliberTotal += isset($product['amount']) ? (float) $product['amount'] : 0;
                                $caliberWeight += isset($product['weight']) ? (float) $product['weight'] : 0;
                            }
                        }
                    }
                }
                if ($caliberTotal > 0 || $caliberWeight > 0) {
                    $perCaliber[] = [
                        'id' => $caliber->id,
                        'name' => $caliber->name,
                        'total' => $caliberTotal,
                        'weight' => $caliberWeight,
                        'price_per_gram' => $caliberWeight > 0 ? $caliberTotal / $caliberWeight : 0,
                    ];
                }
            }
            $branchCaliberSales[$branchId] = $perCaliber;
        }

        return view('branches.sales-summary', [
            'branches' => $branches,
            'totals' => $totals,
            'selectedBranch' => $branchId,
            'from' => $from,
            'to' => $to,
            'branchCaliberSales' => $branchCaliberSales,
        ]);
    }
}
