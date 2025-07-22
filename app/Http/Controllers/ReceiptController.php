<?php
namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller{

    public function viewReceipt($receipt_id)
    {
        $receipt = \App\Models\Receipt::with('customer')->findOrFail($receipt_id);
        return view('receipts_view', compact('receipt'));
    }

    public function showUserReceipts()
    {
        $user = auth()->user();
        if ($user->user_type === 'Staff') {
            $receipts = \App\Models\Receipt::all();
        } else {
            $receipts = \App\Models\Receipt::where('customer_id', $user->id)->get();
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
            'receipt_number' => 'required|numeric',
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
}