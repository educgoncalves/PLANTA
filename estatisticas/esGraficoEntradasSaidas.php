<?php 
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");

// Recuperando as informações do Aeroporto
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];

metaTagsBootstrap('');

// Parâmetros para o gráfico
$width=carregarGets('w',800);
$height=carregarGets('h',600);
$style='width: '.$width.'px; height: '.$height.'px;';
$titulo = 'Entradas e Saídas - (últimos 3 meses) - '.$siglaAeroporto;

// Pegando o periodo de 90 dias da data atual
$periodoInicio = dateTimeUTC($utcAeroporto)->modify('-7 month')->modify('first day of this month')->format('Y-m-d');  
$periodoFinal = dateTimeUTC($utcAeroporto)->modify('last day of this month')->format('Y-m-d'); 
?>
<html>
<head>
   <title><?php echo $_SESSION['plantaSistema']." - ".$titulo ?></title> 
   <style>
        /* CSS para fazer a imagem ocupar todo o espaco do body.*/
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; }
        img { width: 100%; height: 100%; object-fit: contain; /* Ou 'cover' */ }
        /* Estilo para a div que conterá o gráfico */
        .grafico { <?php echo $style ?> margin: auto; }
    </style>
</head>
<body>

<!-- Campos hidden -->
<!--***************************************************************** -->
<input type="hidden" id="hdTitulo" <?="value=\"{$titulo}\"";?>/> 
<input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
<input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
<input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
<input type="hidden" id="hdPeriodoInicio" <?="value=\"{$periodoInicio}\"";?>/>
<input type="hidden" id="hdPeriodoFinal" <?="value=\"{$periodoFinal}\"";?>/>

<!-- Campos para o gráfico -->
<!--***************************************************************** -->
<div class="grafico" id="grafico">
    <canvas id="cnvGrafico"></canvas>
</div>
<!-- <img id="imgGrafico" src="" alt="Imagem do Canvas" class="img-fluid">  -->
<!--***************************************************************** -->
<script src="../ativos/chart_443/chart.umd.min.js"></script>
<script src="../ativos/chart_443/chartjs-plugin-datalabels.min.js"></script>
<script src="../estatisticas/esFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        // Pega informacoes para o gráfico
        const grConfiguracao = (await esConfigurarGrupamentoEntradasSaidas("G-01"));
        const grGrupamento = grConfiguracao[0];

        const filtro = " AND st.idAeroporto = "+$("#hdAeroporto").val()+" AND (sm.movimento = 'ENT' OR sm.movimento = 'SAI')"+
                        " AND (DATE_FORMAT(sm.dhMovimento,'%Y-%m-%d')  >= '"+$("#hdPeriodoInicio").val()+"'"+
                        " AND DATE_FORMAT(sm.dhMovimento,'%Y-%m-%d') <= '"+$("#hdPeriodoFinal").val()+"')";
        const ordem = "st.id, sm.id";

        try {
            var dados = (await esDadosStatusEntradasSaidas(filtro, ordem, grGrupamento)); 
            if (dados != null) {
                await esGraficoStatusEntradasSaidas($("#hdTitulo").val(), dados,'cnvGrafico');
            }
        } catch (error) {
            document.getElementById("grafico").innerHTML = exibirErro(error);
        }    
    });
</script>
</body>
</html>