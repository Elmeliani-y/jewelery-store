<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Caliber;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Check if the current device is trusted (token exists in DB for user).
     * Redirects to pairing page if not trusted.
     */
    private function enforceDeviceToken(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin()) {
            $deviceToken = $request->cookie('device_token');
            if (!$deviceToken || !\App\Models\Device::where('token', $deviceToken)->where('user_id', $user->id)->exists()) {
                return redirect()->route('pair-device.form')->send();
            }
        }
    }
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $this->enforceDeviceToken(request());
        $categories = Category::with('defaultCaliber')->orderBy('name')->get();
        // Aggregate sales count per category using products array in sales
        $sales = \App\Models\Sale::notReturned()->get();
        $categorySalesCount = [];
        foreach ($categories as $category) {
            $count = 0;
            foreach ($sales as $sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                if ($products) {
                    foreach ($products as $product) {
                        if (($product['category_id'] ?? null) == $category->id) {
                            $count++;
                        }
                    }
                }
            }
            $category->sales_count = $count;
        }
        // Paginate manually since we used get()
        $perPage = 15;
        $page = request()->get('page', 1);
        $paged = $categories->forPage($page, $perPage);
        $categories = new \Illuminate\Pagination\LengthAwarePaginator($paged, $categories->count(), $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $this->enforceDeviceToken(request());
        $calibers = Caliber::active()->orderBy('name')->get();
        return view('categories.create', compact('calibers'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $this->enforceDeviceToken($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'default_caliber_id' => 'required|exists:calibers,id',
        ], [
            'name.required' => 'اسم الصنف مطلوب.',
            'name.unique' => 'هذا الصنف موجود بالفعل.',
            'default_caliber_id.required' => 'العيار الافتراضي مطلوب.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $category = Category::create($validated);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
                'is_active' => $category->is_active
            ]);
        }

        return redirect()->route('categories.index')
                       ->with('success', 'تم إضافة الصنف بنجاح');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $this->enforceDeviceToken(request());
        $calibers = Caliber::active()->orderBy('name')->get();
        return view('categories.edit', compact('category', 'calibers'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $this->enforceDeviceToken($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'default_caliber_id' => 'required|exists:calibers,id',
        ], [
            'name.required' => 'اسم الصنف مطلوب.',
            'name.unique' => 'هذا الصنف موجود بالفعل.',
            'default_caliber_id.required' => 'العيار الافتراضي مطلوب.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('categories.index')
                       ->with('success', 'تم تحديث الصنف بنجاح');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        $this->enforceDeviceToken(request());
        $category->delete();

        return redirect()->route('categories.index')
                       ->with('success', 'تم حذف الصنف بنجاح');
    }
}
