<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExtensaoExport implements FromCollection
{
    public function collection()
    {
        return $this->data;
    }
}
