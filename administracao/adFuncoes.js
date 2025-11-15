// Carregar tabela USUÁRIOS - a página deve ter divTituloTabela e divTabela
//
async function adCarregarUsuarios(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Usuarios&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm''><thead class='table-info'><tr>"+
                        "<th>Usuário</th><th>Nome</th><th>Celular</th><th>Email</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm' table-sm'><thead><tr>"+
                        "<th>Usuário</th><th>Nome</th><th>Celular</th><th>E-mail</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.usuario+"</a>" : obj.usuario) + '</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.celular+'</td>'+
                                '<td>'+obj.email+'</td>'+
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
                                '<td>'+obj.celular+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Usuários - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela ACESSOS - a página deve ter divTituloTabela e divTabela
//
async function adCarregarAcessos(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Acessos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm''><thead class='table-info'><tr>"+
                        "<th>Usuário</th><th>Aeroporto</th><th>Sistema</th><th>Grupo</th><th>Preferencial</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm' table-sm'><thead><tr>"+
                        "<th>Usuário</th><th>Aeroporto</th><th>Sistema</th><th>Grupo</th><th>Preferencial</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.usuarioCompleto+"</a>" : obj.usuarioCompleto) + '</td>'+
                                '<td>'+obj.aeroportoCompleto+'</td>'+
                                '<td>'+obj.sistemaCompleto+'</td>'+
                                '<td>'+obj.grupoCompleto+'</td>'+
                                '<td>'+obj.descPreferencial+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.usuarioCompleto+'</td>'+
                                '<td>'+obj.aeroportoCompleto+'</td>'+
                                '<td>'+obj.sistemaCompleto+'</td>'+                            
                                '<td>'+obj.grupoCompleto+'</td>'+
                                '<td>'+obj.descPreferencial+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];
            }
            divTitulo.innerHTML = "<H4>Acessos - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela RESTRIÇÔES - a página deve ter divTituloTabela e divTabela
//
async function adCarregarRestricoes(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Restricoes&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Formulario</th><th>Aeroporto</th><th>Grupo</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Formulario</th><th>Aeroporto</th><th>Grupo</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.formularioCompleto+"</a>" : obj.formularioCompleto) + '</td>'+
                                '<td>'+obj.aeroportoCompleto+'</td>'+
                                '<td>'+obj.grupoCompleto+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.formularioCompleto+'</td>'+
                                '<td>'+obj.aeroportoCompleto+'</td>'+
                                '<td>'+obj.grupoCompleto+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Restrições para Formulários - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela CLIENTES - a página deve ter divTituloTabela e divTabela
