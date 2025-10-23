<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\AccountStatus;
use App\Models\DeliveryRequirements;
use App\Models\User;
use App\Models\Suppliers;
use App\Models\Documents;
use App\Models\Banks;
use App\Models\Reviews;

use Illuminate\Support\Facades\Auth;

class ErrorController extends Controller
{
// view
public function declined(Request $request)
{
    $user_id = session('user_id'); 
    $supplier = Suppliers::where('user_id', $user_id)->first();
    $accStats = AccountStatus::where('user_id', $user_id)->first();
    $review = Reviews::where('user_id', $user_id)->first();
    // Initialize ALL variables to avoid "undefined variable" errors
    $bank = null;
    $documents = collect();
    $deliveryRequirements = collect();

    $reason = $review->head ?? '';

    if ($review) {
        switch ($review->head) {
            case 'ID image and details':
                $documents = Documents::where('user_id', $user_id)
                    ->whereIn('type', ['valid_one', 'valid_two'])
                    ->orderByRaw("FIELD(type, 'valid_one', 'valid_two')") 
                    ->limit(2)
                    ->get();
                break;

            case 'Bank details':
                $bank = Banks::where('user_id', $user_id)->first();
                break;

            case 'Necessary documents':
                // Get all 8 documents (application/pdf) mediumBlob of each (SEC, BP, BIR, MP, BS, PB, NCC, AIB) 
                $documents = Documents::where('supplier_id', $supplier->supplier_id)
                    ->limit(8)
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
        'bank',
        'accStats',
        'reason',
        'deliveryRequirements',
        'review'
    ));
}
public function success(Request $request){
 return view('error.success');
}

public function review($user_id, Request $request){
    $user = User::where('user_id', $user_id)->first();
    $supplier = Suppliers::where('user_id', $user_id)->first();
    $accStats = AccountStatus::where('user_id', $user_id)->first();
    $reviews = Reviews::where('user_id', $user_id)->first();

    $banks = null;
    $documents = collect();
    $deliveryRequirements = collect();

    $reason = $reviews->head ?? '';

    if ($reviews) {
        switch ($reviews->head) {
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

public function reviewConfirm(Request $request)
{
    // Validate form inputs
    $validated = $request->validate([
        'supplier_id' => 'required|exists:suppliers,supplier_id',
        'reviewed_by' => 'required|exists:users,user_id',
        'account_status' => 'required|string|in:Accepted,Declined',
        'review_feedback' => 'nullable|string|max:500',
    ]);

    // Fetch the supplier
    $supplier = Suppliers::where('supplier_id', $validated['supplier_id'])->first();
    $review = Reviews::where('user_id', $supplier->user_id)->first();
    $status = AccountStatus::where('user_id', $supplier->user_id)->first();


    // Check if records exist
    if (!$review || !$status) {
        return redirect()
            ->route('customers.customer', ['supplier_id' => $supplier->supplier_id])
            ->with('error', 'Review or status record not found.');
    }

    if ($validated['account_status'] === "Accepted") {

            $status->account_status = "To confirm";
            $status->updated_at = now();
            $status->save();

        // Mark review as resolved
        $review->status = "Resolved";
        $review->resolved_by = $validated['reviewed_by'];
        $review->resolved_at = now();
        $review->reviewed_by = $validated['reviewed_by'];
        $review->reviewed_at = now();
        $review->review_feedback = null; // Clear previous feedback
        $review->updated_at = now();
        $review->save();

        return redirect()
            ->route('customers.customer', ['supplier_id' => $supplier->supplier_id])
            ->with('success', 'Supplier confirmed successfully and is now pending approval.');
    } 
    
    elseif ($validated['account_status'] === "Declined") {
        // Validate feedback is provided when declining
        if (empty($validated['review_feedback'])) {
            return redirect()
                ->route('customers.customer', ['supplier_id' => $supplier->supplier_id])
                ->with('error', 'Please provide feedback when declining.');
        }

        $status->account_status = "Declined";
        $status->updated_at = now();
        $status->save();

        // Keep review active with new feedback
        $review->status = "Active";
        $review->review_feedback = $validated['review_feedback'];
        $review->reviewed_by = $validated['reviewed_by'];
        $review->reviewed_at = now();
        $review->updated_at = now();
        $review->save();

        return redirect()
            ->route('customers.customer', ['supplier_id' => $supplier->supplier_id])
            ->with('warning', 'Supplier was declined again with feedback.');
    }

    return redirect()
        ->route('customers.customer', ['supplier_id' => $supplier->supplier_id])
        ->with('error', 'Invalid action selected.');
}



// public function updateDeclined(Request $request)
// {
//     $user_id = session('user_id');
    
//     // Validate the request
//     $request->validate([
//         'key' => 'required|in:ids,banks,docx,delreq',

//         'id_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
//         'id_type' => 'required|string',
//         'id_number' => 'required|string|max:100',
//         'birthdate' => 'required|date',
//         'valid_one' => 'nullable|file|mimes:pdf|max:5120',
//         'valid_two' => 'nullable|file|mimes:pdf|max:5120',

//         // banks to be saved to Banks model whcih it has same supplier_id == supplier_id with
//         'account_name' => 'nullable|max:255',
//         'account_number' => 'nullable|max:100',
//         'bank' => 'nullable|max:100',
//         'branch' => 'nullable|max:100'
//     ]);

//     try {
//         // Update Supplier model (ID details)
//         $supplier = Suppliers::where('user_id', $user_id)->first();

//         if (!$supplier) {
//             return back()->with('error', 'Supplier record not found.');
//         }


//         if($request->key === "ids"){
//             // Only update if new image is uploaded
//             if ($request->hasFile('id_image')) {
//                 $imageFile = $request->file('id_image');
//                 $supplier->id_image = file_get_contents($imageFile->getRealPath());
//             }

//             $supplier->id_type = $request->id_type;
//             $supplier->id_number = $request->id_number;
//             $supplier->birthdate = $request->birthdate;
            
//             $supplier->save();

//             // Update Documents (PDFs) - only if new files are uploaded
//             $documentsUpdated = false;

//             // Handle Valid ID (1)
//             if ($request->hasFile('valid_one')) {
//                 $pdfFile = $request->file('valid_one');
//                 $pdfContent = file_get_contents($pdfFile->getRealPath());
                
//                 // Check if document exists, update or create
//                 $document = Documents::where('user_id', $user_id)
//                     ->where('type', 'valid_one')
//                     ->first();
                
//                 if ($document) {
//                     $document->file = $pdfContent;
//                     $document->save();
//                 } else {
//                     Documents::create([
//                         'user_id' => $user_id,
//                         'type' => 'valid_one',
//                         'file' => $pdfContent,
//                     ]);
//                 }
//                 $documentsUpdated = true;
//             }

//             // Handle Valid ID (2)
//             if ($request->hasFile('valid_two')) {
//                 $pdfFile = $request->file('valid_two');
//                 $pdfContent = file_get_contents($pdfFile->getRealPath());
                
//                 // Check if document exists, update or create
//                 $document = Documents::where('user_id', $user_id)
//                     ->where('type', 'valid_two')
//                     ->first();
                
//                 if ($document) {
//                     $document->file = $pdfContent;
//                     $document->save();
//                 } else {
//                     Documents::create([
//                         'user_id' => $user_id,
//                         'type' => 'valid_two',
//                         'file' => $pdfContent,
//                     ]);
//                 }
//                 $documentsUpdated = true;
//             }
//         }
// elseif ($request->key === "banks") {
//             $bank = Banks::where('supplier_id', $supplier->supplier_id)->first();

//     if (!$bank) {
//         return back()->with('error', 'Bank record not found.');
//     }
    
//     $bank->fill($request->only(['account_name', 'account_number', 'bank', 'branch']));

//     if ($bank->isDirty()) {
//         $bank->save();
//         $message = 'Your bank details have been successfully updated.';
//     } else {
//         $message = 'No changes detected in your bank details.';
//     }

//     // Update review status
//     $accStats = AccountStatus::where('user_id', $user_id)->first();
//     if ($accStats) {
//         $accStats->account_status = 'Under review';
//         $accStats->updated_at = now();
//         $accStats->save();
//     }

//     $review = Reviews::where('user_id', $user_id)->first();
//     if ($review) {
//         $review->status = 'Under review';
//         $review->updated_at = now();
//         $review->save();
//     }

//     return redirect()->route('error.success')->with('success', $message);
// }





//         $accStats = AccountStatus::where('user_id', $user_id)->first();
//         if ($accStats) {
//             $accStats->account_status = 'Under review'; 
//             $accStats->updated_at = now();
//             $accStats->save();
//         }
//         $review = Reviews::where('user_id', $user_id)->first();
//         if ($review) {
//             $review->status = 'Under review'; 
//             $review->updated_at = now();
//             $review->save();
//         }


//         // Success message
//         $message = 'Your account has been successfully resubmitted for review.';
//         if ($documentsUpdated) {
//             $message .= ' Updated documents have been uploaded.';
//         }


//         return redirect()->route('error.success')->with('success' , $message);

//     }catch (\Exception $e) {
//     \Log::error('UpdateDeclined error: ' . $e->getMessage());
//     return back()->with('error', 'An error occurred while updating your information. Please try again.');
//     }

// }

public function updateDeclined(Request $request)
{
    $user_id = session('user_id');

    try {
        $supplier = Suppliers::where('user_id', $user_id)->first();
        if (!$supplier) {
            return back()->with('error', 'Supplier record not found.');
        }

        $key = $request->input('key');
        $documentsUpdated = false; 


        if ($key === 'ids') {
            $request->validate([
                'key' => 'required|in:ids,banks,docx,delreq',
                'id_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'id_type' => 'required|string',
                'id_number' => 'required|string|max:100',
                'birthdate' => 'required|date',
                'valid_one' => 'nullable|file|mimes:pdf|max:5120',
                'valid_two' => 'nullable|file|mimes:pdf|max:5120',
            ]);

            /** Update Supplier ID details **/
            if ($request->hasFile('id_image')) {
                $supplier->id_image = file_get_contents($request->file('id_image')->getRealPath());
            }

            $supplier->id_type = $request->id_type;
            $supplier->id_number = $request->id_number;
            $supplier->birthdate = $request->birthdate;
            $supplier->save();

            /** Handle valid ID documents **/
            foreach (['valid_one', 'valid_two'] as $type) {
                if ($request->hasFile($type)) {
                    $pdfContent = file_get_contents($request->file($type)->getRealPath());
                    Documents::updateOrCreate(
                        ['user_id' => $user_id, 'type' => $type],
                        ['file' => $pdfContent]
                    );
                    $documentsUpdated = true;
                }
            }

            $message = 'Your account has been successfully resubmitted for review.';
            if ($documentsUpdated) {
                $message .= ' Updated documents have been uploaded.';
            }

        } elseif ($key === 'banks') {

            $request->validate([
                'key' => 'required|in:ids,banks,docx,delreq',
                'account_name' => 'nullable|max:255',
                'account_number' => 'nullable|max:100',
                'bank' => 'nullable|max:100',
                'branch' => 'nullable|max:100',


                
            ]);

            $bank = Banks::where('supplier_id', $supplier->supplier_id)->first();

            if (!$bank) {
                return back()->with('error', 'Bank record not found.');
            }

            $bank->fill($request->only(['account_name', 'account_number', 'bank', 'branch']));

            if ($bank->isDirty()) {
                $bank->save();
                $message = 'Your bank details have been successfully updated and resubmitted for review.';
            } else {
                $message = 'No changes detected in your bank details.';
            }
        } elseif ($request->key === 'docx') {
                // Define required document types and friendly names (same as in your Blade)

            $request->validate([
                'SEC'             => 'required|file|mimes:pdf|max:2048',
                'BP'              => 'required|file|mimes:pdf|max:2048',
                'BIR'             => 'required|file|mimes:pdf|max:2048',
                'MP'              => 'required|file|mimes:pdf|max:2048',
                'valid_one'       => 'required|file|mimes:pdf|max:2048',
                'valid_two'       => 'required|file|mimes:pdf|max:2048',
                'BS'              => 'required|file|mimes:pdf|max:2048',
                'PB'              => 'required|file|mimes:pdf|max:2048',
                'NCC'             => 'required|file|mimes:pdf|max:2048',
                'AIB'             => 'required|file|mimes:pdf|max:2048',
            ]);

                $requiredDocs = [
                    'SEC' => 'SEC certificate',
                    'BP' => 'Business Permit',
                    'BIR' => 'BIR Certificate',
                    'MP' => 'Mayor’s Permit',
                    'BS' => 'Bank Statement',
                    'PB' => 'Proof of Billing',
                    'NCC' => 'Notarized corporation certificate',
                    'AIB' => 'Articles of incorporation and bylaws',
                ];

                $docsUpdated = false;

                foreach ($requiredDocs as $key => $label) {
                    if ($request->hasFile(strtolower($key))) {
                        $pdfFile = $request->file(strtolower($key));
                        $pdfContent = file_get_contents($pdfFile->getRealPath());

                        // Check if the document already exists
                        $document = Documents::where('user_id', $user_id)
                            ->where('type', $key)
                            ->first();

                        if ($document) {
                            // Update existing document
                            $document->file = $pdfContent;
                            $document->save();
                        } else {
                            // Create new document
                            Documents::create([
                                'user_id' => $user_id,
                                'type' => $key,
                                'file' => $pdfContent,
                            ]);
                        }

                        $docsUpdated = true;
                    }
                }

                // Update review status after uploading
                $accStats = AccountStatus::where('user_id', $user_id)->first();
                if ($accStats) {
                    $accStats->account_status = 'Under review';
                    $accStats->updated_at = now();
                    $accStats->save();
                }

                $review = Reviews::where('user_id', $user_id)->first();
                if ($review) {
                    $review->status = 'Under review';
                    $review->updated_at = now();
                    $review->save();
                }

                $message = $docsUpdated
                    ? 'Your necessary documents have been successfully updated and resubmitted for review.'
                    : 'No new documents were uploaded.';

                return redirect()->route('error.success')->with('success', $message);
        }else {
            return back()->with('error', 'Invalid form submission.');
        }

        $accStats = AccountStatus::where('user_id', $user_id)->first();
        if ($accStats) {
            $accStats->account_status = 'Under review';
            $accStats->updated_at = now();
            $accStats->save();
        }

        $review = Reviews::where('user_id', $user_id)->first();
        if ($review) {
            $review->status = 'Under review';
            $review->updated_at = now();
            $review->save();
        }

        return redirect()->route('error.success')->with('success', $message);

    } catch (\Exception $e) {
        \Log::error('UpdateDeclined error: ' . $e->getMessage());
        return back()->with('error', 'An error occurred while updating your information. Please try again.');
    }
}


}
