// Carregar ÚLTIMOS MOVIMENTOS - a página deve ter divTituloTabela e divTabela
//
async function opCarregarUltimosMovimentos(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=UltimosMovimentosStatus&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                        "<thead class='table-info'><tr>"+
                        "<th>Status</th><th>Matrícula</th><th>Equipamento</th><th>Tipo</th><th>Origem</th><th>Chegada</th>"+
                        "<th>Destino</th><th>Partida</th><th>Movimento</th><th>Dh.Movimento</th><th>Pista</th><th>Posição</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Status</th><th>Matrícula</th><th>Equipamento</th><th>Tipo</th><th>Origem</th><th>Chegada</th>"+
                        "<th>Destino</th><th>Partida</th><th>Movimento</th><th>Dh.Movimento</th><th>Pista</th><th>Posição</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    // Alerta do movimento
                    alerta = opVerificarAlertaMovimento(obj.destaque, obj.alerta, obj.dhMovimento);

                    htmlTabela += '<tr '+alerta+'><td>'+
                                (funcao == 'Cadastrar' ? "<a class='fw-bold' href='?tipo=Status&funcao=Alteração&movimento=Status&idStatus="+obj.id+"&idMovimento=&idUltimo="+obj.idMovimento+
                                        "' title='Alterar status'>"+obj.status+"</a>" : obj.status) + '</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="Grupo: '+obj.grupo+'\nOperador: '+obj.operador+'"><u>'+obj.matricula+'</u></td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="Modelo: '+obj.modelo+'"><u>'+obj.equipamento+'</u></td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descTipo+'"><u>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</u></td>'+
                                '<td>'+obj.origem+'</td>';

                    if (obj.vooChegada == "") {
                        htmlTabela += '<td><a href="?tipo=Status&funcao=Conectar&movimento=Chegada&idStatus='+obj.id+
                                        '"><img src="../ativos/img/conectar.png" title="Conectar chegada"/></a></td>'; 
                    } else {
                        htmlTabela += 
                            '<td>'+
                            '<a style="padding-right:10px;" href="?tipo=Status&funcao=Desconectar&movimento=Chegada&idStatus='+obj.id+
                                '"><img src="../ativos/img/desconectar.png" title="Desconectar chegada"/></a>'+
                            "<a class='fw-bold' href='?tipo=Chegada&funcao=Alteração&movimento=Chegada&idChegada="+obj.idChegada+
                                "' title='Alterar voo'>"+obj.vooChegada+"</a>"+
                            '</td>';
                    }   

                    htmlTabela += '<td>'+obj.destino+'</td>';
                
                    if (obj.vooPartida == "") {
                        htmlTabela += '<td><a href="?tipo=Status&funcao=Conectar&movimento=Partida&idStatus='+obj.id+
                                        '"><img src="../ativos/img/conectar.png" title="Conectar partida"/></a></td>'; 
                    } else {
                        htmlTabela += 
                            '<td>'+
                            '<a style="padding-right:10px;" href="?tipo=Status&funcao=Desconectar&movimento=Partida&idStatus='+obj.id+
                                '"><img src="../ativos/img/desconectar.png" title="Desconectar partida"/></a>'+
                            "<a class='fw-bold' href='?tipo=Partida&funcao=Alteração&movimento=Partida&idPartida="+obj.idPartida+
                                "' title='Alterar voo'>"+obj.vooPartida+"</a></td>"+                            
                            '</td>';   
                    }

                    // Destaca o movimento
                    destaque = "class='fw-bold "+(obj.destaque != '' ? "table-"+obj.destaque : '')+"'";

                    htmlTabela += '<td '+destaque+'>'+
                                (funcao == 'Cadastrar' ? "<a href='?tipo=Status&funcao=Alteração&movimento="+obj.descMovimento+
                                            "&idStatus="+obj.id+"&idMovimento="+obj.idMovimento+"&idUltimo="+obj.idMovimento+
                                            "'><img src='../ativos/img/alterar.png' title='Alterar movimento'/></a>" : "")+
                                            obj.descMovimento+'</td>';
                    
                    // Data e hora do movimento e Recursos                        
                    htmlTabela += '<td>'+obj.dataHoraMovimento+'</td>'+
                                                    // Recursos
                                (obj.tipoRecurso == 'PIS' ? '<td>'+obj.descRecurso+'</td>' :
                                    (obj.tipoSegundoRecurso == 'PIS' ? '<td>'+obj.descSegundoRecurso+'</td>' : '<td></td>'))+
                                (obj.tipoRecurso == 'POS' ? '<td>'+obj.descRecurso+'</td>' :
                                    (obj.tipoSegundoRecurso == 'POS' ? '<td>'+obj.descSegundoRecurso+'</td>' : '<td></td>'));
                                
                    // Montar Ações
                    if (funcao == 'Cadastrar') {
                        htmlTabela += '<td>'+opMontaHtmlAcoes(obj.status, obj.id, obj.idMovimento)+'</td>';
                    }
                    htmlTabela += '</tr>';

                    htmlImpressao += '<tr><td>'+obj.numero+'</td>'+
                                '<td>'+obj.matricula+'</td>'+
                                '<td>'+obj.equipamento+'</td>'+
                                '<td>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</td>'+
                                '<td>'+obj.origem+'</td>'+
                                '<td>'+obj.vooChegada+'</td>'+
                                '<td>'+obj.destino+'</td>'+
                                '<td>'+obj.vooPartida+'</td>'+
                                '<td>'+obj.descMovimento+'</td>'+
                                '<td>'+obj.dataHoraMovimento+'</td>'+
                                // Recursos
                                (obj.tipoRecurso == 'PIS' ? '<td>'+obj.descRecurso+'</td>' :
                                    (obj.tipoSegundoRecurso == 'PIS' ? '<td>'+obj.descSegundoRecurso+'</td>' : '<td></td>'))+
                                (obj.tipoRecurso == 'POS' ? '<td>'+obj.descRecurso+'</td>' :
                                    (obj.tipoSegundoRecurso == 'POS' ? '<td>'+obj.descSegundoRecurso+'</td>' : '<td></td>'))+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = ""; //"<H4>Movimentos de Aeronaves - ["+qtdRegistros+"]</H4>";
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

// Carregar STATUS - a página deve ter divTituloTabela e divTabela
//
async function opCarregarStatus(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var statusAnterior = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=Status&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                        "<thead class='table-info'><tr>"+
                        "<th>Status</th><th>Matrícula</th><th>Equipamento</th><th>Tipo</th><th>Origem</th><th>Chegada</th><th>Destino</th>"+
                        "<th>Partida</th><th>Faturado</th><th>Situação</th><th>Movimento</th><th>Dh.Movimento</th><th>Pista</th><th>Posição</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Status</th><th>Matrícula</th><th>Equipamento</th><th>Tipo</th><th>Origem</th><th>Chegada</th><th>Destino</th>"+
                        "<th>Partida</th><th>Faturado</th><th>Situação</th><th>Movimento</th><th>Dh.Movimento</th><th>Pista</th><th>Posição</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    if (obj.status != statusAnterior){
                        // Se opção de cadastrar e não fechado, libera chamada do formulário
                        htmlTabela += '<tr class="fw-bold"><td>'+
                                        (funcao == 'Cadastrar' && obj.situacao != 'FCH' ? 
                                            "<a href='?tipo=Status&funcao=Alteração&movimento=Status&idStatus="+obj.id+"&idMovimento=&idUltimo="+obj.idMovimento+
                                            "' title='Alterar status'>"+obj.status+"</a>" : obj.status) + '</td>'+
                                '<td>'+obj.matricula+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="Modelo: '+obj.modelo+'"><u>'+obj.equipamento+'</u></td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descTipo+'"><u>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</u></td>'+
                                '<td>'+obj.origem+'</td>';

                        if (obj.vooChegada == "") {
                            htmlTabela += '<td><a href="?tipo=Status&funcao=Conectar&movimento=Chegada&idStatus='+obj.id+
                                            '"><img src="../ativos/img/conectar.png" title="Conectar chegada"/></a></td>'; 
                        } else {
                            htmlTabela += 
                                '<td>'+
                                '<a style="padding-right:10px;" href="?tipo=Status&funcao=Desconectar&movimento=Chegada&idStatus='+obj.id+
                                    '"><img src="../ativos/img/desconectar.png" title="Desconectar chegada"/></a>'+
                                "<a class='fw-bold' href='?tipo=Chegada&funcao=Alteração&movimento=Chegada&idChegada="+obj.idChegada+
                                    "' title='Alterar voo'>"+obj.vooChegada+"</a>"+
                                '</td>';
                        }
                        
                        htmlTabela += '<td>'+obj.destino+'</td>';
                
                        if (obj.vooPartida == "") {
                            htmlTabela += '<td><a href="?tipo=Status&funcao=Conectar&movimento=Partida&idStatus='+obj.id+
                                            '"><img src="../ativos/img/conectar.png" title="Conectar partida"/></a></td>'; 
                        } else {
                            htmlTabela += 
                                '<td>'+
                                '<a style="padding-right:10px;" href="?tipo=Status&funcao=Desconectar&movimento=Partida&idStatus='+obj.id+
                                    '"><img src="../ativos/img/desconectar.png" title="Desconectar partida"/></a>'+
                                "<a class='fw-bold' href='?tipo=Partida&funcao=Alteração&movimento=Partida&idPartida="+obj.idPartida+
                                    "' title='Alterar voo'>"+obj.vooPartida+"</a></td>"+                            
                                '</td>';   
                        }
                        
                        htmlTabela += '<td>'+obj.descFaturado+'</td>'+
                                '<td>'+obj.descSituacao+'</td>';
                    } else {
                        htmlTabela += '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
                    }

                    // Destaca o movimento
                    destaque = "class='fw-bold "+(obj.destaque != '' ? "table-"+obj.destaque : '')+"'";

                    // Se opção de cadastrar, não fechado e é o último movimento, libera chamada do formulário
                    if (funcao == 'Cadastrar' && obj.situacao != 'FCH' && obj.idMovimento == obj.idUltimoMovimento) {
                        htmlTabela += '<td '+destaque+'>'+
                                    "<a href='?tipo=Status&funcao=Alteração&movimento="+obj.descMovimento+
                                    "&idStatus="+obj.id+"&idMovimento="+obj.idMovimento+"&idUltimo="+obj.idMovimento+
                                    "'><img src='../ativos/img/alterar.png' title='Alterar movimento'/></a>"+obj.descMovimento+'</td>';
                    } else {
                        htmlTabela += '<td '+destaque+'>'+obj.descMovimento+'</td>';
                    }
                    // Data e hora do movimento e Recursos                        
                    htmlTabela += '<td>'+obj.dataHoraMovimento+'</td>'+
                                (obj.tipoRecurso == 'PIS' ? '<td>'+obj.descRecurso+'</td><td></td>' : '<td></td><td>'+obj.descRecurso+'</td>');      
                                
                    // Montar Ações
                    // Se opção de cadastrar, não fechado e é o último movimento, libera chamada do formulário
                    if (funcao == 'Cadastrar') {
                        htmlTabela += '<td>';
                        if (obj.situacao != 'FCH' && obj.idMovimento == obj.idUltimoMovimento) {
                            htmlTabela += opMontaHtmlAcoes(obj.status, obj.id, obj.idMovimento);
                        }
                        htmlTabela += '</td>';
                    }
                    htmlTabela += '</tr>';

                    if (obj.status != statusAnterior){
                        htmlImpressao += '<tr style="font-weight:bold;"><td>'+obj.status+'</td>'+
                                '<td>'+obj.matricula+'</td>'+
                                '<td>'+obj.equipamento+'</td>'+
                                '<td>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</td>'+
                                '<td>'+obj.origem+'</td>'+
                                '<td>'+obj.vooChegada+'</td>'+
                                '<td>'+obj.destino+'</td>'+
                                '<td>'+obj.vooPartida+'</td>'+
                                '<td>'+obj.descFaturado+'</td>'+
                                '<td>'+obj.descSituacao+'</td>';
                        statusAnterior = obj.status;
                    } else {
                        htmlImpressao += '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
                    }
                    htmlImpressao += '<td>'+obj.descMovimento+'</td>'+
                                '<td>'+obj.dataHoraMovimento+'</td>'+
                                // Recursos
                                (obj.tipoRecurso == 'PIS' ? '<td>'+obj.descRecurso+'</td><td></td>' : '<td></td><td>'+obj.descRecurso+'</td>')+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "" //"<H4>Status de Aeronaves - ["+qtdRegistros+"]</H4>";
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

// Carregar VOOS OPERACIONAIS - a página deve ter divTituloTabela e divTabela
//
async function opCarregarVoosOperacionais(funcao, filtro = '', ordem = '', descricaoFiltro = '', busca = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var vooAnterior = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=VoosOperacionais&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&busca='+busca+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Operação</th>"+
                        "<th>Voo</th>"+
                        "<th>Próximo</th>"+
                        "<th>De/Para</th>"+
                        "<th>Dh.Previsão</th>"+
                        "<th>Tipo</th>"+
                        "<th>Equipamento</th>"+
                        "<th>Assentos</th>"+
                        "<th>PAX</th>"+
                        "<th>PNAE</th>"+
                        "<th>Posição</th>"+
                        "<th>Esteira</th>"+                    
                        "<th>Codeshare</th>"+
                        "<th>Movimento</th>"+
                        "<th>Dh.Movimento</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Operação</th>"+
                        "<th>Voo</th>"+
                        "<th>Próximo</th>"+
                        "<th>De/Para</th>"+
                        "<th>Dh.Previsão</th>"+
                        "<th>Tipo</th>"+
                        "<th>Equipamento</th>"+
                        "<th>Assentos</th>"+
                        "<th>PAX</th>"+
                        "<th>PNAE</th>"+
                        "<th>Posição</th>"+
                        "<th>Esteira</th>"+ 
                        "<th>Codeshare</th>"+
                        "<th>Movimento</th>"+
                        "<th>Dh.Movimento</th>";
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    if (obj.operacao+obj.voo != vooAnterior){
                        // Se opção de cadastrar, libera chamada do formulário
                        htmlTabela += '<tr>'+
                                    '<td class="fw-bold '+(obj.operacao == 'CHG' ? 'table-success">Chegada' : 'table-primary">Partida')+'</td>'+
                                    '<td class="fw-bold">'+
                                        (funcao == 'Cadastrar' ? 
                                            "<a href='?funcao=Alteração&id="+obj.id+"' title='Alterar voo operacional'>"+obj.voo+"</a>" : obj.voo) + '</td>';
                        htmlImpressao += '<tr><td>'+(obj.operacao == 'CHG' ? 'Chegada' : 'Partida')+'</td><td>'+obj.voo+'</td>';

                        // Monta as informações
                        htmlTabela += 
                                    '<td>'+(obj.operacao == 'CHG' ? obj.vooPartida : obj.vooChegada)+'</td>'+
                                    '<td data-toggle="tooltip" data-placement="bottom" title="'+
                                        (obj.operacao == 'CHG' ? obj.descOrigemCompleta+'"><u>'+obj.origem : obj.descDestinoCompleto+'"><u>'+obj.destino)+
                                    '</u></td>'+
                                    //'<td>'+obj.dtMovimento+'</td>'+
                                    '<td>'+obj.dhPrevista+'</td>'+
                                    //'<td>'+obj.dhConfirmada+'</td>'+
                                    '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descTipo+'"><u>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</u></td>'+
                                    '<td>'+obj.equipamento+'</td>'+
                                    '<td>'+obj.assentos+'</td>'+
                                    '<td>'+obj.pax+'</td>'+
                                    '<td>'+obj.pnae+'</td>'+
                                    '<td>'+obj.posicao+'</td>'+
                                    '<td>'+obj.esteira+'</td>'+
                                    '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.codeshare+'"><u>'+obj.parteCodeshare+'</u></td>';
                        htmlImpressao += 
                                    '<td>'+(obj.operacao == 'CHG' ? '=> '+obj.vooPartida : '<= '+obj.vooChegada)+'</td>'+
                                    '<td>'+(obj.operacao == 'CHG' ? obj.origem : obj.destino)+'</td>'+
                                    '<td>'+obj.dhPrevista+'</td>'+
                                    //'<td>'+obj.dhConfirmada+'</td>'+
                                    '<td>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</td>'+
                                    '<td>'+obj.equipamento+'</td>'+
                                    '<td>'+obj.assentos+'</td>'+
                                    '<td>'+obj.pax+'</td>'+
                                    '<td>'+obj.pnae+'</td>'+
                                    '<td>'+obj.posicao+'</td>'+
                                    '<td>'+obj.esteira+'</td>'+
                                    '<td>'+obj.codeshare+'</td>';

                        vooAnterior = obj.operacao+obj.voo;
                    } else {
                        htmlTabela += '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
                        htmlImpressao += '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
                    } 

                    // Destaca o movimento
                    destaque = "class='fw-bold "+(obj.destaque != '' ? "table-"+obj.destaque : '')+"'";

                    // Movimentos
                    htmlTabela += '<td '+destaque+'>'+obj.descMovimento+'</td>'+
                                '<td>'+obj.dataHoraMovimento+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                '<td><center>'+
                                    '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                    ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                    '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                '</center></td>' : '');
                    htmlTabela += '</tr>';
                    
                    htmlImpressao += '<td>'+obj.descMovimento+'</td>'+
                                '<td>'+obj.dataHoraMovimento+'</td>';
                    htmlImpressao += '</tr>';

                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Voos Operacionais - ["+qtdRegistros+"]</H4>";
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
}

