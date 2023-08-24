<?php

namespace App\Imports;

use App\Participante;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ParticipanteImport implements WithHeadingRow, SkipsOnError
{
    use SkipsErrors;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            new Participante([
                'nome' => $row[0],
                'email' => $row[1]
            ]);
        }
    }

    public function onError(\Throwable $e)
    {
        dd($e);
    }
}
