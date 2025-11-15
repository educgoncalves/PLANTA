
// Carregar POUSOS E DECOLAGENS - a página deve ter divTituloTabela, divTabela e cnvGrafico
//
async function esStatusPousosDecolagens(filtro = '', ordem = '', grupamento = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var qtdPousos = 0;
    var qtdDecolagens = 0;

    // Pega a configuracao do Grupamento
    const grConfiguracao = (await esConfigurarGrupamentoPousosDecolagens(grupamento));
    const grGrupamento = grConfiguracao[0];
    const grThs = grConfiguracao[1]; 
    const grTds = grConfiguracao[2]; 
    const grDescricao = grConfiguracao[3]; 

    try {
        var dados = (await esDadosStatusPousosDecolagens(filtro, ordem, grGrupamento, pagina, limite)); 
        if (dados != null) {
            // Montando a tabela de Filtros e Grupamentos
            htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                    "<thead class='table-info'><tr><th>Filtros</th><th>Grupamento</th></tr></thead>"+
                    "<tbody><tr><td>"+descricaoFiltro+"</td><td>"+grDescricao+"</td></tr></tbody></table><br>";
                
            // Montando a tabela de Dados
            htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                    "<thead class='table-info'><tr>";
            for (const th of grThs) { htmlTabela += '<th>'+th+'</th>'; }
            htmlTabela +="</tr></thead><tbody>";
            
            $.each(dados, function(i, obj){
                htmlTabela += '<tr>';
                for (const td of grTds) { htmlTabela += '<td style="text-align: center">'+obj[td]+'</td>'; }
                htmlTabela += '</tr>';

                qtdPousos += parseInt(obj.pousos);
                qtdDecolagens += parseInt(obj.decolagens);
            });

            // Total
            htmlTabela += '<tr>'+
                    '<td><strong>Totalização</strong></td>'+
                    '<td style="text-align: center"><strong>'+qtdPousos+'</strong></td>'+
                    '<td style="text-align: center"><strong>'+qtdDecolagens+'</strong></td>'+
                    '</tr>';
            qtdRegistros = dados.length;
            qtdTotalRegistros = dados[0]['total'];   

            // Média
            const mdPousos = Number((qtdPousos/qtdRegistros)).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2,maximumFractionDigits: 2});
            const mdDecolagens = Number((qtdDecolagens/qtdRegistros)).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2,maximumFractionDigits: 2});

            htmlTabela += '<tr>'+
                '<td><strong>Média</strong></td>'+
                '<td style="text-align: center"><strong>'+mdPousos+'</strong></td>'+
                '<td style="text-align: center"><strong>'+mdDecolagens+'</strong></td>'+
                '</tr></tbody></table>';
        }
        divTitulo.innerHTML = "<H4>Estatíscas de Pousos e Decolagens - ["+qtdRegistros+"]</H4>";
        divPagina.innerHTML = barraPaginacao(pagina, limite, qtdTotalRegistros);
        divTabela.innerHTML = "<br>"+htmlTabela;
                    
        await esGraficoStatusPousosDecolagens('', dados, 'cnvGrafico');

    } catch (error) {
        divTitulo.innerHTML = "";
        divPagina.innerHTML = "";
        divTabela.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};

