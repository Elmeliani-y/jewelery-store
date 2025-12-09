<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount('sales')->orderBy('name')->paginate(15);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم الصنف مطلوب.',
            'name.unique' => 'هذا الصنف موجود بالفعل.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : ($request->input('is_active', true));

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
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم الصنف مطلوب.',
            'name.unique' => 'هذا الصنف موجود بالفعل.',
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
        if ($category->sales()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف هذا الصنف لأنه مرتبط بمبيعات');
        }

        $category->delete();

        return redirect()->route('categories.index')
                       ->with('success', 'تم حذف الصنف بنجاح');
    }
}
