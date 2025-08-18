<?php


namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;

class AddressController extends Controller
{
    public function provinces($regionId) {
        return Province::where('region_id', $regionId)
            ->orderBy('province_name')
            ->get(['province_id as id','province_name as name']);
    }


    public function municipalities($provinceId) {
        return Municipality::where('province_id', $provinceId)
            ->orderBy('municipality_name')
            ->get(['municipality_id as id','municipality_name as name']);
    }

    public function barangays($municipalityId) {
        return Barangay::where('municipality_id', $municipalityId)
            ->orderBy('barangay_name')
            ->get(['barangay_id as id','barangay_name as name']);
    }
    
}
