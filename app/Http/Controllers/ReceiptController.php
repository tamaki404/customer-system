<?php
namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Product;
use App\Traits\ImageHandler;


class ReceiptController extends Controller{
    use ImageHandler;
    // private function updateReceiptStatus($receipt_id, $status, $message)
    // {
    //     $receipt = Receipt::findOrFail(id: $receipt_id);
    //     $receipt->status = $status;
    //     $receipt->verified_by = auth()->user()->name;
    //     $receipt->verified_at = now();
    //     $receipt->save();

    //     return redirect()
    //         ->route('receipts.view', $receipt_id)
    //         ->with('success', $message);
    // }

    // public function verifyReceipt($receipt_id)
    // {
        
    //     return $this->updateReceiptStatus($receipt_id, 'Verified', 'Receipt verified successfully!');
    // }

    // public function cancelReceipt($receipt_id)
    // {
    //     return $this->updateReceiptStatus($receipt_id, 'Cancelled', 'Receipt cancelled successfully!');
    // }

    // public function rejectReceipt($receipt_id)
    // {
    //     return $this->updateReceiptStatus($receipt_id, 'Rejected', 'Receipt rejected successfully!');
    // }

// UPDATED PHP CONTROLLER METHOD
public function fileReceipt($po_number, Request $request) 
{
    $validated = $request->validate([
        'receipt_id'             => 'required|integer|exists:receipts,receipt_id',
        'status'         => 'required|string|in:Verified,Rejected',
        'action_by'      => 'nullable|string',
        'action_at'      => 'nullable|string',
        'additional_note'=> 'nullable|string|max:255',
        'rejected_note'  => 'nullable|string|max:255',
    ]);

    $po = PurchaseOrder::where('po_number', $po_number)->firstOrFail();
    $receipt = Receipt::where('receipt_id', $validated['receipt_id'])
        ->where('po_number', $po_number)
        ->firstOrFail();

    $status = $validated['status'];
    $user   = $validated['action_by'];

    $receiptUpdateData = [
        'status'          => $status,
        'action_at'       => now(),
        'action_by'       => $user,
        'additional_note' => $validated['additional_note'] ?? null,
        'rejected_note'   => $status === 'Rejected' 
                                ? ($validated['rejected_note'] ?? null)
                                : null,
    ];

    $receipt->update($receiptUpdateData);

    $this->updatePurchaseOrderPaymentStatus($po);

    $message = $status === 'Verified' 
        ? 'Receipt verified and PO payment status updated successfully!' 
        : 'Receipt rejected and PO payment status recalculated successfully!';

    return redirect()->back()->with('success', $message);
}

public function cancelReceipt($receipt_id, Request $request) 
{
    $receipt = Receipt::where('receipt_id', $receipt_id)->firstOrFail();
    $validated = $request->validate([
        'status' => 'required|string',
    ]);
    $status = $validated['status'];
    $receipt->update([
        'status' => $status,
    ]);
    $message = 'Receipt has been cancelled successfully.';
    return redirect()->back()->with('success', $message);
}


private function updatePurchaseOrderPaymentStatus($po)
{
    $totalPaid = Receipt::where('po_number', $po->po_number)
        ->where('status', 'Verified')
        ->sum('total_amount');

    if ($totalPaid == 0) {
        $po->payment_status = 'Processing';
    } elseif ($totalPaid < $po->grand_total) {
        $po->payment_status = 'Partially Settled';
    } elseif ($totalPaid == $po->grand_total) {
        $po->payment_status = 'Fully Paid';
    } elseif ($totalPaid > $po->grand_total) {
        $po->payment_status = 'Overpaid';
    }

    $po->save();
    
    return $po->payment_status;
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
        $user = auth()->user();
        if (!in_array($user->user_type, ['Admin', 'Staff']) && $receipt->id !== $user->id) {
            abort(403, 'Unauthorized access');
        }
        return view('receipts_view', compact('receipt'));
    }

