<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Caliber;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::with('defaultCaliber')->withCount('sales')->orderBy('name')->paginate(15);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $calibers = Caliber::active()->orderBy('name')->get();
        return view('categories.create', compact('calibers'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
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
        $calibers = Caliber::active()->orderBy('name')->get();
        return view('categories.edit', compact('category', 'calibers'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
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
        $category->delete();

        return redirect()->route('categories.index')
                       ->with('success', 'تم حذف الصنف بنجاح');
    }
}