//
async function adCarregarClientes(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Clientes&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'>"+
                        "<tr>"+
                        "<th rowspan=2 scope='col'>Aeroporto</th>"+
                        "<th rowspan=2 scope='col'>Sistema</th>"+
                        "<th rowspan=2 scope='col'>Celular</th>"+
                        "<th rowspan=2 scope='col'>Qtd. Conexões</th>"+
                        "<th rowspan=2 scope='col'>Registros por Página</th>"+
                        "<th rowspan=2 scope='col'>Grava Debug</th>"+
                        "<th colspan=3>Tolerâncias (min)</th>"+
                        "<th colspan=3>UTC</th>"+
                        "<th colspan=2>Taxiamento (min)</th>"+
                        "<th colspan=2>Refresh (seg)</th>"+    
                        "<th rowspan=2 scope='col'>Categoria</th>"+
                        "<th rowspan=2 scope='col'>Tipo Operador</th>"+
                        "<th rowspan=2 scope='col'>AVSEC</th>"+
                        "<th rowspan=2 scope='col'>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th rowspan=2 scope='col'>Ação</th>" : "")+
                        "</tr>"+
                        "<tr>"+
                        "<th colspan=1>Isenção</th>"+"<th colspan=1>Reserva</th>"+"<th colspan=1>Retorno</th>"+
                        "<th colspan=1>Diferença</th>"+"<th colspan=1>Abertura</th>"+"<th colspan=1>Fechamento</th>"+
                        "<th colspan=1>Gr.I</th>"+"<th colspan=1>Gr.II</th>"+
                        "<th colspan=1>Página</th>"+"<th colspan=1>Tela</th>"+
                        "</tr>"+
                        "</thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'>"+
                        "<tr>"+
                        "<th rowspan=2 scope='col'>Aeroporto</th>"+
                        "<th rowspan=2 scope='col'>Sistema</th>"+
                        "<th rowspan=2 scope='col'>Celular</th>"+
                        "<th rowspan=2 scope='col'>Qtd. Conexões</th>"+
                        "<th rowspan=2 scope='col'>Registros por Página</th>"+
                        "<th rowspan=2 scope='col'>Grava Debug</th>"+
                        "<th colspan=3>Tolerâncias (seg)</th>"+
                        "<th colspan=3>UTC</th>"+
                        "<th colspan=2>Taxiamento (min)</th>"+
                        "<th colspan=2>Refresh (seg)</th>"+
                        "<th rowspan=2 scope='col'>Categoria</th>"+
                        "<th rowspan=2 scope='col'>Tipo Operador</th>"+
                        "<th rowspan=2 scope='col'>AVSEC</th>"+
                        "<th rowspan=2 scope='col'>Situação</th>"+
                        "</tr>"+
                        "<tr>"+
                        "<th colspan=1>Isenção</th>"+"<th colspan=1>Reserva</th>"+"<th colspan=1>Retorno</th>"+
                        "<th colspan=1>Diferença</th>"+"<th colspan=1>Abertura</th>"+"<th colspan=1>Fechamento</th>"+
                        "<th colspan=1>Gr.I</th>"+"<th colspan=1>Gr.II</th>"+
                        "<th colspan=1>Página</th>"+"<th colspan=1>Tela</th>"+
                        "</tr>"+
                        "</thead><tbody>";                    

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.aeroporto+"</a>" : obj.aeroporto) + '</td>'+
                                '<td>'+obj.sistema+'</td>'+
                                '<td>'+obj.celular+'</td>'+
                                '<td>'+obj.conexoes+'</td>'+
                                '<td>'+obj.regPorPagina+'</td>'+
                                '<td>'+obj.descDebug+'</td>'+
                                '<td>'+obj.tmpIsencao+'</td>'+
                                '<td>'+obj.tmpReserva+'</td>'+
                                '<td>'+obj.tmpRetorno+'</td>'+
                                '<td>'+obj.utc+'</td>'+
                                '<td>'+obj.horaAbertura+'</td>'+
                                '<td>'+obj.horaFechamento+'</td>'+
                                '<td>'+obj.tmpTaxiG1+'</td>'+
                                '<td>'+obj.tmpTaxiG2+'</td>'+
                                '<td>'+obj.tmpRefreshPagina+'</td>'+
                                '<td>'+obj.tmpRefreshTela+'</td>'+                                
                                '<td>'+obj.descCategoria+'</td>'+
                                '<td>'+obj.tipoOperador+'</td>'+
                                '<td>'+obj.avsec+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=copiar&id='+obj.id+'">'+
                                        '<img src="../ativos/img/copiar.png" title="Copiar registro"/></a>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.aeroporto+'</td>'+
                                '<td>'+obj.sistema+'</td>'+
                                '<td>'+obj.celular+'</td>'+                            
                                '<td>'+obj.conexoes+'</td>'+
                                '<td>'+obj.regPorPagina+'</td>'+
                                '<td>'+obj.descDebug+'</td>'+
                                '<td>'+obj.tmpIsencao+'</td>'+
                                '<td>'+obj.tmpReserva+'</td>'+
                                '<td>'+obj.tmpRetorno+'</td>'+
                                '<td>'+obj.utc+'</td>'+
                                '<td>'+obj.horaAbertura+'</td>'+
                                '<td>'+obj.horaFechamento+'</td>'+ 
                                '<td>'+obj.tmpTaxiG1+'</td>'+
                                '<td>'+obj.tmpTaxiG2+'</td>'+      
                                '<td>'+obj.tmpRefreshPagina+'</td>'+
                                '<td>'+obj.tmpRefreshTela+'</td>'+                                                            
                                '<td>'+obj.descCategoria+'</td>'+
                                '<td>'+obj.tipoOperador+'</td>'+
                                '<td>'+obj.avsec+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Clientes - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela RECURSOS - a página deve ter divTituloTabela e divTabela
