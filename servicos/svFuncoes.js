// Carregar tabela LOGS - a página deve ter divTituloTabela e divTabela
//
async function svCarregarLogsAtividades(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Logs&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Data</th><th>Tabela</th><th>Operação</th><th>Site</th><th>Usuário</th><th>Registro</th><th>Comando</th><th>Observação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Data</th><th>Tabela</th><th>Operação</th><th>Site</th><th>Usuário</th><th>Registro</th><th>Comando</th><th>Observação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.cadastro+"</a>" : obj.cadastro) + '</td>'+
                                '<td>'+obj.tabela+'</td>'+
                                '<td>'+obj.operacao+'</td>'+
                                '<td>'+obj.site+'</td>'+
                                '<td>'+obj.usuario+'</td>'+
                                '<td>'+obj.registro+'</td>'+
                                '<td>'+obj.comando+'</td>'+
                                '<td>'+obj.observacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.cadastro+'</td>'+
                                '<td>'+obj.tabela+'</td>'+
                                '<td>'+obj.operacao+'</td>'+
                                '<td>'+obj.site+'</td>'+
                                '<td>'+obj.usuario+'</td>'+
                                '<td>'+obj.registro+'</td>'+
                                '<td>'+obj.comando+'</td>'+
                                '<td>'+obj.observacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];
            }
            divTitulo.innerHTML = "<H4>Logs de Atividades - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela NOTIFICACOES - a página deve ter divTituloTabela e divTabela
//
async function svCarregarNotificacoes(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Notificacoes&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Data</th><th>Sistema</th><th>Aeroporto</th><th>Notificação</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Data</th><th>Sistema</th><th>Aeroporto</th><th>Notificação</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    // Decora a situacao
                    switch (obj.situacao) {
                        case 'LDS':
                            situacao = "class='fw-bold table-success'";
                        break;
                        case 'NLD':
                            situacao = "class='fw-bold table-danger'";
                        break;
                        default :
                            situacao = "class='fw-bold table-warning'";
                    }
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.dataHoraCadastro+"</a>" : obj.dataHoraCadastro) + '</td>'+
                                '<td>'+obj.sistema+'</td>'+
                                '<td>'+obj.aeroporto+'</td>'+
                                '<td>'+obj.notificacao+'</td>'+
                                '<td '+situacao+'>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.dataHoraCadastro+'</td>'+
                                '<td>'+obj.sistema+'</td>'+
                                '<td>'+obj.aeroporto+'</td>'+
                                '<td>'+obj.notificacao+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];
            }
            divTitulo.innerHTML = "<H4>Notificações - ["+qtdRegistros+"]</H4>";
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