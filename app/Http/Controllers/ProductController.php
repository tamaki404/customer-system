<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GlobalCeiling;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;
use App\Models\Logs;
use App\Models\ProductSetting;
use App\Models\Address;
use Carbon\Carbon;
use App\Models\SaleDiscount;


class ProductController extends Controller
{

        public function productList(Request $request)
        {
            $now = Carbon::now();

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
            $ceilings = GlobalCeiling::orderBy('start_date', 'desc')->get();
            $activePromos = SaleDiscount::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->limit(11)
                ->get();


            return view('products.list', [
                'user' => $user,
                'products' => $products,
                'setProducts' => $setProducts,
                'cities' => $cities,
                'ceilings' => $ceilings,

                'supplierCounts' => $supplierCounts,

                'activePromos' => $activePromos,

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
       
        public function addProduct(Request $request) {
            \Log::info('Request data:', $request->all());
            $user_id = Auth::user()->user_id;
            
            try {
                $validated = $request->validate([
                    'product_id' => 'required|string|max:255|unique:products,product_id',
                    'name' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
                    'base_price' => 'nullable|numeric|min:0|max:99999999.99', // matches decimal(10,2)
                    'category' => 'required|in:By products,Cut ups,Fillets,Dressed chickens,Uncategorized',
                    'measurement_type' => 'required|in:Heads,Kilos,Heads&Kilos',
                    'status' => 'required|string|in:Listed,Unlisted',
                ]);
                
                \Log::info('Validated data:', $validated);
                
                // Add the added_by field (required in migration)
                $validated['added_by'] = auth()->user()->user_id;
                
                \Log::info('Final data for creation:', $validated);
                
                // Create the product
                $product = Products::create($validated); 
                
                // Create log entry
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
                    'user_id' => $user_id,
                    'action' => 'Added a new product',
                    'log_id' => $log_id,
                    'description' => "Staff ($user_id) added a new product named '{$validated['name']}' with base price " . ($validated['base_price'] ?? 'N/A'),
                    'entity' => 'Products',
                    'entity_id' => $product->id,
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

        public function update(Request $request, $product_id)
        {
            $validated = $request->validate([
                'name' => 'required|string|max:100|min:3',
                'description' => 'required|string|max:255|min:5',
                'base_price' => 'nullable|numeric|min:0',
                'category' => 'required|in:By products,Cut ups,Fillets,Dressed chickens,Uncategorized',
                'measurement_type' => 'required|in:Heads,Kilos,Heads&Kilos',
                'status' => 'required|in:Listed,Unlisted',
                'updated_by' => 'required|exists:users,user_id'
            ]);

            try {
                $product = Products::where('product_id', $product_id)->first();
                if (!$product) {
                    return redirect()->back()->with('error', 'Product not found.');
                }
                $product->update($validated);

                return redirect()->back()->with('success', 'Product updated successfully!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to update product: ' . $e->getMessage());
            }
        }
    }