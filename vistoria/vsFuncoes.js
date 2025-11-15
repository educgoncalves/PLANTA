// Carregar tabela VISTORIA ITENS - a página deve ter divTituloTabela e divTabela
//
async function vsCarregarVistoriaItens(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");    
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=VistoriaItens&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Tipo</th><th></th><th>Número</th><th>Item</th><th>Situação</th>"+
                        (funcao.search('Cadastrar') != -1 ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Tipo</th><th>Número</th><th>Item</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (((funcao != 'CadastrarADM' && obj.idAeroporto != 0) || funcao == 'CadastrarADM') ? 
                                    "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+ obj.descTipo+"</a>" : obj.descTipo) + '</td>';
                    // Libera as ações de acordo com o grupo do Usuário
                    htmlTabela += '<td>' +
                                (funcao == 'CadastrarADM' ? 
                                    (obj.idAeroporto != 0 ? 
                                        "<a href='?evento=padronizar&id="+obj.id+"' title='Padronizar item'>"+obj.aeroporto+"</a>" : 
                                        "<a href='?evento=despadronizar&id="+obj.id+"' title='Despadronizar item'>"+obj.aeroporto+"</a>") : 
                                    obj.aeroporto) +'</td>';
                    
                    htmlTabela += '<td>'+obj.numero+'</td>'+
                                '<td>'+obj.item+'</td>'+
                                '<td>'+obj.descSituacao+'</td>';

                    // Libera as ações de acordo com o grupo do Usuário
                    if (funcao.search('Cadastrar') != -1) {
                        htmlTabela += '<td>'+
                                    '<a href="?evento=copiar&id='+obj.id+'">'+
                                    '<img src="../ativos/img/copiar.png" title="Copiar registro"/></a>'+
                                    (((funcao != 'CadastrarADM' && obj.idAeroporto != 0) || funcao == 'CadastrarADM') ?                                 
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>' : '') +
                                    '</td>';
                    }   
                    htmlTabela += '</tr>';
                    htmlImpressao += '<tr><td>'+obj.descTipo+'</td>'+
                                '<td>'+obj.numero+'</td>'+
                                '<td>'+obj.item+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Itens de Vistoria - ["+qtdRegistros+"]</H4>";
            divPagina.innerHTML = barraPaginacao(pagina, limite, qtdTotalRegistros);
            divTabela.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlTabela;
            divImpressao.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlImpressao;
        });
    } catch (error) {
        divTitulo.innerHTML = "";
        divPagina.innerHTML = "";
        divTabela.innerHTML = exibirErro(error);
        divImpressao.innerHTML = "";
    }          
    $('.carregando').hide();
};

// Carregar tabela VISTORIA PLANOS - a página deve ter divTituloTabela e divTabela
//
async function vsCarregarVistoriaPlanos(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");    
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=VistoriaPlanos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Número</th><th>Finalidade</th><th>Mapa</th><th>Início</th><th>Frequência</th><th>Quantidade</th>"+
                        "<th>Período</th><th>Situação</th>"+
                        (funcao.search('Cadastrar') != -1 ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Número</th><th>Finalidade</th><th>Mapa</th><th>Início</th><th>Frequência</th><th>Quantidade</th>"+
                        "<th>Período</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? 
                                    "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+ obj.numero+"</a>" : obj.numero)+
                                '</td>' +
                                '<td>'+obj.finalidade+'</td>'+   
                                '<td>'+
                                (funcao == 'Cadastrar' ? 
                                    "<a href='?evento=mapa&mapa="+obj.mapa+"'>"+obj.mapa+"</a>" : obj.mapa)+
                                '</td>'+
                                '<td>'+obj.dataInicio+'</td>'+
                                '<td>'+obj.descFrequencia+'</td>'+
                                '<td>'+obj.descQuantidade+'</td>'+
                                '<td>'+obj.descPeriodo+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td>'+
                                        '<a href="../vistoria/vsCadastrarPlanoItens.php?idPlano='+obj.id+'">'+
                                        '<img src="../ativos/img/alterar.png" title="Cadastro de itens"/></a>'+
                                        '<a href="?evento=agendar&id='+obj.id+'">'+
                                        '<img src="../ativos/img/calendario.png" title="Agendamento"/></a>'+
                                        '<a href="../vistoria/vsVisualizarAgendamentos.php?idPlano='+obj.id+'"">'+
                                        '<img src="../ativos/img/visualizar.png" title="Visualizar agendamento"/></a>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</td>' : '')+
                                '</tr>';

                        htmlImpressao += '<tr><td>'+obj.numero+'</td>'+
                                '<td>'+obj.finalidade+'</td>'+    
                                '<td>'+obj.mapa+'</td>'+
                                '<td>'+obj.dataInicio+'</td>'+
                                '<td>'+obj.descFrequencia+'</td>'+
                                '<td>'+obj.descQuantidade+'</td>'+
                                '<td>'+obj.descPeriodo+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Planos de Vistoria - ["+qtdRegistros+"]</H4>";
            divPagina.innerHTML = barraPaginacao(pagina, limite, qtdTotalRegistros);
            divTabela.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlTabela;
            divImpressao.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlImpressao;
        });
    } catch (error) {
        divTitulo.innerHTML = "";
        divPagina.innerHTML = "";
        divTabela.innerHTML = exibirErro(error);
        divImpressao.innerHTML = "";
    }
    $('.carregando').hide();
};

