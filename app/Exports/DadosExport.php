<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DadosExport implements FromView
{
    public $dados;

    public function __construct($dados)
    {
        $this->dados = $dados;
    }

    public function view(): View
    {
        return view('dados.export.evolucao', [
            'dados' => $this->dados
        ]);
    }
}