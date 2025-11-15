// Carregar tabela EMPRESAS - a página deve ter divTituloTabela e divTabela
//
async function crCarregarEmpresas(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Empresas&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Empresa</th><th>Atividade</th><th>Endereço</th><th>Bairro</th><th>Email</th><th>Telefone</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Empresa</th><th>Atividade</th><th>Endereço</th><th>Bairro</th><th>Email</th><th>Telefone</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.empresa+"</a>" : obj.empresa) + '</td>'+
                                '<td>'+obj.atividade+'</td>'+
                                '<td>'+obj.endereco+'</td>'+
                                '<td>'+obj.bairro+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.telefone+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="../credenciamento/crCadastrarCredenciados.php?empreda='+obj.id+'"><img src="../ativos/img/pouso.png" title="Credenciados"/></a>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.empresa+'</td>'+
                                '<td>'+obj.atividade+'</td>'+
                                '<td>'+obj.endereco+'</td>'+
                                '<td>'+obj.bairro+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.telefone+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Empresas - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela PESSOAS CREDENCIADAS - a página deve ter divTituloTabela e divTabela
//
async function crCarregarPessoasCredenciadas(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=PessoasCredenciadas&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Nome</th><th>Documento</th><th>Cargo</th><th>Responsável</th><th>Credencial</th><th>Área</th><th>Validade</th>"+
                        "<th>Endereço</th><th>Bairro</th><th>Email</th><th>Telefone</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Nome</th><th>Documento</th><th>Cargo</th><th>Responsável</th><th>Credencial</th><th>Área</th><th>Validade</th>"+
                        "<th>Endereço</th><th>Bairro</th><th>Email</th><th>Telefone</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=alterarPessoas&idPessoa="+obj.id+"' title='Alterar registro'>"+
                                obj.nome+"</a>" : obj.nome) + '</td>'+
                                '<td>'+obj.documento+'</td>'+
                                '<td>'+obj.cargo+'</td>'+
                                '<td>'+obj.descResponsavel+'</td>'+
                                '<td>'+obj.credencial+'</td>'+
                                '<td>'+obj.recurso+'</td>'+
                                '<td>'+obj.dataValidade+'</td>'+
                                '<td>'+obj.endereco+'</td>'+
                                '<td>'+obj.bairro+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.telefone+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluirPessoas&idPessoa='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.nome+'</td>'+
                                '<td>'+obj.documento+'</td>'+
                                '<td>'+obj.cargo+'</td>'+
                                '<td>'+obj.descResponsavel+'</td>'+
                                '<td>'+obj.credencial+'</td>'+
                                '<td>'+obj.recurso+'</td>'+
                                '<td>'+obj.validade+'</td>'+
                                '<td>'+obj.endereco+'</td>'+
                                '<td>'+obj.bairro+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.telefone+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Pessoas Credenciadas - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela CREDENCIADOS (PESSOAS E VEÍCULOS) - a página deve ter divTituloTabela e divTabela
//
async function crCarregarCredenciados(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var empresa = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=Credenciados&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        (funcao == 'Consultar' ? "<th>Empresa</th>" : "")+
                        "<th>Nome</th><th>Documento</th><th>Cargo</th><th>Responsável</th><th>Credencial</th><th>Área</th><th>Validade</th>"+
                        "<th>Endereço</th><th>Bairro</th><th>Email</th><th>Telefone</th><th>Foto</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        (funcao == 'Consultar' ? "<th>Empresa</th>" : "")+
                        "<th>Nome</th><th>Documento</th><th>Cargo</th><th>Responsável</th><th>Credencial</th><th>Área</th><th>Validade</th>"+
                        "<th>Endereço</th><th>Bairro</th><th>Email</th><th>Telefone</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr>'+
                                ((funcao == 'Consultar') ? '<td>' + ((empresa != obj.empresa) ? obj.empresa : "") + '</td>' : "") +
                                '<td>'+(funcao == 'Cadastrar' ? "<a href='?evento=alterarPessoas&idPessoa="+obj.id+"' title='Alterar registro'>"+
                                            obj.nome+"</a>" : obj.nome) + '</td>'+
                                '<td>'+obj.documento+'</td>'+
                                '<td>'+obj.cargo+'</td>'+
                                '<td>'+obj.descResponsavel+'</td>'+
                                '<td>'+obj.credencial+'</td>'+
                                '<td>'+obj.recurso+'</td>'+
                                '<td>'+obj.dataValidade+'</td>'+
                                '<td>'+obj.endereco+'</td>'+
                                '<td>'+obj.bairro+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.telefone+'</td>'+
                                "<td><center>"+
                                (funcao == 'Cadastrar' ? 
                                "<a href='?evento=foto&imagem="+obj.imagem+"&tipo=pessoa&credencial="+obj.credencial+"'>"+
                                "<object data='../arquivos/credenciamentos/"+obj.imagem+".jpg?nocache="+performance.now()+"' type='image/jpg' style='height:25px; width:25px' title='Clique para atualizar a foto!'>"+
                                "<img src='../arquivos/credenciamentos/default.png?nocache="+performance.now()+"' style='height:25px; width:25px' title='Clique para atualizar a foto!'/></object>"+
                                "</a>" : 
                                "<object data='../arquivos/credenciamentos/"+obj.imagem+".jpg?nocache="+performance.now()+"' type='image/jpg' style='height:25px; width:25px'>"+
                                "<img src='../arquivos/credenciamentos/default.png?nocache="+performance.now()+"' style='height:25px; width:25px'/></object>")+
                                "</center></td>" +
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluirPessoas&idPessoa='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';

                    htmlImpressao += '<tr>'+
                                ((funcao == 'Consultar') && (empresa != obj.empresa) ? '<td>'+obj.empresa+'</td>' : "") +
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.documento+'</td>'+
                                '<td>'+obj.cargo+'</td>'+
                                '<td>'+obj.descResponsavel+'</td>'+
                                '<td>'+obj.credencial+'</td>'+
                                '<td>'+obj.recurso+'</td>'+
                                '<td>'+obj.dataValidade+'</td>'+
                                '<td>'+obj.endereco+'</td>'+
                                '<td>'+obj.bairro+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.telefone+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                    empresa = obj.empresa;
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Pessoas Credenciadas - ["+qtdRegistros+"]</H4>";
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

// Carregar select EMPRESAS
async function crCarregarSelectEmpresas(select,codigo,filtro,funcao = '') {
    $('.carregando').show();
    await $.getJSON('../suporte/suBuscar.php?funcao=Empresas&filtro='+filtro, function(dados){
        var option = (funcao == 'Cadastrar' ? 
                        '<option value="" disabled selected>Selecionar</option>' :
                        '<option value="" selected>Todas</option>');
        if (dados != null) {
            $.each(dados, function(i, obj){
                option += '<option value="'+obj.codigo+'#'+obj.descricao+'"'+
                    ((obj.codigo == codigo) ? ' selected ' : '') +'>'+
                    obj.descricao+'</option>';
            });
        } else {
            option = '<option value="" disabled selected>Sem registros</option>'
        }
        $(select).html(option).show();
    });
    $('.carregando').hide();
};
// ***************************************************************************************************