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
                option += '<option value="'+obj.idSite+'|'+obj.sistema+'|'+obj.site+'"'+
                    ((obj.site == codigo) ? ' selected ' : '') +'>'+
                        obj.site+(obj.cliente != '' ? ' - '+obj.cliente : '')+'</option>';
            });
        }
        $(select).html(option).show();
    });
    $('.carregando').hide();
};

// Carregar select SITES ACESSADOS PELO USUARIO
//
async function adCarregarSelectAeroportosAcessados(select,aeroporto,filtro,funcao = ''){
    $('.carregando').show();
    await $.getJSON('../suporte/suBuscar.php?funcao=Acessos&filtro='+filtro, function(dados){
        var option = (funcao == 'Cadastrar' ? 
                        '<option value="" disabled selected>Selecionar</option>' :
                        '<option value="" selected>Todos</option>');
        if (dados != null) {
            $.each(dados, function(i, obj){
                option += '<option value="'+obj.aeroporto+'|'+obj.grupo+'|'+obj.nivel+'|'+obj.idSite+'|'+obj.nome+'|'+obj.sistema+'|'+obj.localidade+'"'+
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
function adDestacarAcao(acao) {
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

function adDestacarSituacao(situacao) {
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