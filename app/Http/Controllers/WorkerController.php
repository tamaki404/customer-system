<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customers;
use App\Models\Staffs;


class WorkerController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();
        $staffs = Staffs::orderBy('created_at', 'desc')->get();
        foreach ($staffs as $staff) {
            $staff->contactNo = Customers::where('staff_id', $staff->staff_id)->count();
        }
        return view('franken.stff.list', [
            'user' => $user,
            'staffs' => $staffs,
        ]);
    }
    public function staff($staff_id, Request $request)
    {
        $user = Auth::user();
        $staff = Staffs::where('staff_id', $staff_id)->first(); 
        $customers = AccountStatus::where('staff_id', $staff->staff_id)->orderBy('updated_at', 'desc')->get(); 

        return view('franken.stff.staff', [
            'user' => $user,
            'staff' => $staff,
            'customers' => $customers,
        ]);
    }

}
