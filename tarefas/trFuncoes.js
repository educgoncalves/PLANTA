// Carregar tabela TAREFAS - a página deve ter divTituloTabela e divTabela
//
async function trCarregarTarefas(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Tarefas&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Código</th><th>Descrição</th><th>Tolerância (min)</th><th>Enviar Email</th><th>Últ.Execução</th><th>Modo</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Código</th><th>Descrição</th><th>Tolerância (min)</th><th>Enviar Email</th><th>Últ.Execução</th><th>Modo</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    // Decora a situacao
                    situacao = (obj.situacao == 'ATV' ? "class='fw-bold table-success'" : "class='fw-bold table-danger'");
                    tolerancia = (obj.tmpDiferenca <= obj.tmpTolerancia ? "class='fw-bold table-success'" : "class='fw-bold table-danger'");

                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.codigo+"</a>" : obj.codigo) + '</td>'+
                                '<td>'+obj.descricao+'</td>'+
                                '<td>'+obj.tmpTolerancia+'</td>'+
                                '<td>'+obj.descEmail+'</td>'+
                                '<td '+tolerancia+'>'+obj.dataHoraExecucao+'</td>'+
                                '<td>'+obj.descModo+'</td>'+
                                '<td '+situacao+'>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.codigo+'</td>'+
                                '<td>'+obj.descricao+'</td>'+
                                '<td>'+obj.tmpTolerancia+'</td>'+
                                '<td>'+obj.descEmail+'</td>'+
                                '<td>'+obj.dataHoraExecucao+'</td>'+
                                '<td>'+obj.descModo+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Tarefas - ["+qtdRegistros+"]</H4>";
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
// ***************************************************************************************************