// Carregar VOOS PLANEJADOS - a página deve ter divTituloTabela e divTabela
//
async function opCarregarVoosPlanejados(funcao, filtro = '', ordem = '', descricaoFiltro = '', busca = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var vooAnterior = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=VoosPlanejados&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&busca='+busca+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        //"<th>Operador</th>"+
                        //"<th>empresa</th>"+
                        "<th>Voo</th><th>Equipamento</th>"+
                        //"<th>segunda</th><th>terca</th><th>quarta</th><th>quinta</th><th>sexta</th><th>sabado</th><th>domingo</td>"+
                        "<th>Frequência</th>"+
                        "<th>Assentos</th>"+
                        "<th>Operação</th><th>Natureza</th><th>Etapa</th><th>Origem</th>"+
                        //"<th>aeroportoOrigem</th>"+
                        "<th>Destino</th>"+
                        //"<th>aeroportoDestino</th>"+
                        "<th>Partida</th>"+
                        "<th>Chegada</th>"+
                        "<th>Serviço</th>"+
                        //"<th>Objeto</th>"+
                        "<th>Codeshare</th>"+
                        //"<th>Situação</th>"+
                        "<th>Fonte</th>"+
                        //"<th>SIROS</th>"+
                        //"<th>Situação</th>"+
                        //"<th>Registro</th>"+
                        //"<th>origem</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Voo</th><th>Equipamento</th>"+
                        "<th>Frequência</th>"+
                        "<th>Assentos</th>"+
                        "<th>Operação</th><th>Natureza</th><th>Etapa</th><th>Origem</th>"+
                        "<th>Destino</th>"+
                        "<th>Partida</th>"+
                        "<th>Chegada</th>"+
                        "<th>Serviço</th>"+
                        "<th>Codeshare</th>"+
                        "<th>Fonte</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    if (obj.voo != vooAnterior){
                        // Se opção de cadastrar, libera chamada do formulário
                        htmlTabela += '<tr><td class="fw-bold" data-toggle="tooltip" data-placement="bottom" title="'+obj.empresa+
                                            (obj.siros ? '\nSIROS '+obj.siros+' de '+obj.dataRegistro : '')+'"><u>'+
                                        (funcao == 'Cadastrar' ? 
                                            "<a href='?funcao=Alteração&id="+obj.id+"' title='Alterar voo planejado'>"+obj.voo+"</a>" : obj.voo) + '</u></td>';
                        htmlImpressao += '<tr><td>'+obj.voo+'</td>';
                        vooAnterior = obj.voo;                            
                    } else {
                        htmlTabela += '<tr><td></td>';
                        htmlImpressao += '<tr><td></td>';
                    }    
                    
                    // Monta as informações
                    htmlTabela += '<td>'+obj.equipamento+'</td>'+
                                // '<td>'+obj.segunda+'</td>'+
                                // '<td>'+obj.terca+'</td>'+
                                // '<td>'+obj.quarta+'</td>'+
                                // '<td>'+obj.quinta+'</td>'+
                                // '<td>'+obj.sexta+'</td>'+
                                // '<td>'+obj.sabado+'</td>'+
                                // '<td>'+obj.domingo+'</td>'+
                                '<td>'+obj.frequencia+'</td>'+
                                //'<td>'+obj.frequencia+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td '+(obj.situacaoSiros == 'Em Operação' ? 'class="fw-bold text-success"' : '')+'>'+obj.inicioOperacao+' a '+obj.fimOperacao+'</td>'+
                                '<td>'+obj.naturezaOperacao+'</td>'+
                                '<td>'+obj.numeroEtapa+'</td>'+
                                '<td>'+obj.icaoOrigem+'</td>'+
                                '<td>'+obj.icaoDestino+'</td>'+
                                //'<td>'+obj.icaoDestino+'</td>'+
                                //'<td>'+obj.aeroportoDestino+'</td>'+
                                '<td '+(obj.horarioPartida == obj.horarioOperacao ? 'class="fw-bold text-warning"' : '')+'>'+obj.horarioPartida+'</td>'+
                                '<td '+(obj.horarioChegada == obj.horarioOperacao ? 'class="fw-bold text-warning"' : '')+'>'+obj.horarioChegada+'</td>'+
                                '<td>'+obj.servico+'</td>'+
                                //'<td>'+obj.objetoTransporte+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.codeshare+'"><u>'+obj.parteCodeshare+'</u></td>'+
                                //'<td>'+obj.situacao+'</td>'+
                                '<td>'+obj.fonte+'</td>'+
                                //'<td>'+obj.siros+'</td>'+
                                //'<td><button type="button" class="btn" data-bs-toggle="collapse" data-bs-target="#'+obj.siros+'">'+obj.fonte+'</button>'+
                                //   '<div id="'+obj.siros+'" class="collapse">'+obj.siros+' de '+obj.dataRegistro+'</div></td>';
                                //'<td>'+obj.situacaoSiros+'</td>'+
                                //'<td>'+obj.dataRegistro+'</td>'+
                                //'<td>'+obj.origem+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                '<td><center>'+
                                    '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                    ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                    '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                '</center></td>' : '');
                    htmlTabela += '</tr>';
                    
                    htmlImpressao += '<td>'+obj.equipamento+'</td>'+
                                '<td>'+obj.frequencia+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td '+(obj.situacaoSiros == 'Em Operação' ? 'class="fw-bold text-success"><u>'+obj.inicioOperacao+' a '+obj.fimOperacao+'</u></td>' : '>'+obj.inicioOperacao+' a '+obj.fimOperacao+'</td>')+
                                '<td>'+obj.naturezaOperacao+'</td>'+
                                '<td>'+obj.numeroEtapa+'</td>'+
                                '<td><u>'+obj.icaoOrigem+'</u></td>'+
                                '<td><u>'+obj.icaoDestino+'</u></td>'+
                                '<td '+(obj.horarioPartida == obj.horarioOperacao ? 'class="fw-bold text-warning"><u>'+obj.horarioPartida+'</u></td>' : '>'+obj.horarioPartida+'</td>')+
                                '<td '+(obj.horarioChegada == obj.horarioOperacao ? 'class="fw-bold text-warning"><u>'+obj.horarioChegada+'</u></td>' : '>'+obj.horarioChegada+'</td>')+
                                '<td>'+obj.servico+'</td>'+
                                '<td>'+obj.codeshare+'</td>'+
                                '<td>'+obj.fonte+'</td>';
                    htmlImpressao += '</tr>';

                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Voos Planejados - ["+qtdRegistros+"]</H4>";
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

