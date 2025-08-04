<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:255',
        ]);


        Product::create($validated);

        return redirect()->back()->with('success', 'Product added successfully!');
    }
    /**
     * Display product image stored in database
     */
    public function showImage($id)
    {
        $product = Product::findOrFail($id);
        if (!$product->image || !$product->image_mime) {
            abort(404);
        }
        return response($product->image)->header('Content-Type', $product->image_mime);
    }
}
