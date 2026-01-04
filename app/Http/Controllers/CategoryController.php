<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Caliber;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Removed device validation from constructor; now enforced at the start of every action.
    private function validateDeviceOrAbort()
    {
        $token = request()->cookie('device_token');
        if ($token) {
            $device = \App\Models\Device::where('token', $token)->first();
            if (! $device || ! $device->active || ! $device->user_id || ! \App\Models\User::where('id', $device->user_id)->exists()) {
                \Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                \Cookie::queue(\Cookie::forget('device_token'));
                abort(404);
            }
        }
    }
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $this->validateDeviceOrAbort();
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
        $this->validateDeviceOrAbort();
        $this->enforceDeviceOrAdminOr404(request());
        $calibers = Caliber::active()->orderBy('name')->get();
        return view('categories.create', compact('calibers'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $this->validateDeviceOrAbort();

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

        return redirect()->route('v5w9x4y1.index')
                       ->with('success', 'تم إضافة الصنف بنجاح');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $v5w9x4y1)
    {
        $this->enforceDeviceOrAdminOr404(request());
        $category = $v5w9x4y1; // Alias for clarity
        $calibers = Caliber::active()->orderBy('name')->get();
        return view('categories.edit', compact('category', 'calibers'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $v5w9x4y1)
    {
        $category = $v5w9x4y1; // Alias for clarity

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

        return redirect()->route('v5w9x4y1.index')
                       ->with('success', 'تم تحديث الصنف بنجاح');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $v5w9x4y1)
    {
        $this->enforceDeviceOrAdminOr404(request());
        $category = $v5w9x4y1; // Alias for clarity
        $category->delete();

        return redirect()->route('v5w9x4y1.index')
                       ->with('success', 'تم حذف الصنف بنجاح');
    }
}
