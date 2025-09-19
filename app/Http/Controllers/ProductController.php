<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;
use App\Models\Logs;

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
                
                \Log::info('Validated data:', $validated);
                
                $validated['added_by'] = auth()->user()->user_id;
                
                \Log::info('Final data for creation:', $validated);
                
                $product = Products::create($validated); 
                    $date = date('Ymd');
                    function randomBase36String(int $length): string {
                        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $str = '';
                        for ($i = 0; $i < $length; $i++) {
                            $str .= $chars[random_int(0, strlen($chars) - 1)];
                        }
                        return $str;
                    }

                    $log_id = 'LOG-' . $date . '-' . randomBase36String(5);
                    Logs::create([
                        'user_id'     => Auth::user()->user_id,
                        'action'      => 'Added new product',
                        'log_id'      => $log_id,
                        'description' => 'Added new product with product_id: ' . $validated['product_id'],
                    ]);
                    
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
