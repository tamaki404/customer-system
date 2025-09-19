<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\ProductSetting;
use Illuminate\Http\Request;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductSettingController extends Controller
{
    public function modifyProduct(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'set_id' => 'required|exists:product_settings,set_id',
        ]);

        $productRequirement = ProductSetting::where('set_id', $request->set_id)->firstOrFail();

        $date = date('Ymd');
        $log_id = 'LOG-' . $date . '-' . strtoupper(Str::random(5));

        $description = '';

        //  Case 1: Removal
        if ($request->has('remove') && $request->remove == 1) {
            $description = "Removed product requirement (Set ID: {$productRequirement->set_id}, Product: {$productRequirement->product->name} from supplier {$productRequirement->supplier_id})";
            $productRequirement->delete();

            Logs::create([
                'user_id'     => $user->user_id,
                'action'      => 'Modified product requirements of a supplier',
                'log_id'      => $log_id,
                'description' => $description,
            ]);

            return back()->with('success', 'Product requirement removed successfully.');
        }

        //  Case 2: Price update
        if ($request->filled('price')) {
            $oldPrice = $productRequirement->price;
            $newPrice = $request->price;

            $productRequirement->price = $newPrice;
            $productRequirement->save();

            $description = "Updated price for product requirement (Set ID: {$productRequirement->set_id}, Product: {$productRequirement->product->name}) from {$oldPrice} to {$newPrice} of supplier {$productRequirement->supplier_id} ";
        }

        Logs::create([
            'user_id'     => $user->user_id,
            'action'      => 'Modified product requirements of a supplier',
            'log_id'      => $log_id,
            'description' => $description,
        ]);

        return back()->with('success', 'Product requirement updated successfully.');
    }

}
