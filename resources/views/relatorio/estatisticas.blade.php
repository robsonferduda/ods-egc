<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório ODS</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #222;
            margin: 20px;
        }
        h3, h4 {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        td, th {
            padding: 8px;
            vertical-align: top;
        }
        img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ccc;
            margin-bottom: 8px;
        }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h3 class="center">Perfil ODS - Relatório de Diagnóstico</h3>
    <p class="center">{{ $periodo }}</p>
    <h4><strong>Centro</strong>:</h4>
    <table>
        <tr>
            <td>
                <p>Evolução por ODS</p>
                <img src="{{ $grafico_evolucao }}" alt="Gráfico Evolução ODS" />
            </td>
            <td>
                <p>Totais de documentos por ODS</p>
                <img src="{{ $grafico_total }}" alt="Gráfico Total ODS" />
            </td>
        </tr>
    </table>
    <!-- Adicione mais conteúdo conforme necessário -->
</body>
</html>