// Carregar tabela VISTORIA PLANOS x ITENS - a página deve ter divTituloTabela e divTabela
//
async function vsCarregarVistoriaPlanosItens(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");    
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var htmlHeader = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=VistoriaPlanosItens&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                $.each(dados, function(i, obj){
                    //
                    // Se primeiro registro imprime informações do Plano
                    //
                    ++qtdRegistros;
                    if (qtdRegistros == 1) {
                        //
                        // Header do Plano
                        //
                        htmlHeader = "Plano "+obj.numero+' - '+obj.finalidade;

                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><td>Data Início: "+obj.dataInicio+"</td>"+
                                '<td>Frequência: '+obj.descFrequencia+'</td>'+
                                '<td>Quantidade: '+obj.descQuantidade+'</td>'+
                                '<td>Período: '+obj.descPeriodo+'</td>'+
                                '<td>Situação: '+obj.descSituacao+'</td>'+
                                '</table>';

                        htmlTabela += "<br>";
                        htmlImpressao += htmlTabela;

                        // Carregar mapa
                        var classe = "";
                        // htmlTabela += "<table id='mapa' class='mapa mx-auto d-block' style='background-image: url(\"http://localhost/gear/arquivos/mapas/SBGL.jpg\");'>";
                        // for (let i = 1; i <= 20; i++) {
                        //     htmlTabela += "<tr>"
                        //     for (let j = 1; j <= 20; j++) {
                        //         classe = ((i+"-"+j == "15-10") ? 'local' : 'grade');
                        //         htmlTabela += "<td id='"+i+"_"+j+"' class='"+classe+"'></td>";
                        //     }
                        //     htmlTabela += "</tr>"
                        // }
                        // htmlTabela += "</table><br><br>";

                        // htmlImpressao += "<div><img src='http://localhost/gear/arquivos/mapas/SBGL.jpg'/></div><br><br>";

                        //
                        // Header dos itens do plano
                        //
                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                                "<th>Tipo</th><th>Número</th><th>Item</th>"+
                                (funcao.search('Cadastrar') != -1 ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                        htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                                "<th>Tipo</th><th>Número</th><th>Item</th>"+
                                "</tr></thead><tbody>";
                    }

                    // Itens do Plano
                    //
                    if (obj.tipo != null) {
                        htmlTabela += '<tr><td>'+obj.descTipo+'</td>'+
                            '<td>'+obj.numeroItem+'</td>'+
                            '<td>'+obj.item+'</td>'+
                            (funcao == 'Cadastrar' ? 
                                '<td>'+
                                    '<a href="?evento=excluir&id='+obj.idPlanoItem+'&idPlano='+obj.id+'"'+ 
                                    ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                    '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                '</td>' : '')+
                            '</tr>';

                        htmlImpressao += '<tr><td>'+obj.descTipo+'</td>'+
                            '<td>'+obj.numeroItem+'</td>'+
                            '<td>'+obj.item+'</td>'+
                            '</tr>';
                    }
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>"+htmlHeader+"</H4>";
            divPagina.innerHTML = barraPaginacao(pagina, limite, qtdTotalRegistros);
            divTabela.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlTabela;
            divImpressao.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlImpressao;
        });
    } catch (error) {
        divTitulo.innerHTML = "";
        divPagina.innerHTML = "";
        divTabela.innerHTML = exibirErro(error);
        divImpressao.innerHTML = "";
    }
    $('.carregando').hide();
};

// Carregar tabela VISTORIA PLANOS x AGENDAMENTOS - a página deve ter divTituloTabela e divTabela
//
async function vsCarregarVistoriaPlanosAgendamentos(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");    
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var htmlHeader = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=VistoriaPlanosAgendamentos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                $.each(dados, function(i, obj){
                    //
                    // Se primeiro registro imprime informações do Plano
                    //
                    ++qtdRegistros;
                    if (qtdRegistros == 1) {
                        //
                        // Header do Plano
                        //
                        htmlHeader = "Plano "+obj.numero+' - '+obj.finalidade;

                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><td>Data Início: "+obj.dataInicio+"</td>"+
                                '<td>Frequência: '+obj.descFrequencia+'</td>'+
                                '<td>Quantidade: '+obj.descQuantidade+'</td>'+
                                '<td>Período: '+obj.descPeriodo+'</td>'+
                                '<td>Situação: '+obj.descSituacao+'</td>'+
                                '</table>';

                        htmlTabela += "<br>";
                        htmlImpressao += htmlTabela;

                        //
                        // Header dos itens do plano
                        //
                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                                "<th>Número</th><th>Agendamento</th><th>Período</th><th>Execução</th><th>Usuário</th>"+
                                "<th>Ação</th></tr></thead><tbody>";
                        htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                                "<th>Número</th><th>Agendamento</th><th>Período</th><th>Execução</th><th>Usuário</th>"+
                                "</tr></thead><tbody>";
                    }

                    // Agendamentos do Plano
                    //
                    if (obj.numero != null) {
                        htmlTabela += '<tr><td>'+obj.numeroAgendamento+'</td>'+
                            '<td>'+obj.dataInicioAgendamento+'</td>'+
                            '<td>'+obj.descPeriodoAgendamento+'</td>'+
                            '<td>'+obj.dataExecucao+'</td>'+
                            '<td>'+obj.usuarioExecucao+'</td>'+
                            '<td>'+
                                '<a href="../vistoria/vsEmitirCheckList.php?&id='+obj.idAgendamento+'&idPlano='+obj.id+
                                '&execucao='+obj.dataExecucao+'" target="_blank">'+
                                '<img src="../ativos/img/checkList.png" title="Emitir checklist"/></a>' +
                                ((funcao == 'Cadastrar') ? 
                                    '<a href="../vistoria/vsManterResultadosItens.php?&id='+obj.idAgendamento+'&idPlano='+obj.id+
                                        '&execucao='+obj.dataExecucao+'">'+
                                        '<img src="../ativos/img/alterar.png" title="Manter resultados"/></a>' +
                                        (!isEmpty(obj.execucao) ?
                                        '<a href="?evento=excluirResultados&id='+obj.idAgendamento+'&idPlano='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão dos resultados?\');">'+
                                        '<img src="../ativos/img/apagar.png" title="Excluir resultados"/></a>'
                                        :
                                        '<a href="?evento=excluir&id='+obj.idAgendamento+'&idPlano='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>' )
                                        : '')+
                            '</td>'+
                            '</tr>';

                        htmlImpressao += '<tr><td>'+obj.numeroAgendamento+'</td>'+
                            '<td>'+obj.dataInicioAgendamento+'</td>'+
                            '<td>'+obj.descPeriodoAgendamento+'</td>'+
                            '<td>'+obj.dataExecucao+'</td>'+
                            '<td>'+obj.usuarioExecucao+'</td>'+
                            '</tr>';
                    }
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>"+htmlHeader+"</H4>";
            divPagina.innerHTML = barraPaginacao(pagina, limite, qtdTotalRegistros);
            divTabela.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlTabela;
            divImpressao.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlImpressao;
        });
    } catch (error) {
        divTitulo.innerHTML = "";
        divPagina.innerHTML = "";
        divTabela.innerHTML = exibirErro(error);
        divImpressao.innerHTML = "";
    }    
    $('.carregando').hide();
};

// Carregar tabela VISTORIA PARECER - a página deve ter divTituloTabela e divTabela
//
async function vsCarregarVistoriaParecer(funcao, base, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");    
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var htmlHeader = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao='+base+'&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                $.each(dados, function(i, obj){
                    //
                    // Se primeiro registro imprime informações do Plano
                    //
                    ++qtdRegistros;
                    if (qtdRegistros == 1) {
                        //
                        // Header do Plano
                        //
                        htmlHeader = "Itens "+(base == 'VistoriaItensResultados' ? "Vistoriados" : "a Vistoriar");

                        htmlTabela = "<div class='mt-2 fw-bold'>Plano: "+obj.numero+' - '+obj.finalidade+'<div>';
                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                                        '<td>Data Início: '+obj.dataInicio+'</td>'+
                                        '<td>Frequência: '+obj.descFrequencia+'</td>'+
                                        '<td>Quantidade: '+obj.descQuantidade+'</td>'+
                                        '<td>Período: '+obj.descPeriodo+'</td>'+
                                        '<td>Situação: '+obj.descSituacao+'</td>'+
                                        '</table>';
                        // Agendamento 
                        htmlTabela += "Agendamento: "+obj.numeroAgendamento;
                        htmlTabela += "<br>";
                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                                        '<td>Data Prevista: '+obj.dataInicioAgendamento+'</td>'+
                                        '<td>Período: '+obj.descPeriodoAgendamento+'</td>'+
                                        '<td>Execução: '+obj.dataExecucao+'</td>'+
                                        '<td>Executor: '+obj.usuarioExecucao+'</td>'+
                                        '</table>';
                        htmlImpressao += htmlTabela;

                        // Carregar mapa
                        var mapa = "../arquivos/mapas/"+obj.mapa;
                        htmlTabela += vsMontarHtmlMapaGrade(mapa,funcao,obj.idAgendamento,obj.id,obj.localAgendamento);

                        //
                        // Header dos itens do plano
                        //
                        htmlTabela += '<div class="row mt-5 mx-auto">';
                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                                "<th>Tipo</th><th>Número</th><th>Item</th><th>Parecer</th>"+
                                (funcao.search('Cadastrar') != -1 ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                        htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                                "<th>Tipo</th><th>Número</th><th>Item</th><th>Parecer</th>"+
                                "</tr></thead><tbody>";
                    }

                    // Itens do Plano
                    //
                    if (obj.tipo != null) {
                        htmlTabela += '<tr><td>'+obj.descTipo+'</td>'+
                            '<td>'+obj.numeroItem+'</td>'+
                            '<td>'+obj.item+'</td>'+
                            '<td>'+obj.parecer+'</td>'+
                            (funcao == 'Cadastrar' ? 
                                '<td>'+
                                    '<a href="?evento=recuperar&id='+obj.idAgendamento+'&idPlano='+obj.id+'&idItem='+obj.idItemParecer+'">'+
                                        '<img src="../ativos/img/alterar.png" title="Lançar Parecer"/></a>'+
                                '</td>' : '')+
                            '</tr>';

                        htmlImpressao += '<tr><td>'+obj.descTipo+'</td>'+
                            '<td>'+obj.numeroItem+'</td>'+
                            '<td>'+obj.item+'</td>'+
                            '<td>'+obj.parecer+'</td>'+
                            '</tr>';
                    }
                });
                htmlTabela += "</tbody></table></div>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>"+htmlHeader+"</H4>";
            divPagina.innerHTML = barraPaginacao(pagina, limite, qtdTotalRegistros);
            divTabela.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlTabela;
            divImpressao.innerHTML = (descricaoFiltro != '' ? descricaoFiltro+"<br><br>" : "") + htmlImpressao;
        });
    } catch (error) {
        divTitulo.innerHTML = "";
        divPagina.innerHTML = "";
        divTabela.innerHTML = exibirErro(error);
        divImpressao.innerHTML = "";
    }    
    $('.carregando').hide();
};

