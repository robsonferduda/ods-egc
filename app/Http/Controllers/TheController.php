<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TheController extends Controller
{
    public function dashboard()
    {
        $totalEvidencias = DB::table('documento_ods')
            ->where('ods', '!=', 0)
            ->count();
            
        $centrosEngajados = DB::table('documento_ods as d')
            ->join('documento_pessoa_dop as dp', 'dp.id_documento_ods', '=', 'd.id')
            ->join('pessoa_pes as p', 'p.id_pessoa_pes', '=', 'dp.id_pessoa_pes')
            ->whereNotNull('d.id_centro')
            ->distinct('d.id_centro')
            ->count('d.id_centro');
            
        $docentesAtivos = DB::table('documento_pessoa_dop')
            ->distinct('id_pessoa_pes')
            ->count('id_pessoa_pes');
            
        $statusOds = $this->calcularStatusOds();
        
        // Calcular ODSs por nível
        $estatisticas = [
            'forte' => collect($statusOds)->where('nivel', 'forte')->count(),
            'medio' => collect($statusOds)->where('nivel', 'medio')->count(),
            'fraco' => collect($statusOds)->where('nivel', 'fraco')->count(),
        ];
        
        return view('the.dashboard', compact(
            'totalEvidencias',
            'centrosEngajados', 
            'docentesAtivos',
            'statusOds',
            'estatisticas'
        ));
    }
    
    public function indiceProntidao()
    {
        $statusOds = $this->calcularStatusOds();
        
        // Agrupar por nível de prontidão
        $odsPorNivel = [
            'forte' => collect($statusOds)->where('nivel', 'forte'),
            'medio' => collect($statusOds)->where('nivel', 'medio'),
            'fraco' => collect($statusOds)->where('nivel', 'fraco'),
        ];
        
        return view('the.prontidao', compact('statusOds', 'odsPorNivel'));
    }
    
    private function calcularStatusOds()
    {
        $status = [];
        $odsNomes = $this->getOdsNomes();
        
        for ($i = 1; $i <= 17; $i++) {
            $evidencias = DB::table('documento_ods')
                ->where('ods', $i)
                ->where('ods', '!=', 0)
                ->count();
                
            // Meta: 50 documentos = 100% de cobertura
            $cobertura = min(($evidencias / 50) * 100, 100);
            
            // Calcular distribuição por dimensão
            $porDimensao = DB::table('documento_ods')
                ->select('id_dimensao', DB::raw('count(*) as total'))
                ->where('ods', $i)
                ->where('ods', '!=', 0)
                ->groupBy('id_dimensao')
                ->get()
                ->pluck('total', 'id_dimensao')
                ->toArray();
            
            $status[$i] = [
                'numero' => $i,
                'nome' => $odsNomes[$i] ?? "ODS {$i}",
                'evidencias' => $evidencias,
                'cobertura' => round($cobertura),
                'nivel' => $cobertura >= 70 ? 'forte' : 
                          ($cobertura >= 40 ? 'medio' : 'fraco'),
                'acao' => $this->definirAcao($cobertura),
                'por_dimensao' => $porDimensao,
                'prioridade' => $this->calcularPrioridade($i, $evidencias)
            ];
        }
        
        return $status;
    }
    
    private function definirAcao($cobertura)
    {
        if ($cobertura >= 70) return 'Submeter';
        if ($cobertura >= 40) return 'Reforçar';
        return 'Buscar Evidências';
    }
    
    private function calcularPrioridade($ods, $evidencias)
    {
        // ODSs mais comuns no THE Impact Rankings
        $odsComuns = [3, 4, 5, 8, 9, 11, 13, 16, 17];
        
        if (in_array($ods, $odsComuns) && $evidencias >= 35) {
            return 'alta';
        } elseif ($evidencias >= 20) {
            return 'media';
        }
        return 'baixa';
    }
    
    public function evidenciasOds($numero)
    {
        $odsNomes = $this->getOdsNomes();
        $nomeOds = $odsNomes[$numero] ?? "ODS {$numero}";
        
        // Buscar evidências
        $evidencias = DB::table('documento_ods as d')
            ->leftJoin('documento_pessoa_dop as dp', 'dp.id_documento_ods', '=', 'd.id')
            ->leftJoin('pessoa_pes as p', 'p.id_pessoa_pes', '=', 'dp.id_pessoa_pes')
            ->leftJoin('centro_cen as c', 'c.cd_centro_cen', '=', 'd.id_centro')
            ->leftJoin('dimensao as dim', 'dim.id', '=', 'd.id_dimensao')
            ->where('d.ods', $numero)
            ->select(
                'd.id',
                'd.titulo',
                'd.ano',
                'd.resumo',
                'p.nm_pessoa_pes as autor',
                'c.ds_sigla_cen as centro',
                'dim.nome as dimensao',
                'd.probabilidade'
            )
            ->orderBy('d.probabilidade', 'desc')
            ->orderBy('d.ano', 'desc')
            ->paginate(50);
            
        // Estatísticas
        $stats = [
            'total' => DB::table('documento_ods')->where('ods', $numero)->count(),
            'por_ano' => DB::table('documento_ods')
                ->select('ano', DB::raw('count(*) as total'))
                ->where('ods', $numero)
                ->where('ods', '!=', 0)
                ->groupBy('ano')
                ->orderBy('ano', 'desc')
                ->limit(5)
                ->get(),
            'por_centro' => DB::table('documento_ods as d')
                ->join('documento_pessoa_dop as dp', 'dp.id_documento_ods', '=', 'd.id')
                ->join('pessoa_pes as p', 'p.id_pessoa_pes', '=', 'dp.id_pessoa_pes')
                ->join('centro as c', 'c.cd_centro_cen', '=', 'p.id_centro_cen')
                ->select('c.ds_sigla_cen as centro', DB::raw('count(distinct d.id) as total'))
                ->where('d.ods', $numero)
                ->groupBy('c.ds_sigla_cen')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'por_dimensao' => DB::table('documento_ods as d')
                ->join('dimensao as dim', 'dim.id', '=', 'd.id_dimensao')
                ->select('dim.nome as dimensao', DB::raw('count(*) as total'))
                ->where('d.ods', $numero)
                ->groupBy('dim.nome')
                ->orderBy('total', 'desc')
                ->get()
        ];
            
        return view('the.evidencias', compact('numero', 'nomeOds', 'evidencias', 'stats'));
    }
    
    public function analiseGaps()
    {
        $statusOds = $this->calcularStatusOds();
        
        // Separar por nível de urgência
        $criticos = collect($statusOds)->where('nivel', 'fraco')->sortBy('evidencias');
        $atencao = collect($statusOds)->where('nivel', 'medio')->sortBy('evidencias');
        $fortes = collect($statusOds)->where('nivel', 'forte')->sortByDesc('evidencias');
        
        // Sugestões de ação por ODS
        $sugestoes = $this->gerarSugestoes($statusOds);
        
        return view('the.gaps', compact('criticos', 'atencao', 'fortes', 'sugestoes'));
    }
    
    private function gerarSugestoes($statusOds)
    {
        $sugestoes = [
            1 => ['contato' => 'CSE, Serviço Social', 'acao' => 'Mapear projetos de combate à pobreza'],
            2 => ['contato' => 'CCA, Nutrição, RU', 'acao' => 'Documentar programas de segurança alimentar'],
            3 => ['contato' => 'CCS, HU, Farmácia', 'acao' => 'Compilar pesquisas e ações em saúde'],
            4 => ['contato' => 'Todos os Centros', 'acao' => 'Destacar programas educacionais e inclusão'],
            5 => ['contato' => 'CFH, CCJ', 'acao' => 'Mapear estudos de gênero e equidade'],
            6 => ['contato' => 'ENS, ECV', 'acao' => 'Documentar pesquisas em recursos hídricos'],
            7 => ['contato' => 'EEL, ENR', 'acao' => 'Compilar pesquisas em energia renovável'],
            8 => ['contato' => 'CSE, CCJ', 'acao' => 'Mapear estudos sobre trabalho decente'],
            9 => ['contato' => 'CTC, Blumenau, Joinville', 'acao' => 'Destacar inovação e infraestrutura'],
            10 => ['contato' => 'CSE, CFH', 'acao' => 'Documentar pesquisas sobre desigualdade'],
            11 => ['contato' => 'ARQ, ECV', 'acao' => 'Mapear projetos urbanos sustentáveis'],
            12 => ['contato' => 'PURESGE, CTC', 'acao' => 'Documentar práticas de consumo responsável'],
            13 => ['contato' => 'CCB, PURESGE', 'acao' => 'Compilar ações climáticas institucionais'],
            14 => ['contato' => 'CCB, Aquicultura', 'acao' => 'Mapear pesquisas marinhas e costeiras'],
            15 => ['contato' => 'CCB, CCA', 'acao' => 'Documentar estudos sobre biodiversidade'],
            16 => ['contato' => 'CCJ, CFH', 'acao' => 'Mapear pesquisas sobre justiça e paz'],
            17 => ['contato' => 'SINTER, Todos', 'acao' => 'Destacar parcerias e colaborações']
        ];
        
        return $sugestoes;
    }
    
    public function exportarEvidencias($ods)
    {
        $odsNomes = $this->getOdsNomes();
        $nomeOds = $odsNomes[$ods] ?? "ODS {$ods}";
        
        $evidencias = DB::table('documento_ods as d')
            ->leftJoin('documento_pessoa_dop as dp', 'dp.id_documento_ods', '=', 'd.id')
            ->leftJoin('pessoa_pes as p', 'p.id_pessoa_pes', '=', 'dp.id_pessoa_pes')
            ->leftJoin('centro as c', 'c.cd_centro_cen', '=', 'p.id_centro_cen')
            ->leftJoin('dimensao as dim', 'dim.id', '=', 'd.id_dimensao')
            ->where('d.ods', $ods)
            ->select(
                'd.titulo',
                'd.ano',
                'd.resumo',
                'p.nm_pessoa_pes as autor',
                'c.ds_sigla_cen as centro',
                'dim.nome as dimensao',
                'd.probabilidade'
            )
            ->orderBy('d.probabilidade', 'desc')
            ->orderBy('d.ano', 'desc')
            ->get();
            
        return view('the.export', compact('ods', 'nomeOds', 'evidencias'));
    }
    
    public function matrizAlinhamento()
    {
        // Matriz Dimensões IES × ODSs THE
        $matriz = [];
        
        $dimensoes = DB::table('dimensao')->orderBy('id')->get();
        
        foreach ($dimensoes as $dimensao) {
            $linha = ['dimensao' => $dimensao->nome, 'id' => $dimensao->id];
            
            for ($ods = 1; $ods <= 17; $ods++) {
                $total = DB::table('documento_ods')
                    ->where('id_dimensao', $dimensao->id)
                    ->where('ods', $ods)
                    ->count();
                    
                $linha["ods_{$ods}"] = $total;
            }
            
            $matriz[] = $linha;
        }
        
        return view('the.matriz', compact('matriz'));
    }
    
    private function getOdsNomes()
    {
        return [
            1 => 'Erradicação da Pobreza',
            2 => 'Fome Zero',
            3 => 'Saúde e Bem-Estar',
            4 => 'Educação de Qualidade',
            5 => 'Igualdade de Gênero',
            6 => 'Água Potável e Saneamento',
            7 => 'Energia Limpa e Acessível',
            8 => 'Trabalho Decente e Crescimento Econômico',
            9 => 'Indústria, Inovação e Infraestrutura',
            10 => 'Redução das Desigualdades',
            11 => 'Cidades e Comunidades Sustentáveis',
            12 => 'Consumo e Produção Responsáveis',
            13 => 'Ação Contra a Mudança Global do Clima',
            14 => 'Vida na Água',
            15 => 'Vida Terrestre',
            16 => 'Paz, Justiça e Instituições Eficazes',
            17 => 'Parcerias e Meios de Implementação'
        ];
    }
}
