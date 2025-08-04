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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = file_get_contents($image->getRealPath());
            $validated['image'] = $imageData;
            $validated['image_mime'] = $image->getMimeType();
        }

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
