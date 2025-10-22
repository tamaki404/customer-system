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



}
