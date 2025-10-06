<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function manage()
    {
        return view('categories.manage');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|string|exists:categories,category_id',
        ]);

        $slug = Str::slug($validated['name']);
        $categoryId = strtoupper('CAT-' . substr(hash('crc32b', $slug . microtime(true)), 0, 8));

        Category::create([
            'category_id' => $categoryId,
            'name' => $validated['name'],
            'slug' => $slug,
            'status' => 'Active',
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Category saved.');
    }
    public function tree()
    {
        $roots = Category::whereNull('parent_id')->orderBy('name')->get();
        $data = $roots->map(function ($root) {
            return [
                'category_id' => $root->category_id,
                'name' => $root->name,
                'children' => $root->children()->orderBy('name')->get(['category_id','name','parent_id'])->toArray(),
            ];
        });

        return response()->json($data);
    }

    public function children($parentId)
    {
        $children = Category::where('parent_id', $parentId)
            ->orderBy('name')
            ->get(['category_id','name','parent_id']);
        return response()->json($children);
    }

    // Product-driven hierarchy endpoints
    public function productTree()
    {
        $roots = \App\Models\Products::whereNull('parent_product_id')->orderBy('name')->get();
        $data = $roots->map(function ($root) {
            return [
                'product_id' => $root->product_id,
                'name' => $root->name,
                'children' => $root->children()->orderBy('name')->get(['product_id','name','parent_product_id'])->toArray(),
            ];
        });
        return response()->json($data);
    }

    public function productChildren($parentProductId)
    {
        $children = \App\Models\Products::where('parent_product_id', $parentProductId)
            ->orderBy('name')
            ->get(['product_id','name','parent_product_id']);
        return response()->json($children);
    }
}


