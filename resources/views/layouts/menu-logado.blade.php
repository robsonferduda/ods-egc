<nav class="navbar navbar-expand-lg mb-0">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuLimpo" aria-controls="menuLimpo" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-start" id="menuEsquerda">
            <ul class="navbar-nav mb-2 mb-lg-0 text-dark" style="margin-left: -42px;">
                <li class="nav-item me-4">
                    <a class="nav-link text-dark" href="{{ url('meu-perfil') }}">
                        <i class="fas fa-user-circle me-1"></i> Perfil
                    </a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link text-dark" href="{{ url('analisar') }}">
                        <i class="fas fa-file-alt me-1"></i> Analisar Documentos
                    </a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link text-dark" href="{{ url('minhas-analises') }}">
                        <i class="fas fa-chart-bar me-1"></i> Minhas Análises
                    </a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link text-dark" href="{{ url('classificar') }}">
                        <i class="fas fa-handshake me-1"></i> COLABORAR
                    </a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link text-dark" href="{{ url('minhas-avaliacoes') }}">
                        <i class="fas fa-thumbs-up me-1"></i> Minhas Colaborações
                    </a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-1"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<p class="mb-2">Olá <strong>{{ Auth::user()->name }}</strong>! O que deseja fazer hoje?</p>
