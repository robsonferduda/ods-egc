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
        <h6><i class="fa fa-users" aria-hidden="true"></i> Rede de Relacionamentos</h6>
        <div id="cy" style="width: 100%; height: 800px;"></div>
    </div>
</div> 
@endsection
@section('script')
<script src="https://unpkg.com/cytoscape@3.26.0/dist/cytoscape.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cytoscape-cose-bilkent@4.0.0/cytoscape-cose-bilkent.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const nodes = @json($nodes);
    const edges = @json($edges);

    const cy = cytoscape({
      container: document.getElementById('cy'),
      elements: {
        nodes: nodes.map(n => ({
          data: {
            id: n.id,
            label: n.label,
            color: n.color ?? '#007bff', // azul padrão
            title: n.label // tooltip
          }
        })),
        edges: edges.map(e => ({
          data: {
            id: `edge-${e.from}-${e.to}`,
            source: e.from,
            target: e.to,
            value: e.value
          }
        }))
      },
      style: [
        {
          selector: 'node',
          style: {
            'shape': 'round-rectangle',
            'label': 'data(label)',
            'width': 'label',
            'padding': '6px',
            'height': 'label',
            'font-size': '11px',
            'background-color': 'data(color)',
            'text-valign': 'center',
            'text-halign': 'center',
            'color': '#fff',
            'text-outline-color': '#444',
            'text-outline-width': 2
          }
        },
        {
          selector: 'edge',
          style: {
            'width': 'mapData(value, 1, 10, 1, 5)',
            'line-color': '#bbb',
            'target-arrow-color': '#999',
            'target-arrow-shape': 'triangle',
            'curve-style': 'bezier'
          }
        }
      ],
      layout: {
          name: 'cose-bilkent',
          animate: true
      }
    });

    // Tooltip nativo
    cy.nodes().forEach(node => {
      node.qtip({
        content: node.data('title'),
        show: { event: 'mouseover' },
        hide: { event: 'mouseout' },
        position: { my: 'top center', at: 'bottom center' },
        style: {
          classes: 'qtip-bootstrap',
          tip: { width: 16, height: 8 }
        }
      });
    });
  });
</script>
@endsection