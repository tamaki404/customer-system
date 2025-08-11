<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReceiptsExport implements FromView
{
    public function __construct(
        protected $receipts,
        protected $startDate,
        protected $endDate
    ) {}

    public function view(): View
    {
        return view('exports.receipts', [
            'receipts' => $this->receipts,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}


