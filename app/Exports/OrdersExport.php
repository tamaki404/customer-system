<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrdersExport implements FromView
{
    protected $orders;
    protected $startDate;
    protected $endDate;

    public function __construct($orders, $startDate, $endDate)
    {
        $this->orders = $orders;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('exports.orders', [
            'orders' => $this->orders,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }
}
