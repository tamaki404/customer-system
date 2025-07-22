<?php
namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'purchase_date' => 'nullable|date',
            'store_name' => 'nullable|string|max:255',
            'total_amount' => 'nullable|numeric',
            'invoice_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt_image')) {
            $imageName = time() . '_' . $request->receipt_image->getClientOriginalName();
            $request->receipt_image->move(public_path('receipts'), $imageName);
            $validated['receipt_image'] = $imageName;
        }

        $validated['customer_id'] = Auth::id();
        $validated['status'] = 'Pending';

        Receipt::create($validated);

        return redirect()->back()->with('success', 'Receipt submitted successfully.');
    }

}
