<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\ProductSetting;
use Illuminate\Http\Request;

class ProductSettingController extends Controller
{
    public function modifyProduct(Request $request)
    {
        $request->validate([
            'set_id' => 'required|exists:product_settings,set_id',
        ]);

        $productRequirement = ProductSetting::where('set_id', $request->set_id)->first();

        if ($request->has('remove') && $request->remove == 1) {
            $productRequirement->delete();
            return back()->with('success', 'Product requirement removed successfully.');
        }

        if ($request->filled('price')) {
            $productRequirement->price = $request->price;
            $productRequirement->save();
        }

        return back()->with('success', 'Product requirement updated successfully.');
    }
}
