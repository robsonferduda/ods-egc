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
              <div id="cy" style="width: 100%; height: 600px;"></div>
          </div>
        </div>
      </div>
    </div>     
</div>
@endsection
@section('script')
    <script src="https://unpkg.com/cytoscape@3.26.0/dist/cytoscape.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
      const rawNodes = @json($nodes);
      const rawEdges = @json($edges);

      const container = document.getElementById("cy");
      if (!container) {
        console.error("Elemento #cy não encontrado!");
        return;
      }

      const cy = cytoscape({
        container: container,
        elements: [
          // Nodes
          ...rawNodes.map(n => ({
            data: { id: n.id.toString(), label: n.label }
          })),

          // Edges
          ...rawEdges.map(e => ({
            data: {
              id: `${e.from}-${e.to}`,
              source: e.from.toString(),
              target: e.to.toString()
            }
          }))
        ],
        style: [
          {
            selector: "node",
            style: {
              "background-color": "#007bff",
              label: "data(label)",
              color: "#fff",
              "text-valign": "center",
              "text-halign": "center",
              "font-size": "12px"
            }
          },
          {
            selector: "edge",
            style: {
              width: 2,
              "line-color": "#ccc",
              "target-arrow-color": "#ccc",
              "target-arrow-shape": "triangle",
              "curve-style": "bezier"
            }
          }
        ],
        layout: {
          name: "cose", // outros: grid, circle, concentric
          animate: true
        }
      });
    });
  </script>
@endsection