// Carregar VOOS ANAC - a página deve ter divTituloTabela e divTabela
//
async function opCarregarVoosANAC(funcao, filtro = '', ordem = '', descricaoFiltro = '', busca = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var vooAnterior = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=VoosANAC&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&busca='+busca+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        //"<th>Operador</th>"+
                        //"<th>empresa</th>"+
                        "<th>Voo</th><th>Equipamento</th>"+
                        //"<th>segunda</th><th>terca</th><th>quarta</th><th>quinta</th><th>sexta</th><th>sabado</th><th>domingo</td>"+
                        "<th>Frequência</th>"+
                        "<th>Assentos</th>"+
                        "<th>Operação</th><th>Natureza</th><th>Etapa</th><th>Origem</th>"+
                        //"<th>aeroportoOrigem</th>"+
                        "<th>Destino</th>"+
                        //"<th>aeroportoDestino</th>"+
                        "<th>Partida</th>"+
                        "<th>Chegada</th>"+
                        "<th>Serviço</th>"+
                        //"<th>Objeto</th>"+
                        "<th>Codeshare</th>"+
                        //"<th>Situação</th>"+
                        //"<th>SIROS</th>"+
                        //"<th>Situação</th>"+
                        //"<th>Registro</th>"+
                        //"<th>origem</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+
                        "</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Voo</th><th>Equipamento</th>"+
                        "<th>Frequência</th>"+
                        "<th>Assentos</th>"+
                        "<th>Operação</th><th>Natureza</th><th>Etapa</th><th>Origem</th>"+
                        "<th>Destino</th>"+
                        "<th>Partida</th>"+
                        "<th>Chegada</th>"+
                        "<th>Serviço</th>"+
                        "<th>Codeshare</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    if (obj.voo != vooAnterior){
                        // Se opção de cadastrar e não faturado, libera chamada do formulário
                        htmlTabela += '<tr><td class="fw-bold" data-toggle="tooltip" data-placement="bottom" title="'+obj.empresa+
                                            (obj.siros ? '\nSIROS '+obj.siros+' de '+obj.dataRegistro : '')+'"><u>'+
                                        (funcao == 'Cadastrar' ? 
                                            "<a href='?funcao=Alteração&id="+obj.id+"' title='Alterar voo regular'>"+obj.voo+"</a>" : obj.voo) + '</u></td>';
                        htmlImpressao += '<tr><td><u>'+obj.voo+'</u></td>';
                        vooAnterior = obj.voo;                            
                    } else {
                        htmlTabela += '<tr><td></td>';
                        htmlImpressao += '<tr><td></td>';
                    }    
                    
                    // Monta as informações
                    htmlTabela += '<td>'+obj.equipamento+'</td>'+
                                // '<td>'+obj.segunda+'</td>'+
                                // '<td>'+obj.terca+'</td>'+
                                // '<td>'+obj.quarta+'</td>'+
                                // '<td>'+obj.quinta+'</td>'+
                                // '<td>'+obj.sexta+'</td>'+
                                // '<td>'+obj.sabado+'</td>'+
                                // '<td>'+obj.domingo+'</td>'+
                                '<td>'+obj.frequencia+'</td>'+
                                //'<td>'+obj.frequencia+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td '+(obj.situacaoSiros == 'Em Operação' ? 'class="fw-bold text-success"' : '')+'>'+obj.inicioOperacao+' a '+obj.fimOperacao+'</td>'+
                                '<td>'+obj.naturezaOperacao+'</td>'+
                                '<td>'+obj.numeroEtapa+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.aeroportoOrigem+'"><u>'+obj.icaoOrigem+'</u></td>'+
                                //'<td>'+obj.aeroportoOrigem+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.aeroportoDestino+'"><u>'+obj.icaoDestino+'</u></td>'+
                                //'<td>'+obj.icaoDestino+'</td>'+
                                //'<td>'+obj.aeroportoDestino+'</td>'+
                                '<td '+(obj.horarioPartida == obj.horarioOperacao ? 'class="fw-bold text-warning"' : '')+'>'+obj.horarioPartida+'</td>'+
                                '<td '+(obj.horarioChegada == obj.horarioOperacao ? 'class="fw-bold text-warning"' : '')+'>'+obj.horarioChegada+'</td>'+
                                '<td>'+obj.servico+'</td>'+
                                //'<td>'+obj.objetoTransporte+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.codeshare+'"><u>'+obj.parteCodeshare+'</u></td>'+
                                //'<td>'+obj.situacao+'</td>'+
                                //'<td>'+obj.siros+'</td>'+
                                //'<td><button type="button" class="btn" data-bs-toggle="collapse" data-bs-target="#'+obj.siros+'">'+obj.fonte+'</button>'+
                                //   '<div id="'+obj.siros+'" class="collapse">'+obj.siros+' de '+obj.dataRegistro+'</div></td>';
                                //'<td>'+obj.situacaoSiros+'</td>'+
                                //'<td>'+obj.dataRegistro+'</td>'+
                                //'<td>'+obj.origem+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                '<td><center>'+
                                    '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                    ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                    '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                '</center></td>' : '');
                    htmlTabela += '</tr>';
                    
                    htmlImpressao += '<td>'+obj.equipamento+'</td>'+
                                '<td>'+obj.frequencia+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td '+(obj.situacaoSiros == 'Em Operação' ? 'class="fw-bold text-success"><u>'+obj.inicioOperacao+' a '+obj.fimOperacao+'</u></td>' : '>'+obj.inicioOperacao+' a '+obj.fimOperacao+'</td>')+
                                '<td>'+obj.naturezaOperacao+'</td>'+
                                '<td>'+obj.numeroEtapa+'</td>'+
                                '<td><u>'+obj.icaoOrigem+'</u></td>'+
                                '<td><u>'+obj.icaoDestino+'</u></td>'+
                                '<td '+(obj.horarioPartida == obj.horarioOperacao ? 'class="fw-bold text-warning"><u>'+obj.horarioPartida+'</u></td>' : '>'+obj.horarioPartida+'</td>')+
                                '<td '+(obj.horarioChegada == obj.horarioOperacao ? 'class="fw-bold text-warning"><u>'+obj.horarioChegada+'</u></td>' : '>'+obj.horarioChegada+'</td>')+
                                '<td>'+obj.servico+'</td>'+
                                '<td>'+obj.parteCodeshare+'</td>';
                    htmlImpressao += '</tr>';

                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Voos ANAC - ["+qtdRegistros+"]</H4>";
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

