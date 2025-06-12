 <div class="pull-left">
    <a href="{{ url('analisar') }}">
        <span class="badge badge-pill badge-default">Analisar Documentos</span>
    </a>
    <a href="{{ url('minhas-analises') }}">
        <span class="badge badge-pill badge-default">Minhas Análises</span>
    </a>
    <a href="{{ url('classificar') }}">
        <span class="badge badge-pill badge-default">COLABORAR</span>
    </a>
    <a href="{{ url('minhas-avaliacoes') }}">
        <span class="badge badge-pill badge-default">Minhas Colaborações</span>
    </a>
    <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
        <span class="badge badge-pill badge-danger">Sair</span>
    </a>
</div>