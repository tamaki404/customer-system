<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\GlobalCeiling;
use App\Models\ProductSetting;
use Illuminate\Http\Request;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\PriceHistory;

class ProductSettingController extends Controller
{
public function modifyProduct(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'set_id' => 'required|exists:product_settings,set_id',
        'price' => 'required|numeric|min:0'
    ]);

    $productRequirement = ProductSetting::where('set_id', $request->set_id)->firstOrFail();

    $date = date('Ymd');
    $log_id = 'LOG-' . $date . '-' . strtoupper(Str::random(5));

    // Case 1: Removal
    if ($request->has('remove') && $request->remove == 1) {
        $description = "Removed product requirement (Set ID: {$productRequirement->set_id}, Product: {$productRequirement->product->name} from supplier {$productRequirement->supplier_id})";
        $productRequirement->delete();

        Logs::create([
            'user_id'     => $user->user_id,
            'action'      => 'Removed product requirement',
            'log_id'      => $log_id,
            'description' => $description,
            'entity'      => 'ProductSettings',
            'entity_id'   => $request->set_id,
        ]);

        return back()->with('success', 'Product requirement removed successfully.');
    }

    $oldPrice = $productRequirement->nego_price;
    $newPrice = $request->price;

    $city = $productRequirement->supplier->address->office_city ?? null;
    $now = \Carbon\Carbon::now();

    $activeCeiling = GlobalCeiling::whereRaw('LOWER(city_selected) = ?', [strtolower($city)])
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->first();

    if ($activeCeiling) {
        if ($activeCeiling->method === 'Fixed') {
            $ceilingLimit = $activeCeiling->fixed_price;
        } else {
            // Percentage method → ceiling = sale_price * (1 + percentage/100)
            $basePrice = $productRequirement->product->sale_price ?? 0;
            $ceilingLimit = $basePrice + ($basePrice * ($activeCeiling->percentage_ceiling / 100));
        }

        if ($newPrice > $ceilingLimit) {
            return back()->with('error', "Price cannot exceed ceiling limit of ₱" . number_format($ceilingLimit, 2));
        }
    }

    $productRequirement->nego_price = $newPrice;
    $productRequirement->save();

    Logs::create([
        'user_id'     => $user->user_id,
        'action'      => 'Changed negotiated price',
        'log_id'      => $log_id,
        'description' => "Staff ($user->user_id) changed price of {$productRequirement->product->name} (Set ID: {$request->set_id}) from {$oldPrice} to {$newPrice}",
        'entity'      => 'ProductSettings',
        'entity_id'   => $productRequirement->id,
    ]);

    $phistory_id = 'PRICE-' . $date . '-' . strtoupper(Str::random(5));
    PriceHistory::create([
        'phistory_id' => $phistory_id,
        'supplier_id' => $request->supplier_id,
        'action'      => 'Change price',
        'set_id'      => $request->set_id,
        'new_price'   => $newPrice,
        'past_price'  => $oldPrice,
        'action_by'   => $user->user_id,
    ]);

    return back()->with('success', 'Product requirement updated successfully.');
}



}
