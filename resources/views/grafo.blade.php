@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="row mb-5 mt-3">
        <div class="col-md-2 col-sm-2">
            <img src="{{ asset('img/logo-egc.png') }}" class="img-fluid mt-1" alt="Responsive image">
        </div>
        <div class="col-md-10 col-sm-10">
            <h6 class="mb-0">Universidade Federal de Santa catarina (UFSC)</h6>
            <h6 class="mb-0 mt-1">Programa de Pós-graduação em Engenharia, Gestão e Mídia do Conhecimento (PPGEGC)</h6>
            <h6 class="mb-0 mt-1">Engenharia do Conhecimento/Teoria e prática em Engenharia do Conhecimento</h6>
        </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Rede de Relacionamentos</h4>
          </div>
          <div class="card-body">
            <div id="network" style="height: 600px; border: 1px solid #ccc;"></div>
          </div>
        </div>
      </div>
    </div>     
</div>
@endsection
@section('script')
    <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
          const nodes = @json($nodes);
          const edges = @json($edges);

          const container = document.getElementById('network');
          if (!container) {
            console.error("Elemento #network não encontrado!");
            return;
          }

          const data = {
            nodes: new vis.DataSet(nodes),
            edges: new vis.DataSet(edges)
          };

          const options = {
            nodes: { shape: 'dot', size: 10, font: { size: 14 } },
            edges: { arrows: 'none', smooth: true },
            physics: { stabilization: false }
          };

          new vis.Network(container, data, options);
        });
    </script>
@endsection