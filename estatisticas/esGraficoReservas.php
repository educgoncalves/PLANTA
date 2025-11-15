
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
$titulo = 'Quadro de Reservas';
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
<input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
<input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
<input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
<div class="container-grafico">
    <canvas id="grfReservas"></canvas>
</div>
<!-- <img id="imgReservas" src="" alt="Imagem do Canvas" class="img-fluid">  -->
<script src="../ativos/chart_443/chart.umd.min.js"></script>
<script src="../ativos/chart_443/chartjs-plugin-datalabels.min.js"></script>
<script>
    $(async function() {
    // //---------- CONFIGURAÇÕES DO GRÁFICO
    // // EIXO Y COMEÇA NO ZERO?
    // var begin_Y_at_zero = true;
    // // HABILITAR ANIMAÇÃO DO GRÁFICO
    // var enable_animations = true;
    // // MOSTRAR VALORES NO EIXO X?
    // var mostrar_eixo_x = true;
    // // COR DO TEXTO EIXOS X E Y
    // var cor_texto = "white";
    // // COR DAS LINHAS DO GRID
    // var cor_grid = "#1879ad";
    // // PREENCHER O ESPAÇÔ ENTRE O GRÁFICO E O EIXO X?
    // var fill_chart = false;

    // // Pega informacoes para o gráfico
    // var arrayLabels = [];
    // var arrayDados = [];
    // await $.getJSON('../suporte/suBuscar.php?funcao=ReservasSomaSituacao&filtro='+
    //         encodeURIComponent(" AND rs.idAeroporto = "+$("#hdAeroporto").val())+
    //         '&ordem=dm.descricao', function (dados){
    //     if (dados != null) {
    //         arrayDados = dados;
    //     }
    // });   
    
    // // Extrai os labels e os valores do array de objetos
    // const labels = arrayDados.map(item => item.descSituacao);
    // const valores = arrayDados.map(item => item.somatorio);

    // // Configuração do gráfico
    // const data = {
    //     labels: labels,
    //     datasets: [{
    //     label: 'Quantidade de Reservas - '+$("#hdSiglaAeroporto").val(),
    //     data: valores,
    //     // backgroundColor: [
    //     //   'rgba(255, 99, 132, 0.5)',
    //     //   'rgba(54, 162, 235, 0.5)',
    //     //   'rgba(255, 206, 86, 0.5)',
    //     //   'rgba(75, 192, 192, 0.5)',
    //     //   'rgba(153, 102, 255, 0.5)'
    //     // ],
    //     // borderColor: [
    //     //   'rgba(255, 99, 132, 1)',
    //     //   'rgba(54, 162, 235, 1)',
    //     //   'rgba(255, 206, 86, 1)',
    //     //   'rgba(75, 192, 192, 1)',
    //     //   'rgba(153, 102, 255, 1)'
    //     // ],
    //     borderWidth: 1
    //     }]
    // };

    // var options = {
    //     responsive: true,
    //     indexAxis: 'y',
    //     animation: enable_animations,
    //     plugins: { 
    //         legend: { position: 'top', labels: { fontSize: 16, boxWidth: 10 } },
    //         tooltip: { boxPadding: 3 }
    //     },
    //     scales: { 
    //             x: {
    //                 display: mostrar_eixo_x,
    //                 //ticks: { color: cor_texto },
    //                 grid: { display: true, color: cor_grid }
    //             },
    //             y: {
    //                 beginAtZero: begin_Y_at_zero,
    //                 //ticks: { color: cor_texto },
    //                 grid: { display: true, color: cor_grid } 
    //             }
    //     } 
    // }; 

    // var ctx = document.getElementById('grfReservas');
    // var myLineChart = new Chart(ctx, {
    //     type: 'bar',
    //     data: data,
    //     options: options
    // });

    //   // Converte o conteúdo do canvas para um URL de dados (imagem PNG)
    //   const dataURL = ctx.toDataURL('image/png');
    //   // Pega o elemento img
    //   const image = document.getElementById('imgGrafico01');
    //   // Atribui o URL de dados ao atributo src da imagem
    //   image.src = dataURL;

    //***************************************************************************************************************** */
        // Seus dados brutos (passo 1)
    // const dadosBrutos = [
    //   { cidade: 'São Paulo', mes: 'Jan', valor: 120 },
    //   { cidade: 'São Paulo', mes: 'Fev', valor: 150 },
    //   { cidade: 'Rio de Janeiro', mes: 'Jan', valor: 90 },
    //   { cidade: 'Rio de Janeiro', mes: 'Fev', valor: 110 },
    //   { cidade: 'Belo Horizonte', mes: 'Jan', valor: 80 },
    //   { cidade: 'Belo Horizonte', mes: 'Fev', valor: 100 }
    // ];

    // // Prepara os dados para o Chart.js (passo 2)
    // const labels = dadosBrutos.map(item => `${item.cidade} - ${item.mes}`);
    // const valores = dadosBrutos.map(item => item.valor);
    
    // // Lista de cores para as barras
    // const cores = ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(75, 192, 192, 0.5)'];
    
    // // A cor de cada barra pode ser definida com base na cidade
    // const backgroundColors = dadosBrutos.map(item => {
    //   if (item.cidade === 'São Paulo') return 'rgba(255, 99, 132, 0.5)';
    //   if (item.cidade === 'Rio de Janeiro') return 'rgba(54, 162, 235, 0.5)';
    //   if (item.cidade === 'Belo Horizonte') return 'rgba(75, 192, 192, 0.5)';
    //   return 'rgba(201, 203, 207, 0.5)'; // Cor padrão
    // });
    
    // // Configuração do gráfico
    // const data = {
    //   labels: labels,
    //   datasets: [{
    //     label: 'Valor de Vendas',
    //     data: valores,
    //     backgroundColor: backgroundColors,
    //     borderColor: backgroundColors.map(color => color.replace('0.5', '1')), // Versão opaca da cor
    //     borderWidth: 1
    //   }]
    // };

    // const config = {
    //   type: 'bar',
    //   data: data,
    //   options: {
    //     responsive: true,
    //     indexAxis: 'y',
    //     scales: {
    //         y: {
    //         beginAtZero: true,
    //         title: {
    //           display: true,
    //           text: 'Valor (em unidades)'
    //         }
    //       }
    //     },
    //     plugins: {
    //       title: {
    //         display: true,
    //         text: 'Vendas por Cidade e Mês'
    //       }
    //     }
    //   }
    // };

    // // Renderiza o gráfico
    // const meuGrafico = new Chart(
    //   document.getElementById('grfReservas'),
    //   config
    // );

    //************************************************************************************************************* */
    const dadosBrutos = [
      { cidade: 'São Paulo', mes: 'Janeiro', valor: 200 },
      { cidade: 'Rio de Janeiro', mes: 'Janeiro', valor: 150 },
      { cidade: 'São Paulo', mes: 'Fevereiro', valor: 220 },
      { cidade: 'Rio de Janeiro', mes: 'Fevereiro', valor: 180 },
      { cidade: 'São Paulo', mes: 'Março', valor: 250 },
      { cidade: 'Rio de Janeiro', mes: 'Março', valor: 210 },
      { cidade: 'Minas Gerais', mes: 'Janeiro', valor: 200 },
      { cidade: 'Brasília', mes: 'Janeiro', valor: 150 },
      { cidade: 'Minas Gerais', mes: 'Fevereiro', valor: 220 },
      { cidade: 'Brasília', mes: 'Fevereiro', valor: 180 },
      { cidade: 'Minas Gerais', mes: 'Março', valor: 250 },
      { cidade: 'Brasília', mes: 'Março', valor: 210 },
      { cidade: 'Recife', mes: 'Janeiro', valor: 200 },
      { cidade: 'João Pessoa', mes: 'Janeiro', valor: 150 },
      { cidade: 'Recife', mes: 'Fevereiro', valor: 220 },
      { cidade: 'João Pessoa', mes: 'Fevereiro', valor: 180 },
      { cidade: 'Recife', mes: 'Março', valor: 250 },
      { cidade: 'João Pessoa', mes: 'Março', valor: 210 },
    ];

    // Passo 1: Agrupa os dados
    const meses = [...new Set(dadosBrutos.map(item => item.mes))];
    const cidades = [...new Set(dadosBrutos.map(item => item.cidade))];

    // Passo 2: Prepara os datasets
    const dadosPorCidade = {};
    cidades.forEach(cidade => {
      dadosPorCidade[cidade] = new Array(meses.length).fill(0);
    });

    dadosBrutos.forEach(item => {
      const indexMes = meses.indexOf(item.mes);
      dadosPorCidade[item.cidade][indexMes] = item.valor;
    });

    const datasets = cidades.map(cidade => ({
      label: cidade,
      data: dadosPorCidade[cidade],
      //backgroundColor: cidade === 'São Paulo' ? 'rgba(54, 162, 235, 0.5)' : 'rgba(255, 99, 132, 0.5)',
      //borderColor: cidade === 'São Paulo' ? 'rgba(54, 162, 235, 1)' : 'rgba(255, 99, 132, 1)',
      borderWidth: 1
    }));

    // Passo 3: Configuração do gráfico
    const data = {
      labels: meses,
      datasets: datasets
    };

    const config = {
      type: 'bar',
      data: data,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Vendas por Mês e Cidade'
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
              text: 'Valor de Vendas'
            }
          }
        }
      }
    };

    // Passo 4: Renderiza o gráfico
    const meuGrafico = new Chart(
      document.getElementById('grfReservas'),
      config
    );
   
});
//--------------------------------------------------
</script>
</body>
</html>

  </script>
</body>
</html>