async function esConfigurarGrupamentoPousosDecolagens(grupamento) {
    // Vetores de configuração dos grupamentos
    //
    const grupamentos = {
        "G-01": {
            grupamento : "SELECT CONCAT(YEAR(dhMovimento),' / ',descMes) as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Ano / Mês","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por ano e mês"
        },
        "G-02": {
            grupamento : "SELECT CONCAT(YEAR(dhMovimento),' / ',WEEKOFYEAR(dhMovimento)) as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Ano / Semana","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por ano e semana"
        },
        "G-03": {
            grupamento : "SELECT CONCAT(descMes,' / ',DAY(dhMovimento)) as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY MONTH(dhMovimento),DAY(dhMovimento) ORDER BY MONTH(dhMovimento),DAY(dhMovimento)",
            th: ["Mês / Dia","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por mês e dia"
        },
        "G-04": {
            grupamento : "SELECT descDiaSemana as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY DAYOFWEEK(dhMovimento) ORDER BY DAYOFWEEK(dhMovimento)",
            th: ["Dia da Semana","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por dia da semana"
        },
        "G-05": {
            grupamento : "SELECT DATE_FORMAT(dhMovimento,'%d/%m/%Y %H:%i') as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY dhMovimento ORDER BY dhMovimento",
            th: ["Data e Hora Movimento","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por data e hora do movimento"
        },
        "G-06": {
            grupamento : "SELECT descGrupo as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+                        
                        "GROUP BY label ORDER BY label",
            th: ["Grupo","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por grupo de voo"
        },
        "G-07": {
            grupamento : "SELECT operador as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Operador","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por operador aéreo"
        },
        "G-08": {
            grupamento : "SELECT equipamento as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Equipamento","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por equipamento"
        },
        "G-09": {
            grupamento : "SELECT descClasse as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Classe do Voo","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por classe do voo"
        },
        "G-10": {
            grupamento : "SELECT descNatureza as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Natureza do Voo","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por natureza do voo"
        },
        "G-11": {
            grupamento : "SELECT descServico as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Serviço Aéreo","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por serviço aéreo"
        },
        "G-12": {
            grupamento : "SELECT IFNULL(recurso,'Não definido') as label, SUM(pouso) as pousos, SUM(decolagem) as decolagens "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Posição do Voo","Pousos","Decolagens"],
            td: ["label","pousos","decolagens"],
            descricao: "Por posição do voo"
        },
    };
    return [grupamentos[grupamento].grupamento, grupamentos[grupamento].th, 
            grupamentos[grupamento].td, grupamentos[grupamento].descricao];
}

async function esDadosStatusPousosDecolagens(filtro = '', ordem = '', grupamento = '', pagina = 0, limite = 0) {
    var array = [];
    await $.getJSON('../suporte/suBuscar.php?funcao=EstatisticasPousosDecolagens&filtro='+encodeURIComponent(filtro)+
                    '&ordem='+ordem+'&grupamento='+grupamento+'&pagina='+pagina+'&limite='+limite, function (dados){
        array = dados;
    });  
    return array;
}

// Grafico POUSOS E DECOLAGENS - obrigatorio no formulario ter o canvas para o gráfico
//
async function esGraficoStatusPousosDecolagens(titulo, dados, cnvGrafico, imgGrafico = '') {
    // Passo 1: Extrai os labels e os valores do array de objetos
    const labels = (dados != null ? dados.map(item => item.label) : '');
    const pousos = (dados != null ? dados.map(item => item.pousos) : '');
    const decolagens = (dados != null ? dados.map(item => item.decolagens) : '');

    // Passo 2: Prepara o dataset
    const data = {
        labels: labels,
        datasets: [{
        label: 'Pousos',
        data: pousos,
        lineTension: 0,
        backgroundColor: '#007bff',
        borderColor: '#007bff',
        borderWidth: 4,
        pointBackgroundColor: '#007bff',
        },{
        label: 'Decolagens',
        data: decolagens,
        lineTension: 0,
        backgroundColor: '#ff7bff',
        borderColor: '#ff7bff',
        borderWidth: 4,
        pointBackgroundColor: '#ff7bff'
        }]
    }

    // Passo 3: Configura o gráfico
    const config = {
        type: 'line',
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
                    text: titulo
                }
            },
            scales: {
                x: {
                    stacked: false,  // Agrupa as barras de datasets diferentes no mesmo rótulo
                },
                y: {
                    beginAtZero: true,
                    title: {
                    display: true,
                    text: 'Movimentos'
                    }
                }
            }
        }
    };

    // Passo 4: Removendo o canvas
    const canvas = document.getElementById(cnvGrafico);
    const grafico = Chart.getChart(canvas);
    if (grafico) { grafico.destroy(); }

    // Passo 5: Renderiza o gráfico
    new Chart(canvas, config);

    // Passo 6: Gerar a imagem do gráfico
    if (imgGrafico !== "") {
        // Converte o conteúdo do canvas para um URL de dados (imagem PNG) e atribui ao source da imagem
        const dataURL = canvas.toDataURL('image/png');
        const image = document.getElementById(imgGrafico);
        image.src = dataURL;
    }
}

// Carregar ENTRADAS E SAIDAS - a página deve ter divTituloTabela, divTabela e o canvas cnvGrafico
//
async function esStatusEntradasSaidas(filtro = '', ordem = '', grupamento = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var qtdEntradas = 0;
    var qtdSaidas = 0;

    // Pega a configuracao do Grupamento
    const grConfiguracao = (await esConfigurarGrupamentoEntradasSaidas(grupamento));
    const grGrupamento = grConfiguracao[0];
    const grThs = grConfiguracao[1]; 
    const grTds = grConfiguracao[2]; 
    const grDescricao = grConfiguracao[3]; 

    try {
        var dados = (await esDadosStatusEntradasSaidas(filtro, ordem, grGrupamento, pagina, limite)); 
        if (dados != null) {
            // Montando a tabela de Filtros e Grupamentos
            htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                    "<thead class='table-info'><tr><th>Filtros</th><th>Grupamento</th></tr></thead>"+
                    "<tbody><tr><td>"+descricaoFiltro+"</td><td>"+grDescricao+"</td></tr></tbody></table><br>";
                
            // Montando a tabela de Dados
            htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                    "<thead class='table-info'><tr>";
            for (const th of grThs) { htmlTabela += '<th>'+th+'</th>'; }
            htmlTabela +="</tr></thead><tbody>";
            
            $.each(dados, function(i, obj){
                htmlTabela += '<tr>';
                for (const td of grTds) { htmlTabela += '<td style="text-align: center">'+obj[td]+'</td>'; }
                htmlTabela += '</tr>';

                qtdEntradas += parseInt(obj.entradas);
                qtdSaidas += parseInt(obj.saidas);
            });

            // Total
            htmlTabela += '<tr>'+
                    '<td><strong>Totalização</strong></td>'+
                    '<td style="text-align: center"><strong>'+qtdEntradas+'</strong></td>'+
                    '<td style="text-align: center"><strong>'+qtdSaidas+'</strong></td>'+
                    '</tr>';
            qtdRegistros = dados.length;
            qtdTotalRegistros = dados[0]['total'];   

            // Média
            const mdEntradas = Number((qtdEntradas/qtdRegistros)).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2,maximumFractionDigits: 2});
            const mdSaidas = Number((qtdSaidas/qtdRegistros)).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2,maximumFractionDigits: 2});

            htmlTabela += '<tr>'+
                '<td><strong>Média</strong></td>'+
                '<td style="text-align: center"><strong>'+mdEntradas+'</strong></td>'+
                '<td style="text-align: center"><strong>'+mdSaidas+'</strong></td>'+
                '</tr></tbody></table>';
        }
        divTitulo.innerHTML = "<H4>Estatíscas de Entradas e Saídas - ["+qtdRegistros+"]</H4>";
        divPagina.innerHTML = barraPaginacao(pagina, limite, qtdTotalRegistros);
        divTabela.innerHTML = "<br>"+htmlTabela;

        await esGraficoStatusEntradasSaidas('', dados,'cnvGrafico');

    } catch (error) {
        divTitulo.innerHTML = "";
        divPagina.innerHTML = "";
        divTabela.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};

