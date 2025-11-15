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
$titulo = 'Quadro de Movimentos - '.$siglaAeroporto;
?>
<html>
<head>
   <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title> 
   <style>
        /* CSS para fazer a imagem ocupar todo o espaco do body.*/
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; }
        img { width: 100%; height: 100%; object-fit: contain; /* Ou 'cover' */ }
        /* Estilo para a div que conterá o gráfico */
        .container-grafico { <?php echo $style ?> margin: auto; }
    </style>
</head>
<body>
<input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
<input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
<input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
<div class="container-grafico">
    <canvas id="grfOcupacao"></canvas>
</div>
<!-- <img id="imgOcupacao" src="" alt="Imagem do Canvas" class="img-fluid">  -->
<script src="../ativos/chart_443/chart.umd.min.js"></script>
<script src="../ativos/chart_443/chartjs-plugin-datalabels.min.js"></script>
<script>
  // Cria o gráfico
  // Obtém o contexto 2D do canvas
  const ctx = document.getElementById('grfOcupacao');
Chart.register(ChartDataLabels);
  new Chart(ctx, {
        type: 'pie', //'doughnut',
        data: {
            labels: ['Hangar', 'Pátio', 'Estacionamento', 'Isento'],
            datasets: [{
                label: 'Minha Distribuição',
                data: [300, 50, 100, 75],
                backgroundColor: [
                    'rgb(255, 99, 132)', // Cor para Categoria A
                    'rgb(54, 162, 235)',  // Cor para Categoria B
                    'rgb(255, 205, 86)',  // Cor para Categoria C
                    'rgb(75, 192, 192)'   // Cor para Categoria D
                ],
                hoverOffset: 4 // Efeito de destaque ao passar o mouse
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                  position: 'right',
                  labels: { // Opcional: Para mostrar o rótulo completo ao passar o mouse
                      usePointStyle: true,
                  }
                },
                tooltip: {
                  callbacks: {
                      // Exibe o rótulo completo no tooltip ao passar o mouse
                      label: function(context) {
                          let label = context.chart.data.labels[context.dataIndex] || '';
                          if (label) {
                              label += ': ' + context.formattedValue;
                          }
                          return label;
                      }
                  }
                },
                title: {
                  display: true,
                  text: 'Quadro de Ocupação - '+$("#hdSiglaAeroporto").val()
                },
                datalabels: {
                    // A cor do texto
                    color: '#fff',
                    // O formato do rótulo
                    formatter: (value, context) => {
                        const dataset = context.chart.data.datasets[0];
                        const total = dataset.data.reduce((a, b) => a + b, 0);
                        // Calcula o percentual e arredonda
                        const percent = Math.round((value / total) * 100);
                        return `${percent}%`;
                    },
                    
                    // Posição do rótulo (pode ser 'center', 'start', 'end')
                    anchor: 'center',
                    
                    // Fonte do texto
                    font: {
                        weight: 'bold'
                    }
                }

            }
        }
    });

//   // Converte o conteúdo do canvas para um URL de dados (imagem PNG)
//   const dataURL = ctx.toDataURL('image/png');
//   // Pega o elemento img
//   const image = document.getElementById('imgGraficoOcupacao');
//   // Atribui o URL de dados ao atributo src da imagem
//   image.src = dataURL;
</script>
</body>
</html>
<!-- 

// Dados do gráfico de pizza
const data = {
    labels: [
        'HTML',
        'CSS',
        'JavaScript'
    ],
    datasets: [{
        label: 'Uso de Linguagens Web',
        data: [300, 50, 100],
        backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'
        ],
        hoverOffset: 4
    }]
};

// Configuração do gráfico, incluindo o plugin
const config = {
    type: 'doughnut', // ou 'pie'
    data: data,
    options: {
        plugins: {
            datalabels: {
                // A cor do texto
                color: '#fff',
                
                // O formato do rótulo
                formatter: (value, context) => {
                    // Obtém o dataset e o total de todos os valores
                    const dataset = context.chart.data.datasets[0];
                    const total = dataset.data.reduce((acc, current) => acc + current, 0);
                    
                    // Calcula o percentual e arredonda
                    const percent = Math.round((value / total) * 100);
                    
                    // Retorna a string formatada com o símbolo de percentual
                    return `${percent}%`;
                },
                
                // Posição do rótulo (pode ser 'center', 'start', 'end')
                anchor: 'center',
                
                // Fonte do texto
                font: {
                    weight: 'bold',
                    size: 16
                }
            }
        }
    }
};

// Renderiza o gráfico
const myChart = new Chart(
    document.getElementById('myChart'),
    config
); -->