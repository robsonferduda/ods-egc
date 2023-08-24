<html>
    <head>
        <style>
            body { background-image: url({{ ENV('ASSET_URL').'/img/fundos/certificado_resized.png' }}); background-position: bottom right; background-repeat: no-repeat; }
            .corpo { text-align: center; padding-top: 180px; min-height: 500px; }
            .corpo p { font-size: 20px; }
            .corpo h1 { padding-bottom: 14px; }
            .corpo h2 { padding-bottom: 14px; }

            .rodape { text-align: center; }
            .rodape p {font-size: 12px; }

            @page {
                margin: 10%;
                margin-header: 3mm; 
	            margin-footer: 3mm; 
                background-image: url({{ ENV('ASSET_URL').'/img/fundos/certificado_resized.png' }}); background-position: bottom right; background-repeat: no-repeat;

                header: page-header;
		        footer: page-footer;
            }

            @page-footer {
                font-size: 10px;
            }
        </style>
    </head>
    <body>
        <htmlpagefooter name="page-footer">
            <p style="font-size: 13px; text-align: center;">The authenticity of this document must be verified in the url https://certificado.studiokem.ufsc.br/certificados/validacao/{{ $certificado->ds_hash_cer }}<p>
        </htmlpagefooter>

        <div class="corpo">
            <h2>Participation Certificate</h2>
            <h1>{{ $certificado->participante->ds_nome_par }}</h1>
            <p>
                Participated in the 11th International Conference on Knowledge and Innovation - ciKi 2021
            </p>
            <p>
                Celebrated in Virtual Mode on the 18th and 19th of November 2021
            </p>
        </div>
    </body>
</html>