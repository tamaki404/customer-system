<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ImageHandler;

class ProductController extends Controller
{
    use ImageHandler;
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'product_id' => 'nullable|string|max:255',
            'unit'=> 'nullable|string|max:25'
        ]);

        $validated['status'] = $validated['status'] ?? 'Available';

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            [$base64, $mime] = $this->convertImageToBase64($request->file('image'));
            $validated['image'] = $base64;
            $validated['image_mime'] = $mime;
        }

        if ($validated['quantity'] === '0') {
            $validated['status'] = "No stock";
        } elseif ($validated['quantity'] < 20) {
            $validated['status'] = "Low stock";
        } else {
            $validated['status'] = "Available";
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

    
    public function unlistProduct($product_id)
    {
        $product = Product::findOrFail($product_id);
        $product->status = 'Unlisted';
        $product->save();
        return redirect()->route('product_view.view', $product_id)->with('success', 'Product unlisted successfully!');
    }

public function addStock($product_id)
{
    $validated = request()->validate([
        'addedStock' => 'required|integer|min:1|max:999'
    ]);

    $product = Product::findOrFail($product_id);
    $product->quantity += $validated['addedStock'];

    if ($product->quantity === 0) {
        $product->status = "No stock";
    } elseif ($product->quantity < 20) {
        $product->status = "Low stock";
    } else {
        $product->status = "Available";
    }

    $product->save();

    return redirect()->route('product_view.view', $product_id)
                     ->with('success', 'Stock added successfully!');
}



    public function productView($id)
    {
        $product = Product::findOrFail($id);

        $soldQuantity = \DB::table('orders')
            ->where('product_id', $product->id)
            ->where('status', 'Completed')
            ->sum('quantity');

        return view('product_view', compact('product', 'soldQuantity'));
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

        return redirect()->route('store', $product_id)->with('success', 'Product deleted successfully!');
    }

}
