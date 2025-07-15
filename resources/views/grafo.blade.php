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
</div>
<div class="row">
    <div class="col-md-12">
     
            <div class="mb-2 text-center">
  <button onclick="cy.zoom(cy.zoom() + 0.1)">🔍 +</button>
  <button onclick="cy.zoom(cy.zoom() - 0.1)">🔍 -</button>
  <button onclick="cy.fit()">🔄 Centralizar</button>
</div>

<div id="network" style="height: 600px; border: 1px solid #ccc;"></div>
          
        
    </div>
</div>  
@endsection
@section('script')
<script src="https://unpkg.com/cytoscape@3.24.0/dist/cytoscape.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const nodes = @json($nodes);
    const edges = @json($edges);

    const cy = cytoscape({
      container: document.getElementById('network'),
      elements: {
        nodes: nodes,
        edges: edges
      },
      style: [
        {
          selector: 'node',
          style: {
            'background-color': '#1f77b4',
            'label': 'data(label)',
            'width': 'mapData(grau, 1, 20, 25, 60)',
            'height': 'mapData(grau, 1, 20, 25, 60)',
            'font-size': 12,
            'color': '#fff',
            'text-valign': 'center',
            'text-halign': 'center',
            'text-wrap': 'wrap',
            'text-max-width': 100
          }
        },
        {
          selector: 'edge',
          style: {
            'width': 'mapData(value, 1, 10, 1, 5)',
            'line-color': '#ccc',
            'target-arrow-color': '#ccc',
            'target-arrow-shape': 'triangle',
            'curve-style': 'bezier'
          }
        }
      ],
      layout: {
        name: 'cose',
        animate: true,
        padding: 30
      },
      wheelSensitivity: 0.2,
      minZoom: 0.3,
      maxZoom: 3
    });

    // Simples tooltip ao passar o mouse (usando title padrão)
    cy.nodes().forEach(function (node) {
      node.qtip = node.data('label');
      node.on('mouseover', function () {
        node.style('label', node.qtip);
      });
    });
  });
</script>
@endsection