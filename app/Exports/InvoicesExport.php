<?php

namespace App\Exports;

use App\Models\invoice;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvoicesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return invoice::all();
    }
}
