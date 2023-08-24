<html>
    <head>
        <style>
            body { background-image: url({{ ENV('ASSET_URL').'/img/modelos/ck1.jpg' }}); background-position: bottom right; background-repeat: no-repeat; }
            .corpo { padding-top: 80px; }

            .rodape { text-align: center; padding-top: 240px; }
            .rodape p {font-size: 12px; }

            *{margin:0;padding:0}
        </style>
    </head>
    <body>
        <div class="corpo">
            {!! $certificado->de_texto_certificado !!}
        </div>
        <div class="rodape">
            <p>A autenticidade deste documento pose ser verificada no endereço https://certificado.studiokem.ufsc.br/certificados/validacao/{{ $certificado->de_hash }}<p>
        </div>
    </body>
</html>