//
async function adCarregarRecursos(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Recursos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Tipo</th><th>Identificação</th><th>Descrição</th><th>Utilização</th><th>Natureza</th><th>Classe</th>"+
                        "<th>Sentido</th><th>Capacidade</th><th>Unidade</th><th>Evergadura</th><th>Comprimento</th><th>Direita</th>"+
                        "<th>Esquerda</th><th>Grupamento</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Tipo</th><th>Identificação</th><th>Descrição</th><th>Utilização</th><th>Natureza</th><th>Classe</th>"+
                        "<th>Sentido</th><th>Capacidade</th><th>Unidade</th><th>Evergadura</th><th>Comprimento</th><th>Direita</th>"+
                        "<th>Esquerda</th><th>Grupamento</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.descTipo+"</a>" : obj.descTipo) + '</td>'+
                                '<td>'+obj.recurso+'</td>'+
                                '<td>'+obj.descricao+'</td>'+
                                '<td>'+obj.descUtilizacao+'</td>'+
                                '<td>'+obj.descNatureza+'</td>'+
                                '<td>'+obj.descClasse+'</td>'+
                                '<td>'+obj.descSentido+'</td>'+
                                '<td>'+obj.capacidade+'</td>'+
                                '<td>'+obj.unidade+'</td>'+
                                '<td>'+Number(obj.envergadura).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+Number(obj.comprimento).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+obj.descDireita+'</td>'+
                                '<td>'+obj.descEsquerda+'</td>'+
                                '<td>'+obj.descGrupamento+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.descTipo+'</td>'+
                                '<td>'+obj.recurso+'</td>'+
                                '<td>'+obj.descricao+'</td>'+
                                '<td>'+obj.descUtilizacao+'</td>'+
                                '<td>'+obj.descNatureza+'</td>'+
                                '<td>'+obj.descClasse+'</td>'+
                                '<td>'+obj.descSentido+'</td>'+                            
                                '<td>'+obj.capacidade+'</td>'+
                                '<td>'+obj.unidade+'</td>'+
                                '<td>'+Number(obj.envergadura).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+Number(obj.comprimento).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+obj.descDireita+'</td>'+
                                '<td>'+obj.descEsquerda+'</td>'+
                                '<td>'+obj.descGrupamento+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Recursos - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela TARIFAS - a página deve ter divTituloTabela e divTabela
//
async function adCarregarTarifas(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Tarifas&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'>"+
                        "<tr>"+
                        "<th rowspan=3 scope='col'>Aeroporto</th>"+
                        "<th rowspan=3 scope='col'>Grupo</th>"+
                        "<th rowspan=3 scope='col'>Faixa PMD</th>"+
                        "<th colspan=6>Valores fixos</th>"+"<th colspan=6>Valores variáveis</th>"+
                        "<th rowspan=3 scope='col'>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th rowspan=3 scope='col'>Ação</th>" : "")+
                        "</tr>"+
                        "<tr>"+
                        "<th colspan=3>Dométicos</th>"+"<th colspan=3>Internacionais</th>"+
                        "<th colspan=3>Dométicos</th>"+"<th colspan=3>Internacionais</th>"+
                        "</tr>"+
                        "<tr>"+
                        "<th>TPO</th><th>TPM</th><th>TPE</th><th>TPO</th><th>TPM</th><th>TPE</th>"+
                        "<th>TPO</th><th>TPM</th><th>TPE</th><th>TPO</th><th>TPM</th><th>TPE</th>"+
                        "</tr>"+
                        "</thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                "<tr>"+
                "<th rowspan=3 scope='col'>Aeroporto</th>"+
                "<th rowspan=3 scope='col'>Grupo</th>"+
                "<th rowspan=3 scope='col'>Faixa PMD</th>"+
                "<th colspan=6>Valores fixos</th>"+"<th colspan=6>Valores variáveis</th>"+
                "<th rowspan=3 scope='col'>Situação</th>"+
                "</tr>"+
                "<tr>"+
                "<th colspan=3>Dométicos</th>"+"<th colspan=3>Internacionais</th>"+
                "<th colspan=3>Dométicos</th>"+"<th colspan=3>Internacionais</th>"+
                "</tr>"+
                "<tr>"+
                "<th>TPO</th><th>TPM</th><th>TPE</th><th>TPO</th><th>TPM</th><th>TPE</th>"+
                "<th>TPO</th><th>TPM</th><th>TPE</th><th>TPO</th><th>TPM</th><th>TPE</th>"+
                "</tr>"+
                "</thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr>'+
                                '<td>'+obj.aeroportoCompleto+'</td>'+
                                '<td>'+obj.descGrupo+'</td>'+
                                '<td>'+(funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.faixaCompleta+"</a>" : obj.faixaCompleta) + '</td>'+
                                "<td align='right'>"+Number(obj.domTPOF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPMF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPEF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPOF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPMF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPEF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPO).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPM).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPE).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPO).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPM).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPE).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar'? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.aeroportoCompleto+'</td>'+
                                '<td>'+obj.descGrupo+'</td>'+
                                '<td>'+obj.faixaCompleta+'</td>'+
                                "<td align='right'>"+Number(obj.domTPOF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPMF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPEF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPOF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPMF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPEF).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPO).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPM).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.domTPE).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPO).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPM).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.intTPE).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];
            }
            divTitulo.innerHTML = "<H4>Tarifas - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela MOVIMENTOS - a página deve ter divTituloTabela e divTabela