    public function showUserReceipts(Request $request)
    {
        $user = auth()->user();
        $query = Receipt::with('customer');

        $from_date = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to_date   = $request->input('to_date', now()->endOfMonth()->format('Y-m-d'));

        $status = $request->input('status');
        if ($status && in_array($status, ['Pending', 'Verified', 'Cancelled', 'Rejected'])) {
            $query->where('status', $status);
        }

        if ($from_date && $to_date) {
            $dateColumn = ($status && in_array($status, ['Verified', 'Cancelled', 'Rejected']))
                ? 'verified_at'
                : 'created_at';

            $query->whereBetween($dateColumn, [
                Carbon::parse($from_date)->startOfDay(),
                Carbon::parse($to_date)->endOfDay()
            ]);
        }

        $search = $request->input('search', '');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%$search%")
                ->orWhere('store_name', 'like', "%$search%")
                ->orWhere('total_amount', 'like', "%$search%")
                ->orWhere('purchase_date', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%");
            });
        }

        if ($user->user_type === 'Customer') {
            $query->where('id', $user->id);
        }

        $receipts = $query->orderBy('created_at', 'desc')->paginate(50);

        $receipts->appends([
            'from_date' => $from_date,
            'to_date'   => $to_date,
            'search'    => $search,
            'status'    => $status,
        ]);

        return view('receipts', compact('receipts', 'user', 'from_date', 'to_date', 'search', 'status'));
    }



    public function checkPONumber(Request $request)
    {
        $po = \DB::table('purchase_orders')
            ->where('po_number', $request->po_number)
            ->where('user_id', auth()->id())
            ->first();

        if ($po) {
            $paidAmount = \DB::table('receipts')
                ->where('po_number', $po->po_number)
                ->where('status', 'Verified')
                ->sum('total_amount');

            $balance = max($po->grand_total - $paidAmount, 0);

            if ($paidAmount == 0) {
                $status = 'Unpaid';
            } elseif ($paidAmount < $po->grand_total) {
                $status = 'Partially Paid';
            } elseif ($paidAmount == $po->grand_total) {
                $status = 'Fully Paid';
            } else {
                $status = 'Overpaid';
            }

            // ✅ Can submit only if not fully paid or overpaid
            $canSubmit = in_array($status, ['Unpaid', 'Partially Paid', 'Processing']);

            return response()->json([
                'valid'       => true,
                'grand_total' => $po->grand_total,
                'balance'     => $balance,
                'status'      => $status,
                'can_submit'  => $canSubmit,
            ]);
        }

        return response()->json([
            'valid'   => false,
            'message' => 'This P.O number does not exist in your records.',
        ]);
    }



public function submitReceipt(Request $request)
{
    $validated = $request->validate([
        'receipt_image'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        'purchase_date'   => 'required|date',
        'store_name'      => 'required|string|max:255',
        'total_amount'    => 'required|numeric',
        'invoice_number'  => 'nullable|string|max:255',
        'notes'           => 'nullable|string',
        'receipt_number'  => 'required|string',
        'po_number'       => 'required|string|max:255',
    ]);

    if ($request->hasFile('receipt_image')) {
        $imageFile = $request->file('receipt_image');
        [$base64Image, $mimeType] = $this->convertImageToBase64($imageFile);
        $validated['receipt_image'] = $base64Image;
        $validated['receipt_image_mime'] = $mimeType;
    }

    $request->validate([
        'po_number' => [
            'required',
            function ($attribute, $value, $fail) {
                $exists = \DB::table('purchase_orders')
                    ->where('po_number', $value)
                    ->where('user_id', auth()->id())
                    ->exists();

                if (! $exists) {
                    $fail('This P.O number does not exist in your records.');
                }
            },
        ],
    ]);

    // ✅ Only create the receipt, don’t update PO payment_status here
    $validated['id'] = Auth::id();  
    $validated['status'] = 'Pending';
    unset($validated['verified_by'], $validated['verified_at']);

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

        // Status filter from tabs
        $status = $request->input('status');
        if ($status && in_array($status, ['Pending', 'Verified', 'Cancelled', 'Rejected'])) {
            $query->where('status', $status);
        }

        // Date range filter based on context (verified_at for finalized states)
        if ($from && $to) {
            $dateColumn = ($status && in_array($status, ['Verified', 'Cancelled', 'Rejected'])) ? 'verified_at' : 'created_at';
            $query->whereBetween($dateColumn, [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
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
            'search' => $search,
            'status' => $status
        ]);
        
        return view('receipts', [
            'receipts' => $receipts,
            'user' => $user,
            'from_date' => $from,
            'to_date' => $to,
            'search' => $search,
            'status' => $status
        ]);
    }



}