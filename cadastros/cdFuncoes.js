// Carregar tabela EQUIPAMENTOS - a página deve ter divTituloTabela e divTabela
//
async function cdCarregarEquipamentos(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Equipamentos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>ICAO</th><th>Modelo</th><th>Fabricante</th><th>IATA</th><th>Categoria</th>"+
                        "<th>Evergadura</th><th>Comprimento</th><th>Assentos</th><th>Tipo Motor</th><th>ASA</th><th>Fonte</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+
                        "</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>ICAO</th><th>Modelo</th><th>Fabricante</th><th>IATA</th><th>Categoria</th>"+
                        "<th>Evergadura</th><th>Comprimento</th><th>Assentos</th><th>Tipo Motor</th><th>ASA</th><th>Fonte</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.equipamento+"</a>" : obj.equipamento) + '</td>'+
                                '<td>'+obj.modelo+'</td>'+
                                '<td>'+obj.fabricante+'</td>'+
                                '<td>'+obj.iataEquipamento+'</td>'+
                                '<td>'+obj.icaoCategoria+'</td>'+
                                '<td>'+Number(obj.envergadura).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+Number(obj.comprimento).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td>'+obj.descTipoMotor+'</td>'+                            
                                '<td>'+obj.descAsa+'</td>'+
                                '<td>'+obj.descFonte+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.equipamento+'</td>'+
                                '<td>'+obj.modelo+'</td>'+
                                '<td>'+obj.fabricante+'</td>'+
                                '<td>'+obj.iataEquipamento+'</td>'+
                                '<td>'+obj.icaoCategoria+'</td>'+
                                '<td>'+Number(obj.envergadura).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+Number(obj.comprimento).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td>'+obj.descTipoMotor+'</td>'+
                                '<td>'+obj.descAsa+'</td>'+
                                '<td>'+obj.descFonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Equipamentos - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela MATRÍCULAS - a página deve ter divTituloTabela e divTabela
//
async function cdCarregarMatriculas(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Matriculas&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Matrícula</th><th>Operador</th><th>Equipamentos</th><th>Assentos</th><th>PMD</th><th>Categoria</th><th>Fonte</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Matrícula</th><th>Operador</th><th>Equipamentos</th><th>Assentos</th><th>PMD</th><th>Categoria</th><th>Fonte</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.matricula+"</a>" : obj.matricula) + '</td>'+
                                '<td>'+obj.operadorCompleto+'</td>'+
                                '<td>'+obj.equipamentoCompleto+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td>'+obj.pmd+'</td>'+
                                '<td>'+obj.descCategoria+'</td>'+
                                '<td>'+obj.descFonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.matricula+'</td>'+
                                '<td>'+obj.operadorCompleto+'</td>'+
                                '<td>'+obj.equipamentoCompleto+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td>'+obj.pmd+'</td>'+
                                '<td>'+obj.descCategoria+'</td>'+
                                '<td>'+obj.descFonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Matrículas de Aeronaves - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela OPERADORES AEREOS COBRANCA - a página deve ter divTituloTabela e divTabela
//
async function cdCarregarOperadoresCobranca(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=OperadoresCobranca&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Operador</th><th>Nome</th><th>CPF/CNPJ</th><th>Endereço</th><th>Contato</th><th>Fonte</th>"+
                        "<th>Situação</th><th><img src='../ativos/img/informacoes.png' title='Visualizar operadores RAB'/>RAB</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+
                        "</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Operador</th><th>Nome</th><th>CPF/CNPJ</th><th>Endereço</th><th>Contato</th><th>Fonte</th>"+
                        "<th>Situação</th><th>Operadores</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.operador+"</a>" : obj.operador) + '</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.cpfCnpj+'</td>'+
                                '<td>'+obj.enderecoCompleto+'</td>'+
                                '<td>'+obj.contatoCompleto+'</td>'+ 
                                '<td>'+obj.descFonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '<td><center>'+
                                (obj.qtdOperadores == 0 ? '' : '<a href="?evento=operadores&id='+obj.id+'&operadorCOB='+obj.operador+'">'+ 
                                '['+obj.qtdOperadores+']</a>')+
                                '</center></td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.operador+'</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.cpfCnpj+'</td>'+
                                '<td>'+obj.enderecoCompleto+'</td>'+
                                '<td>'+obj.contatoCompleto+'</td>'+ 
                                '<td>'+obj.descFonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '<td><center>'+
                                (obj.qtdOperadores == 0 ? '' : '['+obj.qtdOperadores+']')+
                                '</center></td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Operadores Aéreos Cobrança - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela OPERADORES AEREOS RAB - a página deve ter divTituloTabela e divTabela
//
async function cdCarregarOperadoresRAB(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=OperadoresRAB&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Operador</th><th>Nome</th><th>CPF/CNPJ</th><th>ICAO</th><th>IATA</th><th>Grupo</th><th>Matriz</th>"+
                        "<th>Fonte</th><th>Situação</th>"+
                        "<th><img src='../ativos/img/informacoes.png' title='Informações de cobrança'/></th>"+
                        "<th><img src='../ativos/img/aviao.png' title='Visualizar matrículas'/></th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+
                        "</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Operador</th><th>Nome</th><th>CPF/CNPJ</th><th>ICAO</th><th>IATA</th><th>Grupo</th><th>Matriz</th>"+
                        "<th>Fonte</th><th>Situação</th><th>Cobrança</th><th>Matrículas</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.operador+"</a>" : obj.operador) + '</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.cpfCnpj+'</td>'+
                                '<td>'+obj.icao+'</td>'+
                                '<td>'+obj.iata+'</td>'+
                                '<td>'+obj.grupo+'</td>'+
                                (obj.matrizIcao == '' ? '<td></td>' :
                                    '<td data-toggle="tooltip" data-placement="bottom" title="Matriz: '+obj.matrizCompleta+'"><u>'+obj.matrizIcao+'</u></td>')+
                                '<td>'+obj.descFonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '<td><center>'+
                                (obj.idCobranca == 0 ? '' : '<a href="?evento=cobranca&id='+obj.idCobranca+'&operadorRAB='+obj.operadorCompleto+'">'+ 
                                '<img src="../ativos/img/informacoes.png" title="Informações de cobrança"/></a>')+
                                '</center></td>'+
                                '<td><center>'+
                                (obj.qtdMatriculas == 0 ? '' : '<a href="?evento=matriculas&id='+obj.id+'">'+ 
                                '['+obj.qtdMatriculas+']</a>')+
                                '</center></td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.operador+'</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.cpfCnpj+'</td>'+
                                '<td>'+obj.iata+'</td>'+
                                '<td>'+obj.icao+'</td>'+
                                '<td>'+obj.grupo+'</td>'+
                                '<td>'+obj.matrizIcao+'</td>'+
                                '<td>'+obj.descFonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '<td>'+obj.descCobranca+'</td>'+
                                '<td><center>'+
                                (obj.qtdMatriculas == 0 ? '' : '['+obj.qtdMatriculas+']')+
                                '</center></td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Operadores Aéreos RAB - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela AEROPORTOS - a página deve ter divTituloTabela e divTabela
//
async function cdCarregarAeroportos(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Aeroportos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>ICAO</th><th>IATA</th><th>Nome</th><th>Localidade</th><th>País</th><th>Fonte</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>ICAO</th><th>IATA</th><th>Nome</th><th>Localidade</th><th>País</th><th>Fonte</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.icao+"</a>" : obj.icao) + '</td>'+
                                '<td>'+obj.iata+'</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.localidade+'</td>'+
                                '<td>'+obj.pais+'</td>'+
                                '<td>'+obj.descFonte+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.icao+'</td>'+
                                '<td>'+obj.iata+'</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.localidade+'</td>'+
                                '<td>'+obj.pais+'</td>'+
                                '<td>'+obj.descFonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Aeroportos - ["+qtdRegistros+"]</H4>";
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

// Carregar tabela COMANDANTES - a página deve ter divTituloTabela e divTabela
//
async function cdCarregarComandantes(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Comandantes&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Código ANAC</th><th>Nome</th><th>Telefone</th><th>Email</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Código ANAC</th><th>Nome</th><th>Telefone</th><th>Email</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.codigoAnac+"</a>" : obj.codigoAnac) + '</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.telefone+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.codigoAnac+'</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.telefone+'</td>'+
                                '<td>'+obj.email+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Comandantes - ["+qtdRegistros+"]</H4>";
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

// Carregar VISUALIZAR MATRICULAS - a página deve ter labelVisualizar e divVisualizar
//
async function cdVisualizarMatriculas(filtro) {
    $('.carregando').show();
    var labelVisualizar = document.getElementById("labelVisualizar");
    var divVisualizar = document.getElementById("divVisualizar");
    var htmlTabela = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=Matriculas&filtro='+encodeURIComponent(filtro), function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Matrícula</th><th>Equipamento</th></tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr>'+
                                '<td>'+obj.matricula+'</td>'+
                                '<td>'+obj.equipamentoCompleto+'</td>'
                                '</tr>';
                    operador = obj.operadorCompleto;
                });
                htmlTabela += "</tbody></table>";
            }
            labelVisualizar.innerHTML = "<h5>Matrículas do Operador: "+operador+"</h5>";
            divVisualizar.innerHTML = htmlTabela;
        });
    } catch (error) {
        divVisualizar.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};