//
async function adCarregarMovimentos(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Movimentos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Movimento</th><th>Descrição</th><th>Operação</th><th>Ordem</th><th>Sucessora</th><th>Antes(min)</th><th>Depois(min)</th>"+
                        "<th>Antecessoras</th><th>Destaque</th><th>Alerta(min)</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Movimento</th><th>Descrição</th><th>Operação</th><th>Ordem</th><th>Sucessora</th><th>Antes(min)</th><th>Depois(min)</th>"+
                        "<th>Antecessoras</th><th>Destaque</th><th>Alerta(min)</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.movimento+"</a>" : obj.movimento) + '</td>'+
                                '<td>'+obj.descricao+'</td>'+
                                '<td>'+obj.descOperacao+'</td>'+
                                '<td>'+obj.ordem+'</td>'+
                                '<td>'+obj.sucessora+'</td>'+
                                '<td>'+obj.antes+'</td>'+
                                '<td>'+obj.depois+'</td>'+
                                '<td>'+obj.antecessoras+'</td>'+
                                '<td class="table-'+obj.destaque+'">'+obj.descDestaque+'</td>'+
                                '<td>'+obj.alerta+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.movimento+'</td>'+
                                '<td>'+obj.descricao+'</td>'+
                                '<td>'+obj.descOperacao+'</td>'+
                                '<td>'+obj.ordem+'</td>'+
                                '<td>'+obj.sucessora+'</td>'+
                                '<td>'+obj.antes+'</td>'+
                                '<td>'+obj.depois+'</td>'+
                                '<td>'+obj.antecessoras+'</td>'+
                                '<td>'+obj.descDestaque+'</td>'+
                                '<td>'+obj.alerta+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Movimentos - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela MENUS - a página deve ter divTituloTabela e divTabela
//
async function adCarregarMenus(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=MenusFormulario&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Sistema</th><th>Tipo</th><th>Módulo</th><th>Formulário</th><th>Descrição</th><th>Href</th><th>Target</th><th>SVG</th><th>Ordenação</th><th>Atalho</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Sistema</th><th>Tipo</th><th>Módulo</th><th>Formulário</th><th>Descrição</th><th>Href</th><th>Target</th><th>SVG</th><th>Ordenação</th><th>Atalho</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.sistema+"</a>" : obj.sistema) + '</td>'+
                                '<td>'+obj.descTipo+'</td>'+
                                '<td>'+obj.modulo+'</td>'+
                                '<td>'+obj.formulario+'</td>'+
                                '<td>'+obj.descricao+'</td>'+
                                '<td>'+obj.href+'</td>'+
                                '<td>'+obj.target+'</td>'+
                                '<td>'+obj.iconeSVG+'</td>'+
                                '<td>'+obj.ordem+'</td>'+
                                '<td>'+obj.descAtalho+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=copiar&id='+obj.id+'">'+
                                        '<img src="../ativos/img/copiar.png" title="Copiar registro"/></a>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.sistema+'</td>'+
                                '<td>'+obj.descTipo+'</td>'+
                                '<td>'+obj.modulo+'</td>'+
                                '<td>'+obj.formulario+'</td>'+
                                '<td>'+obj.descricao+'</td>'+
                                '<td>'+obj.href+'</td>'+
                                '<td>'+obj.target+'</td>'+
                                '<td>'+obj.iconeSVG+'</td>'+
                                '<td>'+obj.ordem+'</td>'+
                                '<td>'+obj.descAtalho+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Menus - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela PROPAGANDAS - a página deve ter divTituloTabela e divTabela
//
async function adCarregarPropagandas(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Propagandas&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Empresa</th><th>Propaganda</th><th>Dt.Início</th><th>Dt.Final</th><th>Últ.Programação</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Empresa</th><th>Propaganda</th><th>Dt.Início</th><th>Dt.Final</th><th>Últ.Programação</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    // Decora a situacao
                    switch (obj.situacao) {
                        case 'EXB':
                            situacao = "class='fw-bold table-success'";
                        break;
                        case 'INT':
                            situacao = "class='fw-bold table-warning'";
                        break;
                        default :
                            situacao = "class='fw-bold table-danger'";
                    }

                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? 
                                    "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+obj.empresa+"</a>" : obj.empresa)+ 
                                '</td>'+
                                '<td>'+
                                (funcao == 'Cadastrar' ? 
                                    "<a href='?evento=propaganda&propaganda="+obj.propaganda+"'>"+obj.propaganda+"</a>" : obj.propaganda)+
                                '</td>'+
                                '<td>'+obj.dataInicio+'</td>'+
                                '<td>'+obj.dataFinal+'</td>'+
                                '<td>'+obj.dataHoraExibicao+'</td>'+
                                '<td '+situacao+'>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.empresa+'</td>'+
                                '<td>'+obj.propaganda+'</td>'+
                                '<td>'+obj.dataInicio+'</td>'+
                                '<td>'+obj.dataFinal+'</td>'+
                                '<td>'+obj.dataHoraExibicao+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];      
            }
            divTitulo.innerHTML = "<H4>Propagandas - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela MONITORES - a página deve ter divTituloTabela e divTabela
