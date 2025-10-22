<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\AccountStatus;
use App\Models\DeliveryRequirements;
use App\Models\User;
use App\Models\Suppliers;
use App\Models\Documents;
use App\Models\Banks;

use Illuminate\Support\Facades\Auth;

class ErrorController extends Controller
{
public function declined(Request $request)
{
    $user_id = session('user_id'); 
    $supplier = Suppliers::where('user_id', $user_id)->first();
    $accStats = AccountStatus::where('user_id', $user_id)->first();

    // Initialize ALL variables to avoid "undefined variable" errors
    $banks = null;
    $documents = collect();
    $deliveryRequirements = collect();

    $reason = $accStats->to_change ?? '';

    if ($accStats) {
        switch ($accStats->to_change) {
            case 'ID image and details':
                $documents = Documents::where('user_id', $user_id)
                    ->whereIn('type', ['valid_one', 'valid_two'])
                    ->orderByRaw("FIELD(type, 'valid_one', 'valid_two')") 
                    ->limit(2)
                    ->get();
                break;

            case 'Bank details':
                $banks = Banks::where('user_id', $user_id)->first();
                break;

            case 'Necessary documents':
                // Get only valid_one and valid_two, max 2 documents
                $documents = Documents::where('user_id', $user_id)
                    ->whereIn('type', ['valid_one', 'valid_two'])
                    ->orderByRaw("FIELD(type, 'valid_one', 'valid_two')") 
                    ->limit(2)
                    ->get();
                break;

            case 'Delivery requirements':
                $deliveryRequirements = DeliveryRequirements::where('user_id', $user_id)->get();
                break;
        }
    }

    return view('error.declined', compact(
        'supplier',
        'documents',
        'banks',
        'accStats',
        'reason',
        'deliveryRequirements'
    ));
}
public function success(Request $request){
 return view('error.success');
}

public function review($user_id, Request $request){
    $user = User::where('user_id', $user_id)->first();
    $supplier = Suppliers::where('user_id', $user_id)->first();
    $accStats = AccountStatus::where('user_id', $user_id)->first();

    $banks = null;
    $documents = collect();
    $deliveryRequirements = collect();

    $reason = $accStats->to_change ?? '';

    if ($accStats) {
        switch ($accStats->to_change) {
            case 'ID image and details':
                $documents = Documents::where('user_id', $user_id)
                    ->whereIn('type', ['valid_one', 'valid_two'])
                    ->orderByRaw("FIELD(type, 'valid_one', 'valid_two')") 
                    ->limit(2)
                    ->get();
                break;

            case 'Bank details':
                $banks = Banks::where('user_id', $user_id)->first();
                break;

            case 'Necessary documents':
                $documents = Documents::where('user_id', $user_id)
                    ->whereIn('type', ['valid_one', 'valid_two'])
                    ->orderByRaw("FIELD(type, 'valid_one', 'valid_two')") 
                    ->limit(2)
                    ->get();
                break;

            case 'Delivery requirements':
                $deliveryRequirements = DeliveryRequirements::where('user_id', $user_id)->get();
                break;
        }
    }

    return view('error.review', compact(
        'supplier',
        'user',
        'documents',
        'banks',
        'accStats',
        'reason',
        'deliveryRequirements'
    ));

}

public function updateDeclined(Request $request)
{
    $user_id = session('user_id');
    
    // Validate the request
    $request->validate([
        'id_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'id_type' => 'required|string',
        'id_number' => 'required|string|max:100',
        'birthdate' => 'required|date',
        'valid_one' => 'nullable|file|mimes:pdf|max:5120',
        'valid_two' => 'nullable|file|mimes:pdf|max:5120',
    ]);

    try {
        // Update Supplier model (ID details)
        $supplier = Suppliers::where('user_id', $user_id)->first();
        
        if (!$supplier) {
            return back()->with('error', 'Supplier record not found.');
        }

        // Only update if new image is uploaded
        if ($request->hasFile('id_image')) {
            $imageFile = $request->file('id_image');
            $supplier->id_image = file_get_contents($imageFile->getRealPath());
        }

        // Update text fields (always update these as they come from the form)
        $supplier->id_type = $request->id_type;
        $supplier->id_number = $request->id_number;
        $supplier->birthdate = $request->birthdate;
        
        $supplier->save();

        // Update Documents (PDFs) - only if new files are uploaded
        $documentsUpdated = false;

        // Handle Valid ID (1)
        if ($request->hasFile('valid_one')) {
            $pdfFile = $request->file('valid_one');
            $pdfContent = file_get_contents($pdfFile->getRealPath());
            
            // Check if document exists, update or create
            $document = Documents::where('user_id', $user_id)
                ->where('type', 'valid_one')
                ->first();
            
            if ($document) {
                $document->file = $pdfContent;
                $document->save();
            } else {
                Documents::create([
                    'user_id' => $user_id,
                    'type' => 'valid_one',
                    'file' => $pdfContent,
                ]);
            }
            $documentsUpdated = true;
        }

        // Handle Valid ID (2)
        if ($request->hasFile('valid_two')) {
            $pdfFile = $request->file('valid_two');
            $pdfContent = file_get_contents($pdfFile->getRealPath());
            
            // Check if document exists, update or create
            $document = Documents::where('user_id', $user_id)
                ->where('type', 'valid_two')
                ->first();
            
            if ($document) {
                $document->file = $pdfContent;
                $document->save();
            } else {
                Documents::create([
                    'user_id' => $user_id,
                    'type' => 'valid_two',
                    'file' => $pdfContent,
                ]);
            }
            $documentsUpdated = true;
        }

        $accStats = AccountStatus::where('user_id', $user_id)->first();
        if ($accStats) {
            $accStats->account_status = 'Under review'; 
            $accStats->updated_at = now();
            $accStats->save();
        }

        // Success message
        $message = 'Your account has been successfully resubmitted for review.';
        if ($documentsUpdated) {
            $message .= ' Updated documents have been uploaded.';
        }


        return redirect()->route('error.success')->with('success' , $message);

    }catch (\Exception $e) {
    \Log::error('UpdateDeclined error: ' . $e->getMessage());
    return back()->with('error', 'An error occurred while updating your information. Please try again.');
    }

}



}
