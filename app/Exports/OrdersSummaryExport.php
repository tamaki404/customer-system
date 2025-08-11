<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrdersSummaryExport implements FromView
{
    public function __construct(
        protected $orders,
        protected $startDate,
        protected $endDate
    ) {}

    public function view(): View
    {
        return view('exports.orders_summary', [
            'orders' => $this->orders,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}


