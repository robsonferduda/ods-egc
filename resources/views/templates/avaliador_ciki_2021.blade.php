<html>
    <head>
        <style>
             body { background-image: url({{ ENV('ASSET_URL').'/img/fundos/certificado_resized.png' }}); background-position: bottom right; background-repeat: no-repeat; }
            .corpo { text-align: center; padding-top: 150px; min-height: 500px; }
            .corpo p { font-size: 17px; }
            .corpo h1 { padding-bottom: 10px; }
            .corpo h2 { padding-bottom: 10px; }

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
            <p style="font-size: 13px; text-align: center;">The authenticity of this document must be verified in the url {{ ENV('APP_URL') }}/certificados/validar/{{ $certificado->ds_hash_cer }}<p>
        </htmlpagefooter>

        <div class="corpo">
            <h2>Papers Reviewer Certificate</h2>
            <h1>{{ $certificado->participante->ds_nome_par }}</h1>
            <p>
                participated as papers reviewer at the XI International Congress on Knowledge and Innovation (ciKi 2021), held in Maringá, Brazil, in virtual modality on November 18th and 19th, 2021. 
            </p>
        </div>
    </body>
</html>