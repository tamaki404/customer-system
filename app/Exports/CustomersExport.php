<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomersExport implements FromView
{
    public function __construct(
        protected $customers,
        protected $startDate,
        protected $endDate
    ) {}

    public function view(): View
    {
        return view('exports.customers', [
            'customers' => $this->customers,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}


