<span class="pull-right" style="font-size: 16px;">
                    <strong>{{ Auth::user()->pts }}</strong> Pontos /
                    <strong>{{ Auth::user()->nivel->ds_nivel }}</strong>
                    <img style="width: 15%;" src="{{ asset('img/nivel/'.Auth::user()->nivel->ds_icone) }}" class="img-fluid" alt="Nível">
                </span>