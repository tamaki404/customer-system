<?php

namespace App\Http\Controllers;
use App\Models\DeliveryRequest;
use App\Models\Customers;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function receipt($delivery_id, Request $request )
    {
        $delivery = DeliveryRequest::where('delivery_id',$delivery_id)->firstOrFail();
        $customer = Customers::where('customer_id', $delivery->customer_id)->first();
        $pdf = PDF::loadView('franken.pdf.receipt', compact('delivery'));
        return $pdf->stream("$customer->company_name.$delivery->delivery_id.pdf");
    }

}