// Carregar PAINEL CHEGADAS - a página deve ter divChegadas
//
async function opPainelChegadas(funcao, filtro = '', ordem = '', descricaoFiltro = '', busca = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divChegadas = document.getElementById("divChegadas");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    // Filtra operação por chegadas
    filtro += " AND vo.operacao = 'CHG'";
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=UltimosMovimentosVoos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&busca='+busca+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                        "<thead class='table-info'><tr>"+
                        "<th>Voo</th><th>Partida</th><th>Origem</th><th>Dh.Previsão</th><th>Dh.Confirmada</th><th>Tipo</th><th>Equipamento</th>"+
                        "<th>Assentos</th><th>PAX</th><th>PNAE</th><th>Codeshare</th>"+
                        "<th>Movimento</th><th>Dh.Movimento</th><th>Posição</th><th>Esteira</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                $.each(dados, function(i, obj){
                    // Alerta do movimento
                    alerta = opVerificarAlertaMovimento(obj.destaque, obj.alerta, obj.dhMovimento);

                    htmlTabela += '<tr '+alerta+'><td>'+
                                "<a class='fw-bold' href='?tipo=Chegada&funcao=Alteração&movimento=Chegada&idChegada="+obj.id+
                                    "&idUltimo="+obj.idMovimento+"' title='Alterar voo'>"+obj.voo+"</a></td>";
                    // Conectar ou desconectar a partida verificando se o voo está atrelado a um status
                    if (obj.vooPartida == "") {
                        htmlTabela +=
                            '<td>'+
                            (obj.statusChegada == "" ?
                                '<a href="?tipo=Chegada&funcao=Conectar&movimento=Partida&idChegada='+obj.id+
                                    '"><img src="../ativos/img/conectar.png" title="Conectar partida"/></a>' : '') +                            
                            '</td>'; 
                    } else {
                        htmlTabela += 
                            '<td>'+
                            (obj.statusChegada == "" ?
                                '<a style="padding-right:10px;" href="?tipo=Chegada&funcao=Desconectar&idChegada='+obj.id+
                                    '&idPartida='+obj.idPartida+'&idStatus='+obj.idStatusChegada+'">'+
                                        '<img src="../ativos/img/desconectar.png" title="Desconectar partida"/></a>' : '')+
                            "<a class='fw-bold' href='?tipo=Partida&funcao=Alteração&movimento=Partida&idPartida="+obj.idPartida+
                                    "' title='Alterar voo'>"+obj.vooPartida+"</a>"+                            
                            '</td>';   
                    }
                    // Monta demais informações
                    htmlTabela += 
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descOrigemCompleta+'"><u>'+obj.origem+'</u></td>'+     
                                '<td>'+obj.dhPrevista+'</td>'+
                                '<td>'+obj.dhConfirmada+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descTipo+'"><u>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</u></td>'+
                                '<td>'+obj.equipamento+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td>'+obj.pax+'</td>'+
                                '<td>'+obj.pnae+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.codeshare+'"><u>'+obj.parteCodeshare+'</u></td>';
                
                    // Destaca o movimento
                    destaque = "class='fw-bold "+(obj.destaque != '' ? "table-"+obj.destaque : '')+"'";

                    // Movimento
                    htmlTabela += '<td '+destaque+'>'+
                                "<a href='?tipo=Chegada&funcao=Alteração&movimento="+obj.descMovimento+"&idChegada="+obj.id+
                                "&idMovimento="+obj.idMovimento+"&idUltimo="+obj.idMovimento+
                                "'><img src='../ativos/img/alterar.png' title='Alterar movimento'/></a>"+obj.descMovimento+'</td>';
                                
                    // Data e hora do movimento e Recursos                        
                    htmlTabela += '<td>'+obj.dataHoraMovimento+'</td><td>'+obj.posicao+'</td>'+'<td>'+obj.esteira+'</td>';
                                
                    // Define as ações disponíveis
                    htmlTabela += '<td>';
                    htmlTabela += `<a href="?tipo=Chegada&funcao=Inclusão&movimento=Movimento&idChegada=${obj.id}&idMovimento=&idUltimo=${obj.idMovimento}">`+
                                    `<img src="../ativos/img/novo.png" title="Incluir movimento"/></a>`; 
                    htmlTabela += `<a href="?tipo=Chegada&evento=excluir&movimento=Movimento&idChegada=${obj.id}&idMovimento=&idUltimo=${obj.idMovimento}" `+
                                    `onclick="return confirm('Chegada ${obj.voo} - Confirma a exclusão do último movimento?');">`+
                                    `<img src="../ativos/img/excluir.png" title="Excluir último movimento"/></a>`;
                    htmlTabela += `<a href="?tipo=Chegada&funcao=Visualizar&movimento=Visualizar&idChegada=${obj.id}&idMovimento=&idUltimo=${obj.idMovimento}">`+ 
                                    `<img src="../ativos/img/visualizar.png" title="Visualizar chegada"/></a>`;                                     
                    htmlTabela += '</td></tr>';
                });
                htmlTabela += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            htmlFiltro = '';
            if (descricaoFiltro != '') {
                htmlFiltro = '<li class="header">'+
                                '<img src="../ativos/img/pesquisar.png" title="Filtro" style="width:20px; height:20px;"/>'+
                                descricaoFiltro.replace("<br>", "[").replace(/<br>/g,"] [")+']</li>';
            }
            divChegadas.innerHTML = htmlFiltro + htmlTabela;
        });
    } catch (error) {
        divChegadas.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};

