<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Representatives;
use App\Models\Signatories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{

    public static function randomBase36String(int $length): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $str;
    }
   
    public function GroupsView(Request $request)
    {
        $user = Auth::user();
        $supplier = Suppliers::where('user_id', $user->user_id)->first(); 
        $reps = Representatives::where('user_id', $user->user_id)->get();
        $signs = Signatories::where('user_id', $user->user_id)->get();

        return view('groups.group', [
        'user' => $user,
        'reps' => $reps,
        'signs' => $signs,

        ]);
    }

    public function modifyAccount(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'rep_id'  => 'required|exists:representatives,rep_id',

        ]);

        $rep = Representatives::where('rep_id', $request->rep_id)->firstOrFail();

        try {
            $permissions = [
                'Dashboard' => $request->has('Dashboard'),
                'Profile' => $request->has('Profile'),
                'Credits' => $request->has('Credits'),
                'POP' => $request->has('POP'),
                'PO' => $request->has('PO'),
                'Orders' => $request->has('Orders'),
                'Products' => $request->has('Products'),
            ];

            $cid = 'CONTROL-' . $rep->rep_lastname . '-' . $this->randomBase36String(5);

            DB::table('representatives')
                ->where('rep_id', $request->rep_id)
                ->update([
                    'permissions' => json_encode($permissions),
                    'updated_at' => now(),
                    'cid' => $cid,
                ]);

            return redirect()->back()->with('success', 'Representative permissions updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

}
