<?php
namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{




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
            'id' => 'nullable|integer',
        ]);

        // Handle image upload
        if ($request->hasFile('receipt_image')) {
            $imageName = time() . '.' . $request->file('receipt_image')->extension();
            $request->file('receipt_image')->move(public_path('images'), $imageName);
            $validated['receipt_image'] = $imageName;
        }

        // Attach customer_id if user is logged in
        if (Auth::check()) {
            $validated['id'] = Auth::id();
        }

        // Save receipt
        \App\Models\Receipt::create($validated);

        return redirect()->back()->with('success', 'Receipt submitted successfully!');
    }
}