<?php
namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Product;
use App\Traits\ImageHandler;


class ReceiptController extends Controller{
    use ImageHandler;
    public function verifyReceipt($receipt_id)
    {
        $receipt = Receipt::findOrFail($receipt_id);
        $receipt->status = 'Verified';
        $receipt->verified_by = auth()->user()->name;
        $receipt->verified_at = now();
        $receipt->save();
        return redirect()->route('receipts.view', $receipt_id)->with('success', 'Receipt verified successfully!');
    }

    public function cancelReceipt($receipt_id)
    {
        $receipt = Receipt::findOrFail($receipt_id);
        $receipt->status = 'Cancelled';
        $receipt->verified_by = auth()->user()->name; 
        $receipt->verified_at = now();
        $receipt->save();
        return redirect()->route('receipts.view', $receipt_id)->with('success', 'Receipt cancelled successfully!');
    }

    public function rejectReceipt($receipt_id)
    {
        $receipt = Receipt::findOrFail($receipt_id);
        $receipt->status = 'Rejected';
        $receipt->verified_by = auth()->user()->name;
        $receipt->verified_at = now();
        $receipt->save();
        return redirect()->route('receipts.view', $receipt_id)->with('success', 'Receipt rejected successfully!');
    }




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

    public function showUserReceipts(Request $request)
    {
        $user = auth()->user();
        $query = Receipt::with('customer');
        
        // apply date range filter if provided
        $from_date = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to_date = $request->input('to_date', now()->endOfMonth()->format('Y-m-d'));
        
        if ($from_date && $to_date) {
            $query->whereBetween('created_at', [Carbon::parse($from_date)->startOfDay(), Carbon::parse($to_date)->endOfDay()]);
        }
        
        // apply search filter if provided
        $search = $request->input('search', '');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%$search%")
                  ->orWhere('store_name', 'like', "%$search%")
                  ->orWhere('total_amount', 'like', "%$search%")
                  ->orWhere('purchase_date', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%");
            });
        }
        
        if ($user->user_type === 'Staff' || $user->user_type === 'Admin') {
            $receipts = $query->orderBy('created_at', 'desc')->paginate(50);
        } else {
            $receipts = $query->where('id', $user->id)->orderBy('created_at', 'desc')->paginate(50);
        }
        
        // append query parameters to pagination links
        $receipts->appends([
            'from_date' => $from_date,
            'to_date' => $to_date,
            'search' => $search
        ]);
        
        return view('receipts', compact('receipts', 'user', 'from_date', 'to_date', 'search'));
    }



    public function submitReceipt(Request $request)
    {
        $validated = $request->validate([
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'purchase_date' => 'required|date',
            'store_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string',
            'receipt_number' => 'required|string',
            'id' => 'nullable|string',
        ]);

        if ($request->hasFile('receipt_image')) {
            $imageFile = $request->file('receipt_image');
            [$base64Image, $mimeType] = $this->convertImageToBase64($imageFile);
            $validated['receipt_image'] = $base64Image;
            $validated['receipt_image_mime'] = $mimeType;
        }

        if (Auth::check()) {
            $validated['id'] = Auth::id();
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


    public function dateSearch(Request $request)
    {
        $user = auth()->user();
        $query = Receipt::with('customer');

        $from = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to_date', now()->endOfMonth()->format('Y-m-d'));

        if ($from && $to) {
            $query->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
        }

        // Search filter
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%$search%")
                  ->orWhere('store_name', 'like', "%$search%")
                  ->orWhere('total_amount', 'like', "%$search%")
                  ->orWhere('purchase_date', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%");

            });
        }

        if ($user->user_type === 'Staff' || $user->user_type === 'Admin') {
            $receipts = $query->orderBy('created_at', 'desc')->paginate(50);
        } else {
            $receipts = $query->where('id', $user->id)->orderBy('created_at', 'desc')->paginate(50);
        }
        
        // append query parameters to pagination links
        $receipts->appends([
            'from_date' => $from,
            'to_date' => $to,
            'search' => $search
        ]);
        
        return view('receipts', [
            'receipts' => $receipts,
            'user' => $user,
            'from_date' => $from,
            'to_date' => $to,
            'search' => $search
        ]);
    }



}