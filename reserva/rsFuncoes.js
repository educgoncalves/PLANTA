// Carregar tabela RESERVAS USUÁRIOS - a página deve ter divTituloTabela e divTabela
//
async function rsCarregarReservasUsuarios(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById("divPagina");    
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=ReservasUsuarios&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm''><thead class='table-info'><tr>"+
                        "<th>Usuário</th><th>Nome</th><th>Email</th><th>Fonte</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm' table-sm'><thead><tr>"+
                        "<th>Usuário</th><th>Nome</th><th>E-mail</th><th>Fonte</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.usuario+"</a>" : obj.usuario) + '</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.fonte+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=reset&id='+obj.id+'"'+ 
                                            ' onclick="return confirm(\'Confirma o reset da senha?\');">'+
                                            '<img src="../ativos/img/reset.png" title="Reset da senha"/></a>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                            ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                            '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.usuario+'</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.fonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Reservas Usuários - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela RESERVAS - a página deve ter divTituloTabela e divTabela
//
async function rsCarregarReservas(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById("divPagina");    
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var situacao = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=Reservas&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm''><thead class='table-info'><tr>"+
                        "<th>Reserva</th><th>Solicitação UTC</td><th>Matrícula</th><th>Origem UTC</th><th>POB</th><th>Destino UTC</th><th>Solicitante</th><th>Situação</th><th>Observação</th>"+
                        "<th>Envio UTC</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm' table-sm'><thead><tr>"+
                        "<th>Reserva</th><th>Solicitação UTC</td><th>Matrícula</th><th>Origem UTC</th><th>POB</th><th>Destino UTC</th><th>Solicitante</th><th>Situação</th><th>Observação</th>"+
                        "<th>Envio UTC</th>"+
                        "</tr></thead><tbody>";
                $.each(dados, function(i, obj){
                    // Destacar a situacao
                    situacao = rsDestacarSituacao(obj.situacao);

                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.reserva+"</a>" : obj.reserva) + '</td>'+
                                '<td>'+obj.dataHoraCadastro+'</td>'+
                                '<td>'+obj.matriculaCompleta+'</td>'+
                                '<td>'+obj.origem+' - '+obj.dataHoraChegada+'</td>'+
                                '<td>'+obj.pob+'</td>'+
                                '<td>'+obj.destino+' - '+obj.dataHoraPartida+'</td>'+
                                '<td>'+obj.usuarioCompleto+'</td>'+
                                '<td '+situacao+'>'+obj.descSituacao+'</td>'+
                                '<td>'+obj.observacao+(obj.enviar == "SIM" ? " (Aguardando envio)" : "")+'</td>'+
                                '<td>'+obj.dataHoraEnvio+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        // '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        //     ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        //     '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                        '<a href="?evento=visualizar&id='+obj.id+'">'+ 
                                            '<img src="../ativos/img/visualizar.png" title="Visualizar histórico"/></a>'+                                              
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.reserva+'</td>'+
                                '<td>'+obj.dataHoraCadastro+'</td>'+
                                '<td>'+obj.matriculaCompleta+'</td>'+
                                '<td>'+obj.origem+' - '+obj.dataHoraChegada+'</td>'+
                                '<td>'+obj.pob+'</td>'+
                                '<td>'+obj.destino+' - '+obj.dataHoraPartida+'</td>'+
                                '<td>'+obj.usuarioCompleto+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '<td>'+obj.observacao+(obj.enviar == "SIM" ? " (Aguardando envio)" : "")+'</td>'+
                                '<td>'+obj.dataHoraEnvio+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Reservas - ["+qtdRegistros+"]</H4>";
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

// Carregar VISUALIZAR RESERVAS HISTORICOS - a página deve chamar o modalVisualizar()
//
async function rsVisualizarReservasHistoricos(filtro = '', ordem = '') {
    $('.carregando').show();        
    var labelVisualizar = document.getElementById("labelVisualizar");
    var divVisualizar = document.getElementById("divVisualizar");
    var htmlTabela = '';
    var reservaAnterior = '';
    var situacao = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=ReservasHistoricos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Solicitação UTC</td><th>Matrícula</th><th>Origem UTC</th><th>POB</th><th>Destino UTC</th><th>Solicitante</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    if (obj.reserva != reservaAnterior){
                        // Destacar a situacao
                        situacao = rsDestacarSituacao(obj.situacao);

                        // Se opção de cadastrar e não faturado, libera chamada do formulário
                        htmlTabela += '<tr>'+
                                '<td>'+obj.dataHoraCadastro+'</td>'+
                                '<td>'+obj.matriculaCompleta+'</td>'+
                                '<td>'+obj.origem+' - '+obj.dataHoraChegada+'</td>'+
                                '<td>'+obj.pob+'</td>'+
                                '<td>'+obj.destino+' - '+obj.dataHoraPartida+'</td>'+
                                '<td>'+obj.usuarioCompleto+'</td>'+
                                '<td '+situacao+'>'+obj.descSituacao+
                                    (obj.dataHoraCadastroHistorico == '' && obj.observacao != '' ? ' - '+obj.observacao : '')+'</td>'+
                                "</tr></tbody></table>";
                                
                        // Finaliza a tabela com as informações da reserva e abre a de históricos
                        htmlTabela += 
                                "<table class='table table-striped table-hover table-bordered table-sm'><thead class='table-info'><tr>"+
                                "<th>Dh.Envio</th><th>Situação</th><th>Observação</th></tr></thead><tbody>";                              
                        reservaAnterior = obj.reserva;                            
                    }
                    htmlTabela += '<tr>'+
                        '<td>'+obj.dataHoraCadastroHistorico+'</td>'+
                        '<td>'+obj.descSituacaoHistorico+'</td>'+
                        '<td>'+obj.observacaoHistorico+'</td>'+
                        '</tr>';
                });
                htmlTabela += "</tbody></table>";
            }
            labelVisualizar.innerHTML = "<h5>Reserva "+reservaAnterior+"</h5>";
            divVisualizar.innerHTML = htmlTabela;
        });
    } catch (error) {
        divVisualizar.innerHTML = exibirErro(error);
    }        
    $('.carregando').hide();
};

// Funções AUXILIARES
//
function rsDestacarSituacao(situacao) {
    var htmlRetorno = '';
    switch (situacao) {
        case 'APR':
            htmlRetorno = "class='fw-bold table-success'";
        break;
        case 'AVN':
        case 'VEN':
        case 'PEN':
            htmlRetorno = "class='fw-bold table-warning'";
        break;
        default :
            htmlRetorno = "class='fw-bold table-danger'";
    }
    return htmlRetorno;
}
// ***************************************************************************************************