// Carregar VISUALIZAR CHEGADAS - a página deve chamar o modalVisualizar()
//
async function opVisualizarChegada(filtro) {
    $('.carregando').show();    
    var divVisualizar = document.getElementById("divVisualizar");
    var htmlTabela = '';
    var vooAnterior = '';
    // Filtra operação por chegadas
    filtro += " AND vo.operacao = 'CHG'";
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=VoosOperacionais&filtro='+encodeURIComponent(filtro), function (dados){
            if (dados != null) {
                // Inicia a tabela de informações do voo
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Partida</th>"+
                        "<th>Origem</th>"+
                        "<th>Dh.Previsão</th>"+
                        "<th>Dh.Confirmada</th>"+
                        "<th>Tipo</th>"+
                        "<th>Equipamento</th>"+
                        "<th>Assentos</th>"+
                        "<th>PAX</th>"+
                        "<th>PNAE</th>"+
                        "<th>Posição</th>"+
                        "<th>Esteira</th>"+
                        "<th>Codeshare</th>"+
                        "</tr></thead><tbody>";
                $.each(dados, function(i, obj){
                    if (obj.voo != vooAnterior){
                        // Completa a tabela com as informações do voo 
                        htmlTabela += '<tr>'+
                            "<td><a class='fw-bold' href='?tipo=Partida&funcao=Alteração&movimento=Partida&idPartida="+obj.idPartida+
                                "' title='Alterar voo'>"+obj.vooPartida+"</a></td>"+ 
                            '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descOrigemCompleta+'"><u>'+obj.origem+'</u></td>'+     
                            '<td>'+obj.dhPrevista+'</td>'+
                            '<td>'+obj.dhConfirmada+'</td>'+
                            '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descTipo+'"><u>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</u></td>'+
                            '<td>'+obj.equipamento+'</td>'+
                            '<td>'+obj.assentos+'</td>'+
                            '<td>'+obj.pax+'</td>'+
                            '<td>'+obj.pnae+'</td>'+
                            '<td>'+obj.posicao+'</td>'+
                            '<td>'+obj.esteira+'</td>'+
                            '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.codeshare+'"><u>'+obj.parteCodeshare+'</u></td>'+
                            '</tr></tbody></table>';
                        // Tabela com as informações do status
                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-sm'><thead class='table-info'><tr>"+
                                        "<th>Status</th></tr></thead><tbody><tr><td>"+
                                        "<a class='fw-bold' href='?tipo=Status&funcao=Alteração&movimento=Status&idStatus="+obj.idStatusChegada+
                                        "' title='Alterar status'>"+obj.statusChegada+"</a></td></tr></tbody></table>";
                        // Inicia a tabela com as informações dos movimentos do voo
                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-sm'><thead class='table-info'><tr>"+
                                        "<th>Movimento</th><th>Dh.Movimento</th><th>Recurso</th><th>Usuário</th></tr></thead><tbody>";                        
                        vooAnterior = obj.voo;                            
                    }
                    // Completa a tabela com as informações dos movimentos do voo
                    htmlTabela += '<tr>'+
                        '<td>'+obj.descMovimento+'</td>'+
                        '<td>'+obj.dataHoraMovimento+'</td>'+
                        '<td>'+obj.recurso+'</td>'+
                        '<td>'+obj.usuario+'</td>'+
                        '</tr>';
                });
                htmlTabela += "</tbody></table>";
            }
            labelVisualizar.innerHTML = "<h5>Chegada "+vooAnterior+"</h5>";
            divVisualizar.innerHTML = htmlTabela;
        });
    } catch (error) {
        divVisualizar.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};

