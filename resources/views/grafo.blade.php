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
    <!-- HTML -->
<div id="cy" style="width: 100%; height: 600px;"></div>

<!-- Script -->
<script src="https://unpkg.com/cytoscape@3.26.0/dist/cytoscape.min.js"></script>
<script src="https://unpkg.com/cytoscape-qtip@2.7.0/cytoscape-qtip.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.3/jquery.qtip.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.3/jquery.qtip.min.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const rawNodes = @json($nodes);
    const rawEdges = @json($edges);

    // Cores por tipo
    const tipoCores = {
      'Orientador': '#007bff',
      'Participante': '#28a745',
      'Inventor': '#ffc107',
      'Aluno': '#17a2b8',
      'Coordenador': '#6610f2',
      'Desconhecido': '#6c757d'
    };

    const cy = cytoscape({
      container: document.getElementById('cy'),
      elements: [
        ...rawNodes.map(n => ({
          data: {
            id: n.id.toString(),
            label: n.label,
            tipo: n.tipo || 'Desconhecido'
          }
        })),
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
          selector: 'node',
          style: {
            'background-color': ele => tipoCores[ele.data('tipo')] || '#999',
            'label': 'data(label)',
            'text-valign': 'center',
            'color': '#fff',
            'font-size': '12px',
            'width': ele => 20 + ele.degree() * 2,
            'height': ele => 20 + ele.degree() * 2
          }
        },
        {
          selector: 'edge',
          style: {
            'width': 2,
            'line-color': '#ccc',
            'target-arrow-color': '#ccc',
            'target-arrow-shape': 'triangle',
            'curve-style': 'bezier'
          }
        }
      ],
      layout: { name: 'cose', animate: true }
    });

    // Tooltips com qTip2
    cy.nodes().forEach(function (ele) {
      ele.qtip({
        content: {
          title: ele.data('label'),
          text: 'Função: ' + ele.data('tipo')
        },
        position: {
          my: 'top center',
          at: 'bottom center'
        },
        style: {
          classes: 'qtip-bootstrap'
        }
      });
    });
  });
</script>
@endsection