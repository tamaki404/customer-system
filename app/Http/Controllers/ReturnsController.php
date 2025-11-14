<?php

namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\DeliveryRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryItemRequest;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ReturnsController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();

        $varianceByDelivery = DeliveryItemRequest::select(
                'delivery_id',
                DB::raw('SUM(planned_heads - received_heads) AS heads_variance'),
                DB::raw('SUM(planned_kilos - received_kilos) AS kilos_variance')
            )
            ->whereRaw('planned_heads != received_heads OR planned_kilos != received_kilos')
            ->groupBy('delivery_id')
            ->orderBy('delivery_id', 'desc')
            ->get();



        return view('franken.rtn.list', compact(
            'user',
            'varianceByDelivery',
        ));
    }

}
