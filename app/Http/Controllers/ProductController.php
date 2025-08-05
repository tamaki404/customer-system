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

    
    public function unlistProduct($product_id)
    {
        $product = Product::findOrFail($product_id);
        $product->status = 'Unlisted';
        $product->save();
        return redirect()->route('product_view.view', $product_id)->with('success', 'Product unlisted successfully!');
    }

    public function listProduct($product_id)
    {
        $product = Product::findOrFail($product_id);
        $product->status = 'Available';
        $product->save();
        return redirect()->route('product_view.view', $product_id)->with('success', 'Product listed successfully!');
    }


    public function deleteProduct($product_id)
    {
        $product = Product::findOrFail($product_id);

        if (auth()->user()->user_type !== 'Admin') {
            abort(403, 'Unauthorized access');
        }
        $product->delete();

        return redirect()->route('ordering')->with('success', 'Product deleted successfully!');
    }

}