// FUNCOES AUXILIARES
//
// Montagem do mapa de grade 
//
function vsMontarHtmlMapaGrade(mapa,funcao,idAgendamento,idPlano,local){
    var html = "";
    html += '<div class="row mt-2 base mx-auto">';
    html += "<table id='mapa' class='mapa mx-auto d-block' style='background-image: url(\""+mapa+"\");'>";
    html += vsMontarMapaGrade(local,80,43);

    // Prepara o caption da tabela
    html +='<caption>'+
                '<div class="row">'+
                    '<div class="col-md-1 local"></div>' +
                    (funcao == 'Cadastrar' ? 
                        '<div class="col-md-11 fw-bold">Marque os locais que apresentaram problemas e clique '+
                            '<a id="salvarMapa" href="?evento=salvarMapa&id='+idAgendamento+'&idPlano='+idPlano+'">aqui</a> para salvar</div>' 
                    : 
                        '<div class="col-md-11 fw-bold">Locais que apresentaram problemas, conforme relatório abaixo.</div>')+
                '</div>'+
                '</caption>';

    html += "</table>";
    html += '</div>';
    return html;
}

function vsMontarMapaGrade(local,x,y) {
    var classe = "";
    var elemento = "";
    var html = "";
    local = (local === null ? '' : local);
    //var letras = ['','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','X','W','Y','Z'];
    for (let i = 1; i <= y; i++) {
        html += "<tr>";
        for (let j = 1; j <= x; j++) {
            //elemento = letras[i]+(j<=9 ? "0" : "")+j;
            elemento = (i<=9 ? "0" : "")+i+'-'+(j<=9 ? "0" : "")+j;
            classe = ((local.search(elemento) != -1) ? 'local' : 'grade');
            html += "<td id='"+elemento+"' class='"+classe+"'></td>";
        }
        html += "</tr>";
    }
    return html;
}
// ***************************************************************************************************