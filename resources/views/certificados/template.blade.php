<html>
    <head>
        <style>
            body { background-image: url({{ ENV('ASSET_URL').'/img/modelos/ciki_2020_resized4.jpg' }}); background-position: bottom right; background-repeat: no-repeat; }
            .corpo { text-align: center; padding-top: 180px; }
            .corpo p { font-size: 20px; }
            .corpo h1 { padding-bottom: 14px; }
            .corpo h2 { padding-bottom: 14px; }

            .rodape { text-align: center; padding-top: 200px; }
            .rodape p {font-size: 12px; }

            *{margin:0;padding:0}
        </style>
    </head>
    <body>
        <div class="corpo">
            <h2>Evaluator Certificate</h2>
            <h1>Robson Fernando Duda</h1>
            <p>
                Participated in the X International Congress of Knowledge and Innovation - ciKi 2020
            </p>
            <p>
                Virtual Mode on November 19 and 20, 2020
            </p>
            <p>
                How: Reviewer of articles 
            </p>
        </div>
        <div class="rodape">
            <p>The authenticity of this document must be verified in the url https://certificado.studiokem.ufsc.br/certificados/validacao/{{ $certificado->ds_hash_cer }}<p>
        </div>
    </body>
</html>