//
async function adCarregarMonitores(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var situacao = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=Monitores&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Monitor</th><th>Localização</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Monitor</th><th>Localização</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    // Destacar a situacao
                    situacao = adDestacarSituacaoMonitores(obj.situacao);

                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? 
                                    "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+obj.identificacao+"</a>" : obj.identificacao)+ 
                                '</td>'+
                                '<td>'+obj.localizacao+'</td>'+
                                '<td '+situacao+'>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="../administracao/adCadastrarMonitoresPaginas.php?idMonitor='+obj.id+'">'+
                                            '<img src="../ativos/img/alterar.png" title="Cadastro de Páginas"/></a>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                            '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';                                    
                    htmlImpressao += '<tr><td>'+obj.identificacao+'</td>'+
                                '<td>'+obj.localizacao+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });

                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];      
            }
            divTitulo.innerHTML = "<H4>Monitores - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela MONITORES PÁGINAS - a página deve ter divTituloTabela e divTabela
//
async function adCarregarMonitoresPaginas(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=MonitoresPaginas&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Página</th><th>Ação no monitor</th><th>Refresh</th><th>Resolução</th><th>Situação</th>"+
                        "<th>Ação</th></tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Página</th><th>Ação no monitor</th><th>Refresh</th><th>Resolução</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){

                    // Se primeiro registro montar informações do Monitor
                    //
                    ++qtdRegistros;
                    if (qtdRegistros == 1) {
                        htmlHeader = "Monitor "+obj.identificacao+' '+obj.localizacao;
                    }

                    // Destacar a ação e situacao
                    acao = adDestacarAcaoMonitores(obj.acao);                        
                    situacao = adDestacarSituacaoMonitores(obj.situacao);
                    
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? 
                                    "<a href='?evento=recuperar&id="+obj.idPagina+"' title='Alterar registro'>"+obj.pagina+"</a>" : obj.pagina)+ 
                                '</td>'+
                                '<td '+acao+'>'+obj.descAcao+'</td>'+
                                '<td>'+obj.segundos+'</td>'+
                                '<td>'+obj.descResolucao+'</td>'+
                                '<td '+situacao+'>'+obj.descSituacaoPagina+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.idPagina+'&idMonitor='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';

                    htmlImpressao += '<td>'+obj.pagina+'</td>'+
                                '<td>'+obj.descAcao+'</td>'+
                                '<td>'+obj.segundos+'</td>'+
                                '<td>'+obj.descResolucao+'</td>'+
                                '<td>'+obj.descSituacaoPagina+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>"+htmlHeader+" - ["+qtdRegistros+"]</H4>";
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

