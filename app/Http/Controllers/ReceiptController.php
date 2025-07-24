<?php
namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class ReceiptController extends Controller{



    public function index(Request $request)
    {
       $user = auth()->user();
       $query = Receipt::with('customer');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('purchase_date', [$from, $to]);
        }

        $receipts = $query->orderBy('created_at', 'desc')->get();

        return view('receipts', compact('receipts', 'user'));
    }

    public function viewReceipt($receipt_id)
    {
        $receipt = Receipt::with('customer')->findOrFail($receipt_id);
        return view('receipts_view', compact('receipt'));
    }

    public function showUserReceipts()
    {
        $user = auth()->user();
        if ($user->user_type === 'Staff') {
            $receipts = Receipt::all();
        } else {
            $receipts = Receipt::where('customer_id', $user->id)->get();
        }
        return view('receipts', compact('receipts', 'user'));
    }



    public function submitReceipt(Request $request)
    {
        $validated = $request->validate([
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'purchase_date' => 'required|date',
            'store_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric',
            'invoice_number' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string',
            'receipt_number' => 'required|string',
            'customer_id' => 'nullable|integer',
        ]);

        if ($request->hasFile('receipt_image')) {
            $imageName = time() . '.' . $request->file('receipt_image')->extension();
            $request->file('receipt_image')->move(public_path('images'), $imageName);
            $validated['receipt_image'] = $imageName;
        }

        if (Auth::check()) {
            $validated['customer_id'] = Auth::id();
        }

        Receipt::create($validated);

        return redirect()->back()->with('success', 'Receipt submitted successfully!');
    }

    public function getReceiptImage($receipt_id)
    {
               $user = auth()->user();

        $receipt = Receipt::findOrFail($receipt_id);
        return view('receipt_image', compact('receipt'));
    }


    public function date_search(Request $request){
        
    }


}