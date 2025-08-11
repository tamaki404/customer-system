<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsExport implements FromView
{
    public function __construct(
        protected $products,
        protected $startDate,
        protected $endDate
    ) {}

    public function view(): View
    {
        return view('exports.products', [
            'products' => $this->products,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}