// Carregar VISUALIZAR OOPERADORES - a página deve ter labelVisualizar e divVisualizar
//
async function cdVisualizarOperadoresRAB(filtro,operadorCOB) {
    $('.carregando').show();
    var labelVisualizar = document.getElementById("labelVisualizar");
    var divVisualizar = document.getElementById("divVisualizar");
    var htmlTabela = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=OperadoresRAB&filtro='+encodeURIComponent(filtro), function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>CPF/CNPJ</th><th>Operador</th>"+
                        "<th><img src='../ativos/img/aviao.png' title='Visualizar matrículas'/></th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr>'+
                                '<td>'+obj.cpfCnpj+'</td>'+
                                '<td>'+obj.operador+'</td>'+
                                '<td><center>'+
                                (obj.qtdMatriculas == 0 ? '' : '<a href="?evento=matriculas&id='+obj.id+'">'+ 
                                '['+obj.qtdMatriculas+']</a>')+
                                '</center></td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
            }
            labelVisualizar.innerHTML = "<h5>Operadores RAB: "+operadorCOB+"</h5>";
            divVisualizar.innerHTML = htmlTabela;
        });
    } catch (error) {
        divVisualizar.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};

// Carregar VISUALIZAR OPERADORES DE COBRANÇA - a página deve ter labelVisualizar e divVisualizar
//
async function cdVisualizarOperadoresCobranca(filtro,operadorRAB) {
    $('.carregando').show();
    var labelVisualizar = document.getElementById("labelVisualizar");
    var divVisualizar = document.getElementById("divVisualizar");
    var htmlTabela = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=OperadoresCobranca&filtro='+encodeURIComponent(filtro), function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                "<th>Operador</th><th>Nome</th><th>CPF/CNPJ</th><th>Endereço</th><th>Contato</th>"+
                "<th>Fonte</th><th>Situação</th>"+
                "</th></tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr>'+
                                '<td>'+obj.operador+'</td>'+
                                '<td>'+obj.nome+'</td>'+
                                '<td>'+obj.cpfCnpj+'</td>'+
                                '<td>'+obj.enderecoCompleto+'</td>'+
                                '<td>'+obj.contatoCompleto+'</td>'+ 
                                '<td>'+obj.descFonte+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
            }
            labelVisualizar.innerHTML = "<h5>Informações de Cobrança: "+operadorRAB+"</h5>";
            divVisualizar.innerHTML = htmlTabela;
        });
    } catch (error) {
        divVisualizar.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};
// ***************************************************************************************************