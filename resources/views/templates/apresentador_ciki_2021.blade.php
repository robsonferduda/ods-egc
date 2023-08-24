<html>
    <head>
        <style>
            body { background-image: url({{ ENV('ASSET_URL').'/img/fundos/certificado_resized.png' }}); background-position: bottom right; background-repeat: no-repeat; }
            .corpo { text-align: center; padding-top: 150px; min-height: 500px; }
            .corpo p { font-size: 17px; }
            .corpo h1 { padding-bottom: 12px; }
            .corpo h2 { padding-bottom: 12px; }

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
            <h2>Article Presentation Certificate</h2>
            <p>
                This is to certify that the article entitled
                <strong>"{{ $certificado->metadados->where('label_metadado_cem','#titulo')->first()->valor_metadado_cem }}"</strong> 
                authored by 
                <strong>{{ $certificado->metadados->where('label_metadado_cem','#autores')->first()->valor_metadado_cem }}</strong> 
                was accepted, presented and published at the 
                XI International Congress of Knowledge and Innovation - ciKi 2021 (virtual modality, November 18 and 19, 2021)
            </p>
        </div>
    </body>
</html>