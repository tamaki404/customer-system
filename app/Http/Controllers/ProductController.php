<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;
use App\Models\Logs;
use App\Models\ProductSetting;
use App\Models\Address;

class ProductController extends Controller
{

        public function productList(Request $request)
        {
            $user = Auth::user();
            $supplier = Suppliers::where('user_id', $user->user_id)->first(); 

            $products = Products::where('status', 'Listed')->get(); 
            $setProducts = $supplier 
                ? ProductSetting::where('supplier_id', $supplier->supplier_id)->get() 
                : collect(); 
            $cities = Address::selectRaw('LOWER(office_city) as city')
                ->distinct()
                ->pluck('city');

            // Supplier counts per city
            $supplierCounts = Address::selectRaw('LOWER(office_city) as city, COUNT(DISTINCT supplier_id) as count')
                ->groupBy('city')
                ->pluck('count', 'city');

            return view('products.list', [
                'user' => $user,
                'products' => $products,
                'setProducts' => $setProducts,
                'cities' => $cities,
                'supplierCounts' => $supplierCounts,

            ]);
        }

        public function productView($product_id, Request $request)
        {
            $user = Auth::user();
            $product = Products::where('product_id', $product_id)->first(); 

            $products = Products::all(); 

            return view('products.product', [
                'user' => $user,
                'products' => $products,
                'product' => $product,


            ]);
        }
        public function updateParent(Request $request)
        {
            $data = $request->validate([
                'product_id' => 'required|string|exists:products,product_id',
                'parent_product_id' => 'nullable|string|exists:products,product_id',
            ]);

            $product = Products::where('product_id', $data['product_id'])->firstOrFail();
            $product->parent_product_id = $data['parent_product_id'] ?? null;
            $product->save();

            return back()->with('success', 'Product parent updated.');
        }
        public function addProduct(Request $request) {
            \Log::info('Request data:', $request->all());
            
            try {
                $validated = $request->validate([
                    'product_id' => 'required|string|max:50|unique:products,product_id',
                    'parent_product_id' => 'nullable|string|exists:products,product_id',
                    'name'       => 'required|string|max:255',
                    'base_price'        => 'required|numeric|min:0',
                    'category'   => 'nullable|string|max:100',
                    'category_id'=> 'nullable|string|exists:categories,category_id',
                    'unit'       => 'required|string|max:50',
                    'weight'     => 'nullable|string|max:50',
                    'status'     => 'required|string|in:Listed,Unlisted',
                ]);
                
                \Log::info('Validated data:', $validated);
                
                $validated['added_by'] = auth()->user()->user_id;
                if (!empty($validated['category_id']) && empty($validated['category'])) {
                    $cat = \App\Models\Category::where('category_id', $validated['category_id'])->first();
                    if ($cat) {
                        $validated['category'] = $cat->name; // keep legacy text for now
                    }
                }
                
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

        public function filter(Request $request)
        {
            $query = Products::where('status', 'Listed');

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }
            if ($request->filled('unit')) {
                $query->where('unit', $request->unit);
            }
            if ($request->filled('weight')) {
                $query->where('weight', $request->weight);
            }

            $products = $query->get();

            return view('customers.partials.filter_results', compact('products'));
        }

        public function info($product_id)
        {
            $product = Products::where('product_id', $product_id)->firstOrFail();
            return response()->json([
                'product_id' => $product->product_id,
                'name' => $product->name,
                'category' => $product->category,
                'unit' => $product->unit,
                'weight' => $product->weight,
            ]);
        }



}