// CARREGAR SELECTS
// ***************************************************************************************************

// Carregar select SITES
//
async function adCarregarSelectLoginPlantas(select,codigo,filtro,funcao = ''){
    $('.carregando').show();
    await $.getJSON('suporte/suBuscar.php?funcao=Plantas&filtro='+filtro, function(dados){
        var option = (funcao == 'Cadastrar' ? 
                        '<option value="" disabled selected>Escolha um dos sites que você tem acesso</option>' :
                        (funcao == 'Login' ? 
                            '<option value="">Selecione</option>' : '<option value="" selected>Todos</option>'));
        if (dados != null) {
            $.each(dados, function(i, obj){
                option += '<option value="'+obj.idAeroporto+'|'+obj.sistema+'|'+obj.site+'"'+
                    ((obj.site == codigo) ? ' selected ' : '') +'>'+
                        obj.site+(obj.cliente != '' ? ' - '+obj.cliente : '')+'</option>';
            });
        }
        $(select).html(option).show();
    });
    $('.carregando').hide();
};

// Carregar select AEROPORTOS ACESSADOS PELO USUARIO
//
async function adCarregarSelectAeroportosAcessados(select,aeroporto,filtro,funcao = ''){
    $('.carregando').show();
    await $.getJSON('../suporte/suBuscar.php?funcao=Acessos&filtro='+filtro, function(dados){
        var option = (funcao == 'Cadastrar' ? 
                        '<option value="" disabled selected>Selecionar</option>' :
                        '<option value="" selected>Todos</option>');
        if (dados != null) {
            $.each(dados, function(i, obj){
                option += '<option value="'+obj.aeroporto+'|'+obj.grupo+'|'+obj.nivel+'|'+obj.idAeroporto+'|'+obj.nome+'|'+obj.sistema+'|'+obj.localidade+'"'+
                    ((obj.aeroporto == aeroporto) ? ' selected ' : '') +'>'+
                        obj.aeroporto+' ['+obj.localidade+']</option>';
            });
        } else {
            option = '<option value="" disabled selected>Sem registros</option>'
        }
        $(select).html(option).show();
    });
    $('.carregando').hide();
};

// Carregar select MENUS FORMULARIO
//
async function adCarregarSelectMenusFormulario(select,codigo,filtro,funcao = ''){
    $('.carregando').show();
    await $.getJSON('../suporte/suBuscar.php?funcao=MenusFormulario&filtro='+filtro, function(dados){
        var option = (funcao == 'Cadastrar' ? 
                        '<option value="" disabled selected>Selecionar</option>' :
                        '<option value="" selected>Todos</option>');
        if (dados != null) {
            $.each(dados, function(i, obj){
                option += '<option value="'+obj.sistema+"#"+obj.formulario+'"'+
                    ((obj.sistema+"#"+obj.formulario == codigo) ? ' selected ' : '') +'>'+
                    obj.formularioCompleto+'</option>';
            });
        } else {
            option = '<option value="" disabled selected>Sem registros</option>'
        }
        $(select).html(option).show();
    });
    $('.carregando').hide();
};
// ***************************************************************************************************

// Funções AUXILIARES
//
function adDestacarAcaoMonitores(acao) {
    var htmlRetorno = '';
    switch (acao) {
        case 'EXB':
            htmlRetorno = "class='fw-bold table-success'";
        break;
        case 'INT':

            htmlRetorno = "class='fw-bold table-warning'";
        break;
        default :
            htmlRetorno = "class='fw-bold table-danger'";
    }
    return htmlRetorno;
}

function adDestacarSituacaoMonitores(situacao) {
    var htmlRetorno = '';
    switch (situacao) {
        case 'ATV':
            htmlRetorno = "class='fw-bold table-success'";
        break;
        default :
            htmlRetorno = "class='fw-bold table-danger'";
    }
    return htmlRetorno;
}
// ***************************************************************************************************