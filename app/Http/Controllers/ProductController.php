<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;

class ProductController extends Controller
{
        public function productList(Request $request)
        {
            $user = Auth::user();
            $products = Products::where('status', 'Listed')->get(); 
            return view('products.list', [
                'user' => $user,
                'products' => $products,
            ]);
        }

public function addProduct(Request $request) {
    // Debug: Log the incoming request
    \Log::info('Request data:', $request->all());
    
    try {
        $validated = $request->validate([
            'product_id' => 'required|string|max:50|unique:products,product_id',
            'name'       => 'required|string|max:255',
            'srp'        => 'required|numeric|min:0',
            'category'   => 'required|string|max:100',
            'unit'       => 'required|string|max:50',
            'weight'     => 'nullable|string|max:50',
            'status'     => 'required|string|in:Listed,Unlisted',
        ]);
        
        // Debug: Log validated data
        \Log::info('Validated data:', $validated);
        
        $validated['added_by'] = auth()->user()->user_id;
        
        // Debug: Log final data before creation
        \Log::info('Final data for creation:', $validated);
        
        $product = Products::create($validated); // Note: using Product not Products
        
        return redirect()->route('products.list')
            ->with('success', 'Product added successfully!');
            
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation failed:', $e->errors());
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();
    } catch (\Exception $e) {
        \Log::error('Exception in addProduct: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        
        return redirect()->back()
            ->with('error', 'An error occurred while adding the product: ' . $e->getMessage())
            ->withInput();
    }
}


}