async function esConfigurarGrupamentoEntradasSaidas(grupamento) {
    // Vetores de configuração dos grupamentos
    //
    const grupamentos = {
        "G-01": {
            grupamento : "SELECT CONCAT(YEAR(dhMovimento),' / ',descMes) as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Ano / Mês","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por ano e mês"
        },
        "G-02": {
            grupamento : "SELECT CONCAT(YEAR(dhMovimento),' / ',WEEKOFYEAR(dhMovimento)) as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Ano / Semana","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por ano e semana"
        },
        "G-03": {
            grupamento : "SELECT CONCAT(descMes,' / ',DAY(dhMovimento)) as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY MONTH(dhMovimento),DAY(dhMovimento) ORDER BY MONTH(dhMovimento),DAY(dhMovimento)",
            th: ["Mês / Dia","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por mês e dia"
        },
        "G-04": {
            grupamento : "SELECT descDiaSemana as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY DAYOFWEEK(dhMovimento) ORDER BY DAYOFWEEK(dhMovimento)",
            th: ["Dia da Semana","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por dia da semana"
        },
        "G-05": {
            grupamento : "SELECT DATE_FORMAT(dhMovimento,'%d/%m/%Y %H:%i') as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY dhMovimento ORDER BY dhMovimento",
            th: ["Data e Hora Movimento","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por data e hora do movimento"
        },
        "G-06": {
            grupamento : "SELECT descGrupo as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+                        
                        "GROUP BY label ORDER BY label",
            th: ["Grupo","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por grupo de voo"
        },
        "G-07": {
            grupamento : "SELECT operador as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Operador","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por operador aéreo"
        },
        "G-08": {
            grupamento : "SELECT equipamento as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Equipamento","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por equipamento"
        },
        "G-09": {
            grupamento : "SELECT descClasse as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Classe do Voo","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por classe do voo"
        },
        "G-10": {
            grupamento : "SELECT descNatureza as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Natureza do Voo","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por natureza do voo"
        },
        "G-11": {
            grupamento : "SELECT descServico as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Serviço Aéreo","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por serviço aéreo"
        },
        "G-12": {
            grupamento : "SELECT IFNULL(recurso,'Não definido') as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Posição do Voo","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por posição do voo"
        },
        "G-13": {
            grupamento : "SELECT descUtilizacao as label, SUM(entrada) as entradas, SUM(saida) as saidas "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Utilização da Posição","Entradas","Saídas"],
            td: ["label","entradas","saidas"],
            descricao: "Por utilização da posição"
        }
    };
    return [grupamentos[grupamento].grupamento, grupamentos[grupamento].th, 
            grupamentos[grupamento].td, grupamentos[grupamento].descricao];
}

async function esDadosStatusEntradasSaidas(filtro = '', ordem = '', grupamento = '', pagina = 0, limite = 0) {
    var array = [];
    await $.getJSON('../suporte/suBuscar.php?funcao=EstatisticasEntradasSaidas&filtro='+encodeURIComponent(filtro)+
                    '&ordem='+ordem+'&grupamento='+grupamento+'&pagina='+pagina+'&limite='+limite, function (dados){
        array = dados;
    });  
    return array;
}

// Grafico ENTRADAS E SAIDAS - obrigatorio no formulario ter o canvas para o gráfico
//
async function esGraficoStatusEntradasSaidas(titulo, dados, cnvGrafico, imgGrafico = '') {
    // Passo 1: Extrai os labels e os valores do array de objetos
    const labels = (dados != null ? dados.map(item => item.label) : '');
    const entradas = (dados != null ? dados.map(item => item.entradas) : '');
    const saidas = (dados != null ? dados.map(item => item.saidas) : '');

    // Passo 2: Prepara o dataset
    const data = {
        labels: labels,
        datasets: [{
        label: 'Entradas',
        data: entradas,
        lineTension: 0,
        backgroundColor: '#007bff',
        borderColor: '#007bff',
        borderWidth: 4,
        pointBackgroundColor: '#007bff',
        },{
        label: 'Saídas',
        data: saidas,
        lineTension: 0,
        backgroundColor: '#ff7bff',
        borderColor: '#ff7bff',
        borderWidth: 4,
        pointBackgroundColor: '#ff7bff'
        }]
    }

    // Passo 3: Configura o gráfico
    const config = {
        type: 'line',
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
                    text: titulo
                }
            },
            scales: {
                x: {
                    stacked: false,  // Agrupa as barras de datasets diferentes no mesmo rótulo
                },
                y: {
                    beginAtZero: true,
                    title: {
                    display: true,
                    text: 'Movimentos'
                    }
                }
            }
        }
    };
    
    // Passo 4: Removendo o canvas
    const canvas = document.getElementById(cnvGrafico);
    const grafico = Chart.getChart(canvas);
    if (grafico) { grafico.destroy(); }

    // Passo 5: Renderiza o gráfico
    new Chart(canvas, config);

    // Passo 6: Gerar a imagem do gráfico
    if (imgGrafico !== "") {
        // Converte o conteúdo do canvas para um URL de dados (imagem PNG) e atribui ao source da imagem
        const dataURL = canvas.toDataURL('image/png');
        const image = document.getElementById(imgGrafico);
        image.src = dataURL;
    }
}

// Carregar PASSAGEIROS - a página deve ter divTituloTabela, divTabela e o canvas cnvGrafico
//
async function esStatusPassageiros(filtro = '', ordem = '', grupamento = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var qtdDesembarques = 0;
    var qtdEmbarques = 0;
    var qtdTransito = 0;

    // Pega a configuracao do Grupamento
    const grConfiguracao = (await esConfigurarGrupamentoPassageiros(grupamento));
    const grGrupamento = grConfiguracao[0];
    const grThs = grConfiguracao[1]; 
    const grTds = grConfiguracao[2]; 
    const grDescricao = grConfiguracao[3]; 

    try {
        var dados = (await esDadosStatusPassageiros(filtro, ordem, grGrupamento, pagina, limite)); 
        if (dados != null) {
            // Montando a tabela de Filtros e Grupamentos
            htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                    "<thead class='table-info'><tr><th>Filtros</th><th>Grupamento</th></tr></thead>"+
                    "<tbody><tr><td>"+descricaoFiltro+"</td><td>"+grDescricao+"</td></tr></tbody></table><br>";
                
            // Montando a tabela de Dados
            htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                    "<thead class='table-info'><tr>";
            for (const th of grThs) { htmlTabela += '<th>'+th+'</th>'; }
            htmlTabela +="</tr></thead><tbody>";
            
            $.each(dados, function(i, obj){
                htmlTabela += '<tr>';
                for (const td of grTds) { htmlTabela += '<td style="text-align: center">'+obj[td]+'</td>'; }
                htmlTabela += '</tr>';

                qtdDesembarques += parseInt(obj.desembarques);
                qtdEmbarques += parseInt(obj.embarques);
                qtdTransito += parseInt(obj.transito)
            });

            // Total
            htmlTabela += '<tr>'+
                    '<td><strong>Totalização</strong></td>'+
                    '<td style="text-align: center"><strong>'+qtdDesembarques+'</strong></td>'+
                    '<td style="text-align: center"><strong>'+qtdEmbarques+'</strong></td>'+
                    '<td style="text-align: center"><strong>'+qtdTransito+'</strong></td>'+
                    '</tr>';
            qtdRegistros = dados.length;
            qtdTotalRegistros = dados[0]['total'];   

            // Média
            const mdDesembarques = Number((qtdDesembarques/qtdRegistros)).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2,maximumFractionDigits: 2});
            const mdEmbarques = Number((qtdEmbarques/qtdRegistros)).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2,maximumFractionDigits: 2});
            const mdTransito = Number((qtdTransito/qtdRegistros)).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2,maximumFractionDigits: 2});

            htmlTabela += '<tr>'+
                '<td><strong>Média</strong></td>'+
                '<td style="text-align: center"><strong>'+mdDesembarques+'</strong></td>'+
                '<td style="text-align: center"><strong>'+mdEmbarques+'</strong></td>'+
                '<td style="text-align: center"><strong>'+mdTransito+'</strong></td>'+
                '</tr></tbody></table>';
        }
        divTitulo.innerHTML = "<H4>Estatíscas de Passageiros - ["+qtdRegistros+"]</H4>";
        divPagina.innerHTML = barraPaginacao(pagina, limite, qtdTotalRegistros);
        divTabela.innerHTML = "<br>"+htmlTabela;

        await esGraficoStatusPassageiros('', dados,'cnvGrafico');

    } catch (error) {
        divTitulo.innerHTML = "";
        divPagina.innerHTML = "";
        divTabela.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};

