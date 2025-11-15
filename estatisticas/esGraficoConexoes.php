
<?php 
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
metaTagsBootstrap('');

// Recuperando as informações do Aeroporto
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];

// Parâmetros para o gráfico
$width=carregarGets('w',800);
$height=carregarGets('h',600);
$style='width: '.$width.'px; height: '.$height.'px;';
$titulo = 'Quadro de Conexões';
?>
<html>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
   <style>
        /* CSS para fazer a imagem ocupar todo o espaco do body.*/
        body, html { margin: 0; padding: 0; width: 100%; height: 100%;  }
        img { width: 100%; height: 100%; object-fit: contain; /* Ou 'cover' */ }
        /* Estilo para a div que conterá o gráfico */
        .container-grafico { <?php echo $style ?> margin: auto; }
    </style>
</head>
<body>
<input type="hidden" id="hdTitulo" <?="value=\"{$titulo}\"";?>/> 
<input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
<input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
<input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
<div class="container-grafico">
    <canvas id="graficoConexoes"></canvas>
</div>
<!-- <img id="imgGraficoConexoes" src="" alt="Imagem do Canvas" class="img-fluid">  -->
<script src="../ativos/chart_443/chart.umd.min.js"></script>
<script src="../ativos/chart_443/chartjs-plugin-datalabels.min.js"></script>
<script>
    $(async function() {
        // Pega informacoes para o gráfico
        var arrayDados = [];
        await $.getJSON('../suporte/suBuscar.php?funcao=ConexoesSomaSituacao&filtro='+
                encodeURIComponent(" AND co.idAeroporto = "+$("#hdAeroporto").val())+
                '&ordem=dm.descricao', function (dados){
            if (dados != null) {
                arrayDados = dados;
            }
        });   

        
    
        // Passo 1: Extrai os labels e os valores do array de objetos
        const labels = arrayDados.map(item => item.descSituacao);
        const valores = arrayDados.map(item => item.somatorio);

        // Passo 2: Prepara o dataset
        const data = {
            labels: labels,
            datasets: [{ 
                label: '',
                data: valores,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Passo 3: Configura o gráfico
        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        display: true
                    },
                    title: {
                        display: true,
                        text: $("#hdTitulo").val()+' - '+$("#hdSiglaAeroporto").val()
                    }
                },
                scales: {
                    x: {
                        // Isso agrupa as barras de datasets diferentes no mesmo rótulo
                        stacked: false,
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                        display: true,
                        text: 'Conexões'
                        }
                    }
                }
            }
        };

        // Passo 4: Renderiza o gráfico
        const meuGrafico = new Chart(
            document.getElementById('graficoConexoes'),
            config
        );

        // Passo 5: Converte o conteúdo do canvas para um URL de dados (imagem PNG)
        // const dataURL = ctx.toDataURL('image/png');
        // const image = document.getElementById('imgGraficoConexoes');
        // image.src = dataURL;

    });
</script>
</body>
</html>
