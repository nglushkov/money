<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->paginate(50);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('categories.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $lastOperations = $category->operations()->with([
            'bill',
            'category',
            'currency',
            'place',
        ])->latestDate()->paginate(20);

        return view('categories.show', [
            'category' => $category,
            'lastOperations' => $lastOperations,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->operations()->count() > 0) {
            return redirect()->route('currencies.show', $category)->withErrors(['error' => 'This category is used in operations and cannot be deleted.']);
        }
        $category->delete();

        return redirect()->route('categories.index');
    }
}