// Carregar PAINEL PARTIDAS - a página deve ter divPartidas
//
async function opPainelPartidas(funcao, filtro = '', ordem = '', descricaoFiltro = '', busca = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divPartidas = document.getElementById("divPartidas");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    // Filtra operação por partidas
    filtro += " AND vo.operacao = 'PRT'";
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=UltimosMovimentosVoos&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&busca='+busca+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                        "<thead class='table-info'><tr>"+
                        "<th>Voo</th><th>Chegada</th><th>Destino</th><th>Dh.Previsão</th><th>Dh.Confirmada</th><th>Tipo</th>"+
                        "<th>Equipamento</th><th>Assentos</th><th>PAX</th><th>PNAE</th><th>Codeshare</th>"+
                        "<th>Movimento</th><th>Dh.Movimento</th><th>Posição</th><th>Portão</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                $.each(dados, function(i, obj){
                    // Alerta do movimento
                    alerta = opVerificarAlertaMovimento(obj.destaque, obj.alerta, obj.dhMovimento);

                    htmlTabela += '<tr '+alerta+'><td>'+
                                "<a class='fw-bold' href='?tipo=Partida&funcao=Alteração&movimento=Partida&idPartida="+obj.id+
                                "&idUltimo="+obj.idMovimento+"' title='Alterar voo'>"+obj.voo+"</a></td>";  
                    // Conectar ou desconectar a partida verificando se o voo está atrelado a um status
                    if (obj.vooChegada == "") {
                        htmlTabela +=
                            '<td>'+
                            (obj.statusPartida == "" ?
                                '<a href="?tipo=Partida&funcao=Conectar&movimento=Chegada&idPartida='+obj.id+
                                    '"><img src="../ativos/img/conectar.png" title="Conectar chegada"/></a>' : '') + 
                            '</td>'; 
                    } else {
                        htmlTabela += 
                            '<td>'+
                            (obj.statusPartida == "" ?
                                '<a style="padding-right:10px;" href="?tipo=Partida&funcao=Desconectar&idPartida='+obj.id+
                                    '&idChegada='+obj.idChegada+'&idStatus='+obj.idStatusPartida+'">'+
                                        '<img src="../ativos/img/desconectar.png" title="Desconectar chegada"/></a>' : '')+
                            "<a class='fw-bold' href='?tipo=Chegada&funcao=Alteração&movimento=Chegada&idChegada="+obj.idChegada+
                                    "' title='Alterar voo'>"+obj.vooChegada+"</a>"+
                            '</td>';
                    }
                    // Monta demais informações  
                    htmlTabela +=                                     
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descDestinoCompleto+'"><u>'+obj.destino+'</u></td>'+  
                                '<td>'+obj.dhPrevista+'</td>'+
                                '<td>'+obj.dhConfirmada+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descTipo+'"><u>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</u></td>'+
                                '<td>'+obj.equipamento+'</td>'+
                                '<td>'+obj.assentos+'</td>'+
                                '<td>'+obj.pax+'</td>'+
                                '<td>'+obj.pnae+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.codeshare+'"><u>'+obj.parteCodeshare+'</u></td>';

                    // Destaca o movimento
                    destaque = "class='fw-bold "+(obj.destaque != '' ? "table-"+obj.destaque : '')+"'";

                    // Movimento
                    htmlTabela += '<td '+destaque+'>'+
                                "<a href='?tipo=Partida&funcao=Alteração&movimento="+obj.descMovimento+"&idPartida="+obj.id+
                                "&idMovimento="+obj.idMovimento+"&idUltimo="+obj.idMovimento+
                                "'><img src='../ativos/img/alterar.png' title='Alterar movimento'/></a>"+obj.descMovimento+'</td>';
                                
                    // Data e hora do movimento e Recursos                        
                    htmlTabela += '<td>'+obj.dataHoraMovimento+'</td><td>'+obj.posicao+'</td>'+'<td>'+obj.portao+'</td>';

                    // Define as ações disponíveis
                    htmlTabela += '<td>';
                    htmlTabela +=  `<a href="?tipo=Partida&funcao=Inclusão&movimento=Movimento&idPartida=${obj.id}&idMovimento=&idUltimo=${obj.idMovimento}">`+
                                    `<img src="../ativos/img/novo.png" title="Incluir movimento"/></a>`; 
                    htmlTabela += `<a href="?tipo=Partida&evento=excluir&movimento=Movimento&idPartida=${obj.id}&idMovimento=&idUltimo=${obj.idMovimento}" `+
                                    `onclick="return confirm('Partida ${obj.voo} - Confirma a exclusão do último movimento?');">`+
                                    `<img src="../ativos/img/excluir.png" title="Excluir último movimento"/></a>`;
                    htmlTabela += `<a href="?tipo=Partida&funcao=Visualizar&movimento=Visualizar&idPartida=${obj.id}&idMovimento=&idUltimo=${obj.idMovimento}">`+ 
                                    `<img src="../ativos/img/visualizar.png" title="Visualizar partida"/></a>`;                                     
                    htmlTabela += '</td></tr>';
                });
                htmlTabela += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            htmlFiltro = '';
            if (descricaoFiltro != '') {
                htmlFiltro = '<li class="header">'+
                                '<img src="../ativos/img/pesquisar.png" title="Filtro" style="width:20px; height:20px;"/>'+
                                descricaoFiltro.replace("<br>", "[").replace(/<br>/g,"] [")+']</li>';
            }
            divPartidas.innerHTML = htmlFiltro + htmlTabela;
        });
    } catch (error) {
        divPartidas.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};

