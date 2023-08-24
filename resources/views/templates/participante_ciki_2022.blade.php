<html>
    <head>
        <style>
            body { background-image: url({{ ENV('ASSET_URL').'/img/fundos/ciki_2022.jpg' }}); background-position: bottom right; background-repeat: no-repeat; }
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
                background-image: url({{ ENV('ASSET_URL').'/img/fundos/ciki_2022.jpg' }}); background-position: bottom right; background-repeat: no-repeat;

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
            <p style="font-size: 13px; text-align: center;">The authenticity of this document must be verified in the url https://certificados.studiokem.ufsc.br/certificados/validar/{{ $certificado->ds_hash_cer }}<p>
        </htmlpagefooter>

        <div class="corpo">
            <h2>Certificate of Participation</h2>
            <h1>{{ $certificado->participante->ds_nome_par }}</h1>
            <p>
                Was present and participated in the XII International Congress of Knowledge and Innovation - CiKi 2022, held in Monterrey, Mexico, on November 7-9, 2022.
            </p>
        </div>
    </body>
</html>