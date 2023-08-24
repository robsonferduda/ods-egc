<html>
    <head>
        <style>
            body { background-image: url({{ ENV('ASSET_URL').'/img/fundos/ceurs.jpg' }}); background-position: bottom right; background-repeat: no-repeat; }
            .corpo { text-align: center; padding-top: 120px; min-height: 500px; }
            .corpo p { font-size: 14px; }
            .corpo h1 { padding-bottom: 8px; }
            .corpo h2 { padding-bottom: 8px; }

            .rodape { text-align: center; }
            .rodape p {font-size: 12px; }

            @page {
                margin: 10%;
                margin-header: 3mm; 
	            margin-footer: 3mm; 
                background-image: url({{ ENV('ASSET_URL').'/img/fundos/ceurs.jpg' }}); background-position: bottom right; background-repeat: no-repeat;

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
            <p style="font-size: 12px; text-align: center; margin-top: 5px;">A autenticação deste documento pode ser verificada em https://certificado.studiokem.ufsc.br/certificados/validacao/{{ $certificado->ds_hash_cer }}<p>
        </htmlpagefooter>

        <div class="corpo">
            <h2>Certificamos que</h2>
            <h1>{{ $certificado->participante->ds_nome_par }}</h1>
            <p>
                Atuou como Participante no curso de <strong>CAPACITAÇÃO EM MUNICIPALIZAÇÃO DA AGENDA 2030</strong>,
            </p>
            <p>
                realizado no período de <strong>03/06/2022</strong> a <strong>15/10/2022</strong>, com carga horária de <strong>10</strong> horas.
            </p>
            <p>
                Este curso está amparado pelo projeto de extensão "Curso Introdutório à Municipalização da Agenda 2030".
            </p>

            <h3>Tópicos Abordados</h3>
            <p>Introdução à Agenda 2030; Gestão de Cidades; Municipalização da Agenda 2030.</p>
        </div>
    </body>
</html>