// Carregar VISUALIZAR PARTIDAS - a página deve chamar o modalVisualizar()
//
async function opVisualizarPartida(filtro) {
    $('.carregando').show();    
    var labelVisualizar = document.getElementById("labelVisualizar");
    var divVisualizar = document.getElementById("divVisualizar");
    var htmlTabela = '';
    var vooAnterior = '';
    // Filtra operação por partidas
    filtro += " AND vo.operacao = 'PRT'";
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=VoosOperacionais&filtro='+encodeURIComponent(filtro), function (dados){
            if (dados != null) {
                // Inicia a tabela de informações do voo
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Chegada</th>"+
                        "<th>Destino</th>"+
                        "<th>Dh.Previsão</th>"+
                        "<th>Dh.Confirmada</th>"+
                        "<th>Tipo</th>"+
                        "<th>Equipamento</th>"+
                        "<th>Assentos</th>"+
                        "<th>PAX</th>"+
                        "<th>PNAE</th>"+
                        "<th>Posição</th>"+
                        "<th>Portão</th>"+
                        "<th>Codeshare</th>"+
                        "</tr></thead><tbody>";
                $.each(dados, function(i, obj){
                    if (obj.voo != vooAnterior){
                        // Completa a tabela com as informações do voo 
                        htmlTabela += '<tr>'+
                            "<td><a class='fw-bold' href='?tipo=Chegada&funcao=Alteração&movimento=Chegada&idChegada="+obj.idChegada+
                                "' title='Alterar voo'>"+obj.vooChegada+"</a></td>"+  
                            '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descDestinoCompleto+'"><u>'+obj.destino+'</u></td>'+  
                            '<td>'+obj.dhPrevista+'</td>'+
                            '<td>'+obj.dhConfirmada+'</td>'+
                            '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descTipo+'"><u>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</u></td>'+
                            '<td>'+obj.equipamento+'</td>'+
                            '<td>'+obj.assentos+'</td>'+
                            '<td>'+obj.pax+'</td>'+
                            '<td>'+obj.pnae+'</td>'+
                            '<td>'+obj.posicao+'</td>'+
                            '<td>'+obj.portao+'</td>'+
                            '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.codeshare+'"><u>'+obj.parteCodeshare+'</u></td>'+
                            '</tr></tbody></table>';
                        // Tabela com as informações do status
                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-sm'><thead class='table-info'><tr>"+
                                        "<th>Status</th></tr></thead><tbody><tr><td>"+
                                        "<a class='fw-bold' href='?tipo=Status&funcao=Alteração&movimento=Status&idStatus="+obj.idStatusPartida+
                                        "' title='Alterar status'>"+obj.statusPartida+"</a></td></tr></tbody></table>";
                        // Inicia a tabela com as informações dos movimentos do voo
                        htmlTabela += "<table class='table table-striped table-hover table-bordered table-sm'><thead class='table-info'><tr>"+
                                        "<th>Movimento</th><th>Dh.Movimento</th><th>Recurso</th><th>Usuário</th></tr></thead><tbody>"; 
                        vooAnterior = obj.voo;                            
                    }
                    // Completa a tabela com as informações dos movimentos do
                    htmlTabela += '<tr>'+
                        '<td>'+obj.descMovimento+'</td>'+
                        '<td>'+obj.dataHoraMovimento+'</td>'+
                        '<td>'+obj.recurso+'</td>'+
                        '<td>'+obj.usuario+'</td>'+
                        '</tr>';
                });
                htmlTabela += "</tbody></table>";
            }
            labelVisualizar.innerHTML = "<h5>Partida "+vooAnterior+"</h5>";
            divVisualizar.innerHTML = htmlTabela;
        });
    } catch (error) {
        divVisualizar.innerHTML = exibirErro(error);
    }        
    $('.carregando').hide();
};

// Carregar PAINEL STATUS - a página deve ter divStatus
//
async function opPainelStatus(funcao, filtro = '', ordem = '', descricaoFiltro = '', busca = '', pagina = 0, limite = 0) {
    $('.carregando').show();    
    var divStatus = document.getElementById("divStatus");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=UltimosMovimentosStatus&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&busca='+busca+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'>"+
                        "<thead class='table-info'><tr>"+
                        "<th>Status</th><th>Matrícula</th><th>Equipamento</th><th>Tipo</th><th>Origem</th><th>Chegada</th>"+
                        "<th>Destino</th><th>Partida</th><th>Movimento</th><th>Dh.Movimento</th><th>Pista</th><th>Posição</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    // Alerta do movimento
                    alerta = opVerificarAlertaMovimento(obj.destaque, obj.alerta, obj.dhMovimento);

                    htmlTabela += '<tr '+alerta+'><td>'+
                        "<a class='fw-bold' href='?tipo=Status&funcao=Alteração&movimento=Status&idStatus="+obj.id+
                        "&idMovimento=&idUltimo="+obj.idMovimento+"' title='Alterar status'>"+obj.status+"</a></td>"+
                        '<td>'+obj.matricula+'</td>'+
                        '<td data-toggle="tooltip" data-placement="bottom" title="Modelo: '+obj.modelo+'"><u>'+obj.equipamento+'</u></td>'+
                        '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descTipo+'"><u>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</u></td>'+
                        '<td>'+obj.origem+'</td>';

                    if (obj.vooChegada == "") {
                        htmlTabela += '<td><a href="?tipo=Status&funcao=Conectar&movimento=Chegada&idStatus='+obj.id+
                                        '"><img src="../ativos/img/conectar.png" title="Conectar chegada"/></a></td>'; 
                    } else {
                        htmlTabela += 
                            '<td>'+
                            '<a style="padding-right:10px;" href="?tipo=Status&funcao=Desconectar&movimento=Chegada&idStatus='+obj.id+
                                '"><img src="../ativos/img/desconectar.png" title="Desconectar chegada"/></a>'+
                            "<a class='fw-bold' href='?tipo=Chegada&funcao=Alteração&movimento=Chegada&idChegada="+obj.idChegada+
                                "' title='Alterar voo'>"+obj.vooChegada+"</a>"+
                            '</td>';
                    }
                    htmlTabela += '<td>'+obj.destino+'</td>';

                    if (obj.vooPartida == "") {
                        htmlTabela += '<td><a href="?tipo=Status&funcao=Conectar&movimento=Partida&idStatus='+obj.id+
                                        '"><img src="../ativos/img/conectar.png" title="Conectar partida"/></a></td>'; 
                    } else {
                        htmlTabela += 
                            '<td>'+
                            '<a style="padding-right:10px;" href="?tipo=Status&funcao=Desconectar&movimento=Partida&idStatus='+obj.id+
                                '"><img src="../ativos/img/desconectar.png" title="Desconectar partida"/></a>'+
                            "<a class='fw-bold' href='?tipo=Partida&funcao=Alteração&movimento=Partida&idPartida="+obj.idPartida+
                                "' title='Alterar voo'>"+obj.vooPartida+"</a>"+                            
                            '</td>';   
                    }
                    
                    // Destaca o movimento
                    destaque = "class='fw-bold "+(obj.destaque != '' ? "table-"+obj.destaque : '')+"'";

                    // Movimento
                    htmlTabela += '<td '+destaque+'>'+
                                "<a href='?tipo=Status&funcao=Alteração&movimento="+obj.descMovimento+
                                "&idStatus="+obj.id+"&idMovimento="+obj.idMovimento+"&idUltimo="+obj.idMovimento+
                                "'><img src='../ativos/img/alterar.png' title='Alterar movimento'/></a>"+obj.descMovimento+'</td>';

                    // Data e hora do movimento e Recursos                        
                    htmlTabela += '<td>'+obj.dataHoraMovimento+'</td>'+
                                (obj.tipoRecurso == 'PIS' ? '<td>'+obj.descRecurso+'</td><td></td>' : '<td></td><td>'+obj.descRecurso+'</td>');                            
                                
                    // Define as ações disponíveis
                    htmlTabela += '<td>'+opMontaHtmlAcoes(obj.status, obj.id, obj.idMovimento)+'</td></tr>';
                });

                htmlTabela += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            htmlFiltro = '';
            if (descricaoFiltro != '') {
                htmlFiltro = '<li class="header">'+
                                '<img src="../ativos/img/pesquisar.png" title="Filtro" style="width:20px; height:20px;"/>'+
                                descricaoFiltro.replace("<br>", "[").replace(/<br>/g,"] [")+']</li>';
            }
            divStatus.innerHTML = htmlFiltro + htmlTabela;
        });
    } catch (error) {
        divStatus.innerHTML = exibirErro(error);
    }
    $('.carregando').hide();
};