async function esConfigurarGrupamentoPassageiros(grupamento) {
    // Vetores de configuração dos grupamentos
    //
    const grupamentos = {
        "G-01": {
            grupamento : "SELECT CONCAT(YEAR(dhMovimento),' / ',descMes) as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Ano / Mês","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por ano e mês"
        },
        "G-02": {
            grupamento : "SELECT CONCAT(YEAR(dhMovimento),' / ',WEEKOFYEAR(dhMovimento)) as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Ano / Semana","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por ano e semana"
        },
        "G-03": {
            grupamento : "SELECT CONCAT(descMes,' / ',DAY(dhMovimento)) as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY MONTH(dhMovimento),DAY(dhMovimento) ORDER BY MONTH(dhMovimento),DAY(dhMovimento)",
            th: ["Mês / Dia","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por mês e dia"
        },
        "G-04": {
            grupamento : "SELECT descDiaSemana as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY DAYOFWEEK(dhMovimento) ORDER BY DAYOFWEEK(dhMovimento)",
            th: ["Dia da Semana","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por dia da semana"
        },
        "G-05": {
            grupamento : "SELECT DATE_FORMAT(dhMovimento,'%d/%m/%Y %H:%i') as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY dhMovimento ORDER BY dhMovimento",
            th: ["Data e Hora Movimento","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por data e hora do movimento"
        },
        "G-06": {
            grupamento : "SELECT descGrupo as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+                        
                        "GROUP BY label ORDER BY label",
            th: ["Grupo","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por grupo de voo"
        },
        "G-07": {
            grupamento : "SELECT operador as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Operador","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por operador aéreo"
        },
        "G-08": {
            grupamento : "SELECT equipamento as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Equipamento","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por equipamento"
        },
        "G-09": {
            grupamento : "SELECT descClasse as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Classe do Voo","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por classe do voo"
        },
        "G-10": {
            grupamento : "SELECT descNatureza as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Natureza do Voo","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por natureza do voo"
        },
        "G-11": {
            grupamento : "SELECT descServico as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Serviço Aéreo","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por serviço aéreo"
        },
        "G-12": {
            grupamento : "SELECT IFNULL(recurso,'Não definido') as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Portão do Voo","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por portão do voo"
        },
        "G-13": {
            grupamento : "SELECT IFNULL(recurso,'Não definido') as label, "+
                            "SUM(desembarque) as desembarques, SUM(embarque) as embarques, SUM(transito) as transito "+
                        "FROM ([query]) T "+
                        "GROUP BY label ORDER BY label",
            th: ["Esteira do voo","Desembarques","Embarques","Trânsito"],
            td: ["label","desembarques","embarques","transito"],
            descricao: "Por esteira do voo"
        }
    };
    return [grupamentos[grupamento].grupamento, grupamentos[grupamento].th, 
            grupamentos[grupamento].td, grupamentos[grupamento].descricao];
}

async function esDadosStatusPassageiros(filtro = '', ordem = '', grupamento = '', pagina = 0, limite = 0) {
    var array = [];
    await $.getJSON('../suporte/suBuscar.php?funcao=EstatisticasPassageiros&filtro='+encodeURIComponent(filtro)+
                    '&ordem='+ordem+'&grupamento='+grupamento+'&pagina='+pagina+'&limite='+limite, function (dados){
        array = dados;
    });  
    return array;
}

// Grafico PASSAGEIROS - obrigatorio no formulario ter o canvas para o gráfico
//
async function esGraficoStatusPassageiros(titulo, dados, cnvGrafico, imgGrafico = '') {
    // Passo 1: Extrai os labels e os valores do array de objetos
    const labels = (dados != null ? dados.map(item => item.label) : '');
    const desembarques = (dados != null ? dados.map(item => item.desembarques) : '');
    const embarques = (dados != null ? dados.map(item => item.embarques) : '');
    const transito = (dados != null ? dados.map(item => item.transito) : '');

    // Passo 2: Prepara o dataset
    const data = {
        labels: labels,
        datasets: [{
        label: 'Desembarques',
        data: desembarques,
        lineTension: 0,
        backgroundColor: '#007bff',
        borderColor: '#007bff',
        borderWidth: 4,
        pointBackgroundColor: '#007bff',
        },{
        label: 'Embarques',
        data: embarques,
        lineTension: 0,
        backgroundColor:  '#ff7bff',
        borderColor: '#ff7bff',
        borderWidth: 4,
        pointBackgroundColor: '#ff7bff'
        },{
        label: 'Trânsito',
        data: transito,
        lineTension: 0,
        backgroundColor: '#f9984eff',
        borderColor: '#f9984eff',
        borderWidth: 4,
        pointBackgroundColor: '#f9984eff'
        }]
    }

    // Passo 3: Configura o gráfico
    const config = {
        type: 'line',
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
                    text: titulo
                }
            },
            scales: {
                x: {
                    stacked: false,  // Agrupa as barras de datasets diferentes no mesmo rótulo
                },
                y: {
                    beginAtZero: true,
                    title: {
                    display: true,
                    text: 'Passageiros'
                    }
                }
            }
        }
    };
    
    // Passo 4: Removendo o canvas
    const canvas = document.getElementById(cnvGrafico);
    const grafico = Chart.getChart(canvas);
    if (grafico) { grafico.destroy(); }

    // Passo 5: Renderiza o gráfico
    new Chart(canvas, config);

    // Passo 6: Gerar a imagem do gráfico
    if (imgGrafico !== "") {
        // Converte o conteúdo do canvas para um URL de dados (imagem PNG) e atribui ao source da imagem
        const dataURL = canvas.toDataURL('image/png');
        const image = document.getElementById(imgGrafico);
        image.src = dataURL;
    }
}
// ***************************************************************************************************