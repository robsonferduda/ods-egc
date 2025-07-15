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
    <h4 class="card-title">Rede de Relacionamentos</h4>
     <!-- Botões de controle -->
<div class="mb-2 text-center">
  <button onclick="cy.zoom(cy.zoom() + 0.1)">🔍 +</button>
  <button onclick="cy.zoom(cy.zoom() - 0.1)">🔍 -</button>
  <button onclick="cy.fit()">🔄 Centralizar</button>
</div>

<!-- Div para o grafo -->
<div id="network" style="height: 600px; border: 1px solid #ccc;"></div>
          
        
    </div>
</div>  
@endsection
@section('script')
<!-- Script Cytoscape -->
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
            'background-color': 'data(color)',
            'label': 'data(label)',
            'width': 'mapData(grau, 1, 20, 20, 60)',
            'height': 'mapData(grau, 1, 20, 20, 60)',
            'font-size': '12px',
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
            'width': 'mapData(value, 1, 10, 1, 6)',
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

      // Melhoria no zoom e pan
      zoomingEnabled: true,
      userZoomingEnabled: true,
      wheelSensitivity: 0.1,
      minZoom: 0.2,
      maxZoom: 3,
      panningEnabled: true,
      userPanningEnabled: true
    });

    // Tooltip simples
    cy.nodes().on('mouseover', function (evt) {
      this.qtip({
        content: this.data('label'),
        show: { ready: true },
        hide: { event: 'mouseout unfocus' },
        position: { my: 'top center', at: 'bottom center' },
        style: {
          classes: 'qtip-bootstrap',
          tip: { width: 16, height: 8 }
        }
      }, evt);
    });
  });
</script>

<!-- Tooltip lib opcional -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/qtip2@3.0.3/dist/jquery.qtip.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qtip2@3.0.3/dist/jquery.qtip.min.js"></script>
@endsection