// Carregar VISUALIZAR STATUS - a página deve chamar o modalVisualizar()
//
async function opVisualizarStatus(filtro) {
    $('.carregando').show();        
    var labelVisualizar = document.getElementById("labelVisualizar");
    var divVisualizar = document.getElementById("divVisualizar");
    var htmlTabela = '';
    var statusAnterior = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=Status&filtro='+encodeURIComponent(filtro), function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Matrícula</th><th>Equipamento</th><th>Tipo</th><th>Origem</th><th>Chegada</th><th>Destino</th><th>Partida</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    if (obj.status != statusAnterior){
                        // Se opção de cadastrar e não faturado, libera chamada do formulário
                        htmlTabela += '<tr>'+
                                '<td>'+obj.matricula+'</td>'+
                                '<td>'+obj.equipamento+' - '+obj.modelo+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="'+obj.descTipo+'"><u>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</u></td>'+
                                '<td>'+obj.origem+'</td>'+
                                "<td><a class='fw-bold' href='?tipo=Chegada&funcao=Alteração&movimento=Chegada&idChegada="+obj.idChegada+
                                    "' title='Alterar voo'>"+obj.vooChegada+"</a></td>"+
                                '<td>'+obj.destino+'</td>'+
                                "<td><a class='fw-bold' href='?tipo=Partida&funcao=Alteração&movimento=Partida&idPartida="+obj.idPartida+
                                    "' title='Alterar voo'>"+obj.vooPartida+"</a></td>"+
                                "</tr></tbody></table>";
                        // Comando
                        htmlTabela += 
                                "<table class='table table-striped table-hover table-bordered table-sm'><thead class='table-info'><tr>"+
                                "<th>Comandante</th><th>Email</th><th>Regra</th></tr></thead><tbody>"+
                                "<tr><td>"+obj.comandante+"</td><td>"+obj.email+"</td><td>"+obj.descRegra+"</td></tr></tbody></table>"; 
                                
                        // Desembarque e Embarque  
                        htmlTabela += 
                                "<table class='table table-striped table-hover table-bordered table-sm'><thead class='table-info'><tr>"+
                                "<th colspan=3 style='text-align: center'>Desembarque</th>"+
                                "<th colspan=3 style='text-align: center'>Embarque</th>"+
                                "<th style='text-align: center'>Trânsito</th></tr>"+
                                "<th>Passageiros</th><th>Carga</th><th>Correio</th>"+
                                "<th>Passageiros</th><th>Carga</th><th>Correio</th><th>Passageiros</th></thead><tbody>"+
                                "<tr><td>"+obj.desembarque_pax+"</td><td>"+obj.desembarque_carga+"</td><td>"+obj.desembarque_correio+"</td>"+
                                "<td>"+obj.embarque_pax+"</td><td>"+obj.embarque_carga+"</td><td>"+obj.embarque_correio+"</td>"+
                                "<td>"+obj.transito_pax+"</td></tr></tbody></table>";   

                        // Observação
                        htmlTabela += 
                                "<table class='table table-striped table-hover table-bordered table-sm'><thead class='table-info'><tr>"+
                                "<th style='text-align: center'>Observação</th></tr></thead><tbody>"+
                                "<tr><td>"+obj.observacao+"</td></tr></tbody></table>"; 
                                
                        // Finaliza a tabela com as informações do status e abre a de movimento
                        htmlTabela += 
                                "<table class='table table-striped table-hover table-bordered table-sm'><thead class='table-info'><tr>"+
                                "<th>Movimento</th><th>Dh.Movimento</th><th>Pista</th><th>Posição</th><th>Usuário</th></tr></thead><tbody>";                              
                        statusAnterior = obj.status;                            
                    }
                    htmlTabela += '<tr>'+
                        '<td>'+obj.descMovimento+'</td>'+
                        '<td>'+obj.dataHoraMovimento+'</td>'+
                        // Recursos
                        (obj.tipoRecurso == 'PIS' ? '<td>'+obj.descRecurso+'</td><td></td>' : '<td></td><td>'+obj.descRecurso+'</td>')+
                        '<td>'+obj.usuario+'</td>'+                        
                        '</tr>';
                });
                htmlTabela += "</tbody></table>";
            }
            labelVisualizar.innerHTML = "<h5>Status "+statusAnterior+"</h5>";
            divVisualizar.innerHTML = htmlTabela;
        });
    } catch (error) {
        divVisualizar.innerHTML = exibirErro(error);
    }        
    $('.carregando').hide();
};

// Funções AUXILIARES
//
function opMontaHtmlAcoes(status, id, idMovimento) {
    var htmlRetorno = '';
    htmlRetorno += `<a href="?tipo=StatusComplementos&funcao=Complementos&movimento=&idStatus=${id}&idMovimento=&idUltimo=">`+ 
                    `<img src="../ativos/img/informacoes.png" title="Informações complementares"/></a>`;  
    htmlRetorno += `<a href="?tipo=Status&funcao=Inclusão&movimento=Movimento&idStatus=${id}&idMovimento=&idUltimo=${idMovimento}">`+
                    `<img src="../ativos/img/novo.png" title="Incluir movimento"/></a>`; 
    htmlRetorno += `<a href="?tipo=Status&evento=excluir&movimento=movimento&idStatus=${id}&idMovimento=&idUltimo=${idMovimento}" `+ 
                    `onclick="return confirm('Status ${status} - Confirma a exclusão do último movimento?');">`+
                    '<img src="../ativos/img/excluir.png" title="Excluir último movimento"/></a>';
    htmlRetorno += `<a href="?tipo=Status&funcao=Visualizar&movimento=&idStatus=${id}&idMovimento=&idUltimo=${idMovimento}">`+ 
                    `<img src="../ativos/img/visualizar.png" title="Visualizar status"/></a>`;  
    return htmlRetorno;
}

// Verificar alerta
function opVerificarAlertaMovimento(destaque, alerta, dhMovimento){
    var htmlRetorno = '';
    if (alerta != 0) {
        const diff = diferenca(new Date(dhMovimento), new Date());
        if (diff >= alerta) {
            htmlRetorno = "class='fw-bold "+(destaque != '' ? "table-"+destaque : "table-danger")+"'";
        }
    }
    return htmlRetorno;

    function diferenca(dtAnterior, dtAtual) {
        return (dtAtual.getTime() - dtAnterior.getTime()) / (60000);
    }
}
// ***************************************************************************************************