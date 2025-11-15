// Carregar STATUS NÃO FATURADOS a página deve ter divTituloTabela e divTabela
//
async function faCarregarStatusNaoFaturados(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var operadorAnterior = '';
    var situacao = '';
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=StatusFaturamento&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Operador</th><th>Status</th><th>Matrícula</th><th>Tipo</th><th>Origem</th><th>Destino</th>"+
                        "<th>Primeiro Movimento</th><th>Último Movimento</th><th>PAT</th><th>EST</th><th>ISE</th>"+                    
                        "<th>PPO</th><th>PPM</th><th>PPE</th><th>Situação</th>"+
                        "</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Operador</th><th>Status</th><th>Matrícula</th><th>Tipo</th><th>Origem</th><th>Destino</th>"+
                        "<th>Primeiro Movimento</th><th>Último Movimento</th><th>PAT</th><th>EST</th><th>ISE</th>"+                    
                        "<th>PPO</th><th>PPM</th><th>PPE</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr>'
                    htmlImpressao += '<tr>';
                    if (obj.operadorOperacao != operadorAnterior) {
                        htmlTabela += '<td class="fw-bold">'+obj.operadorOperacao+'</td>';
                        htmlImpressao += '<td class="fw-bold">'+obj.operadorOperacao+'</td>';
                        operadorAnterior = obj.operadorOperacao;
                    } else {
                        htmlTabela += '<td></td>';
                        htmlImpressao += '<td></td>';
                    }
                    // Resume a situação do calculo e do faturamento
                    situacao = ''
                    switch(obj.situacaoFaturamento) {
                        case "CNF":  
                            situacao = '<td  class="fw-bold table-warning">'+obj.descFaturamento+' em '+obj.dhConfirmacaoFaturamento+'</td>';
                        break;
                        case "PRC":  
                            switch(obj.situacaoCalculo) {
                                case "PEN":  
                                    situacao = '<td  class="fw-bold table-danger">'+obj.descCalculo+'</td>';
                                break;   
                                case "NCN":  
                                    situacao = '<td  class="fw-bold table-success">'+obj.descCalculo+'</td>';
                                break;                             
                            }
                        break;
                        default:
                            situacao = '<td>'+obj.descCalculo+'</td>';
                    }

                    htmlTabela +=
                                '<td class="fw-bold">'+obj.status+'</u></td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="PMD:'+obj.pmd+' Grupo: '+
                                    (obj.vooChegada == '' ? '2' : '1')+'"><u>'+obj.matricula+'</u></td>'+
                                '<td>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</td>'+
                                (obj.vooChegada == '' ? '<td>'+obj.origem+'</td>' : 
                                    '<td data-toggle="tooltip" data-placement="bottom" title="Chegada: '+obj.vooChegada+'"><u>'+obj.origem+'</u></td>')+
                                (obj.vooPartida == '' ? '<td>'+obj.destino+'</td>' : 
                                    '<td data-toggle="tooltip" data-placement="bottom" title="Partida: '+obj.vooPartida+'"><u>'+obj.destino+'</u></td>')+
                                '<td>'+obj.moPrimeiroMovimento+' - '+obj.dataHoraPrimeiroMovimento+'</td>'+
                                '<td>'+obj.moUltimoMovimento+' - '+obj.dataHoraUltimoMovimento+'</td>'+
                                '<td>'+obj.tmpPatio+'</td>'+
                                '<td>'+obj.tmpEstadia+'</td>'+
                                '<td>'+obj.tmpIsento+'</td>'+
                                "<td align='right'>"+Number(obj.vlrPPO).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.vlrPPM).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.vlrPPE).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+                                                   
                                situacao+
                                '</tr>';

                    htmlImpressao +=
                                '<td class="fw-bold">'+obj.status+'</td>'+
                                '<td>'+obj.matricula+'</td>'+
                                '<td>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</td>'+
                                '<td>'+obj.origem+'</td>'+
                                '<td>'+obj.destino+'</td>'+
                                '<td>'+obj.moPrimeiroMovimento+' - '+obj.dataHoraPrimeiroMovimento+'</td>'+
                                '<td>'+obj.moUltimoMovimento+' - '+obj.dataHoraUltimoMovimento+'</td>'+
                                '<td>'+obj.tmpPatio+'</td>'+
                                '<td>'+obj.tmpEstadia+'</td>'+
                                '<td>'+obj.tmpIsento+'</td>'+
                                "<td align='right'>"+Number(obj.vlrPPO).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.vlrPPM).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.vlrPPE).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+                          
                                situacao+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Status pendentes de Faturamento - ["+qtdRegistros+"]</H4>";
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

// Carregar STATUS FATURADOS - a página deve ter divTituloTabela e divTabela
//
async function faCarregarStatusFaturados(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0, arquivo = '') {
    $('.carregando').show();
    var divTitulo = document.getElementById("divTitulo");
    var divPagina = document.getElementById('divPagina');
    var divTabela = document.getElementById("divTabela");
    var divImpressao = document.getElementById("divImpressao");
    var qtdRegistros = 0;
    var qtdTotalRegistros = 0;
    var htmlTabela = '';
    var htmlImpressao = '';
    var faturamentoAnterior = '';
    var boolAcao = true;
    try {
        await $.getJSON('../suporte/suBuscar.php?funcao=StatusFaturamento&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Faturamento</th><th>Remessa ASAAS</th><th>Operador</th>"+
                        "<th>Status</th><th>Matrícula</th><th>Tipo</th><th>Origem</th><th>Destino</th>"+
                        "<th>Primeiro Movimento</th><th>Último Movimento</th><th>PAT</th><th>EST</th><th>ISE</th>"+                    
                        "<th>PPO</th><th>PPM</th><th>PPE</th><th>Situação</th>"+
                        (funcao == 'Cadastrar' ? "<th>Ação</th>" : "")+"</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Faturamento</th><th>Remessa ASAAS</th><th>Operador</th>"+
                        "<th>Status</th><th>Matrícula</th><th>Tipo</th><th>Origem</th><th>Destino</th>"+
                        "<th>Primeiro Movimento</th><th>Último Movimento</th><th>PAT</th><th>EST</th><th>ISE</th>"+                    
                        "<th>PPO</th><th>PPM</th><th>PPE</th><th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    if (obj.faturamento != faturamentoAnterior){
                        htmlTabela += '<tr><td class="fw-bold">'+obj.faturamento+' '+obj.dhConfirmacaoFaturamento+'</td>'+
                                '<td class="fw-bold">'+obj.remessa+'</td>'+
                                '<td class="fw-bold">'+obj.operadorOperacao+'</td>';
                        htmlImpressao += '<tr><td class="fw-bold">'+obj.faturamento+' '+obj.dhConfirmacaoFaturamento+'</td>'+
                                '<td class="fw-bold">'+obj.remessa+'</td>'+
                                '<td class="fw-bold">'+obj.operadorOperacao+'</td>';                                
                        faturamentoAnterior = obj.faturamento;  
                        boolAcao = true;
                    } else {
                        htmlTabela += '<tr><td></td><td></td><td></td>';
                        htmlImpressao += '<tr><td></td><td></td><td></td>'; 
                        boolAcao = false;
                    }          
                    
                    // Resume a situação da emissão e do pagamento
                    situacao = ''
                    bCancelarFatura = false;
                    bEmitirFatura = false;
                    bCancelarEmissao = false;
                    bPagarFatura = false;
                    bCancelarPagamento = false;
                    switch(obj.dhPagamento) {
                        case "":
                            if (obj.dhFatura == "") {
                                situacao = '<td class="table-danger">Pendente</td>';
                                bCancelarFatura = true;
                                bEmitirFatura = true;
                            } else {
                                situacao = '<td class="fw-bold table-warning" data-toggle="tooltip" data-placement="bottom" title="Emitida em '+
                                                obj.dhFatura+'"><u>Emitida</u></td>';
                                bCancelarEmissao = true;
                                bPagarFatura = true;
                            }
                        break;
                        default:
                            if (obj.dhFatura == "") {
                                situacao = '<td class="fw-bold table-success" data-toggle="tooltip" data-placement="bottom" title="Baixada em '+
                                                obj.dhPagamento+'"><u>Baixada</u></td>';
                            } else {
                                situacao = '<td class="fw-bold table-success" data-toggle="tooltip" data-placement="bottom" title="Emitida em '+
                                                obj.dhFatura+' Baixada em '+obj.dhPagamento+'"><u>Fechada</u></td>';
                                bCancelarPagamento = true;
                            }
                    }

                    htmlTabela += '<td>'+obj.status+'</td>'+
                                '<td data-toggle="tooltip" data-placement="bottom" title="PMD:'+obj.pmd+' Grupo: '+
                                    (obj.vooChegada == '' ? '2' : '1')+'"><u>'+obj.matricula+'</u></td>'+
                                '<td>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</td>'+
                                (obj.vooChegada == '' ? '<td>'+obj.origem+'</td>' : 
                                    '<td data-toggle="tooltip" data-placement="bottom" title="Chegada: '+obj.vooChegada+'"><u>'+obj.origem+'</u></td>')+
                                (obj.vooPartida == '' ? '<td>'+obj.destino+'</td>' : 
                                    '<td data-toggle="tooltip" data-placement="bottom" title="Partida: '+obj.vooPartida+'"><u>'+obj.destino+'</u></td>')+
                                '<td>'+obj.moPrimeiroMovimento+' - '+obj.dataHoraPrimeiroMovimento+'</td>'+
                                '<td>'+obj.moUltimoMovimento+' - '+obj.dataHoraUltimoMovimento+'</td>'+
                                '<td>'+obj.tmpPatio+'</td>'+
                                '<td>'+obj.tmpEstadia+'</td>'+
                                '<td>'+obj.tmpIsento+'</td>' +
                                "<td align='right'>"+Number(obj.vlrPPO).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.vlrPPM).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.vlrPPE).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                situacao;

                    if (funcao == 'Cadastrar') {
                        htmlTabela += '<td>';
                        if (boolAcao){  
                            if (bEmitirFatura) {
                                htmlTabela += '<a href="faEmitirFatura.php?id='+obj.idFaturamento+'" target="_blank">'+
                                            '<img src="../ativos/img/fatura.png" title="Faturamento à vista"'+
                                            ' onclick="return confirm(\'Confirma o faturamento à vista?\\n\\nEsta ação poderá ser cancelada posteriormente, mas implicará na obrigatoriedade de cancelamento manual da fatura emitida.\');"/></a>'; 
                            }
                            if (bCancelarEmissao) {
                                htmlTabela += '<a href="?evento=cancelarEmissaoFaturamento&idFaturamento='+obj.idFaturamento+
                                                '&faturamento='+obj.faturamento+'&dhFatura='+obj.dhFatura+'"'+
                                                ' onclick="return confirm(\'Confirma o cancelamento do faturamento?\\n\\nATENÇÃO: Verifique se haverá a necessidade de cancelamento manual da(s) fatura(s) enventualmente emitida(s).\');">'+
                                                '<img src="../ativos/img/cancelarFatura.png" title="Cancelar faturamento"/></a>'; 
                            }
                            if (bPagarFatura) {
                                htmlTabela += '<a href="?evento=atualizarPagamento&idFaturamento='+obj.idFaturamento+
                                                '&faturamento='+obj.faturamento+'&dhPagamento='+obj.dhPagamento+'"'+
                                                ' onclick="return confirm(\'Confirma a baixa da fatura?\');">'+                                                
                                                '<img src="../ativos/img/pagamento.png" title="Pagar fatura"/></a>'; 
                            }
                            if (bCancelarPagamento) {
                                htmlTabela += '<a href="?evento=atualizarPagamento&idFaturamento='+obj.idFaturamento+
                                                '&faturamento='+obj.faturamento+'&dhPagamento='+obj.dhPagamento+'"'+
                                                ' onclick="return confirm(\'Confirma o cancelamento da baixa?\');">'+
                                                '<img src="../ativos/img/cancelarPagamento.png" title="Cancelar a baixa da fatura"/></a>'; 
                            }
                            if (bCancelarFatura) {
                                htmlTabela += '<a href="?evento=cancelarFaturamentoConfirmado&idFaturamento='+obj.idFaturamento+
                                                '&faturamento='+obj.faturamento+'"'+
                                                ' onclick="return confirm(\'Confirma a cancelamento do faturamento?\\n\\nEsta ação tornará todos os status deste faturamento pendentes.\');">'+
                                                '<img src="../ativos/img/excluir.png" title="Cancelar faturamento"/></a>';  
                            }                                                                         
                        }
                        htmlTabela += '</td>';
                    }
                    htmlTabela += '</tr>';
                    
                    htmlImpressao += '<td>'+obj.status+'</td>'+
                                '<td>'+obj.matricula+'</td>'+
                                '<td>'+obj.classe+' '+obj.natureza+' '+obj.servico+'</td>'+
                                '<td>'+obj.origem+'</td>'+
                                '<td>'+obj.destino+'</td>'+
                                '<td>'+obj.moPrimeiroMovimento+' - '+obj.dataHoraPrimeiroMovimento+'</td>'+
                                '<td>'+obj.moUltimoMovimento+' - '+obj.dataHoraUltimoMovimento+'</td>'+
                                '<td>'+obj.tmpPatio+'</td>'+
                                '<td>'+obj.tmpEstadia+'</td>'+
                                '<td>'+obj.tmpIsento+'</td>'+
                                "<td align='right'>"+Number(obj.vlrPPO).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.vlrPPM).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                "<td align='right'>"+Number(obj.vlrPPE).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+"</td>"+
                                situacao+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Movimentos Faturados - ["+qtdRegistros+"]</H4>";
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

// Carregar REMESSAS a página deve ter divTituloTabela e divTabela
//
async function faCarregarRemessas(funcao, filtro = '', ordem = '', descricaoFiltro = '', pagina = 0, limite = 0) {
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
        await $.getJSON('../suporte/suBuscar.php?funcao=Remessas&filtro='+encodeURIComponent(filtro)+'&ordem='+ordem+'&pagina='+pagina+'&limite='+limite, function (dados){
            if (dados != null) {
                htmlTabela += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead class='table-info'><tr>"+
                        "<th>Remessa</th><th>Data</th><th>Qtd Faturas</th><th>Qtd Linhas</th><th>Valor Total</th><th>Usuario</th>"+
                        "<th>Situação</th>"+
                        "</tr></thead><tbody>";
                htmlImpressao += "<table class='table table-striped table-hover table-bordered table-reduzida table-sm'><thead><tr>"+
                        "<th>Remessa</th><th>Data</th><th>Qtd Faturas</th><th>Qtd Linhas</th><th>Valor Total</th><th>Usuario</th>"+
                        "<th>Situação</th>"+
                        "</tr></thead><tbody>";

                $.each(dados, function(i, obj){
                    htmlTabela += '<tr><td>'+
                                (funcao == 'Cadastrar' ? "<a href='?evento=recuperar&id="+obj.id+"' title='Alterar registro'>"+
                                obj.remessa+"</a>" : obj.remessa) + '</td>'+
                                '<td>'+obj.dataHoraCadastro+'</td>'+
                                '<td>'+obj.qtdFaturas+'</td>'+
                                '<td>'+obj.qtdLinhas+'</td>'+
                                '<td>'+Number(obj.vlrTotal).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+obj.usuario+'</td>'+
                                '<td>'+obj.descSituacao+'</td>'+
                                (funcao == 'Cadastrar' ? 
                                    '<td><center>'+
                                        '<a href="?evento=excluir&id='+obj.id+'"'+ 
                                        ' onclick="return confirm(\'Confirma a exclusão do registro?\');">'+
                                        '<img src="../ativos/img/excluir.png" title="Excluir registro"/></a>'+
                                    '</center></td>' : '')+
                                '</tr>';
                    htmlImpressao += '<tr><td>'+obj.remessa+'</td>'+
                                '<td>'+obj.dataHoraCadastro+'</td>'+
                                '<td>'+obj.qtdFaturas+'</td>'+
                                '<td>'+obj.qtdLinhas+'</td>'+
                                '<td>'+Number(obj.vlrTotal).toLocaleString('pt-BR', {style: 'decimal', minimumFractionDigits: 2})+'</td>'+
                                '<td>'+obj.usuario+'</td>'+                            
                                '<td>'+obj.descSituacao+'</td>'+
                                '</tr>';
                });
                htmlTabela += "</tbody></table>";
                htmlImpressao += "</tbody></table>";
                qtdRegistros = dados.length;
                qtdTotalRegistros = dados[0]['total'];            
            }
            divTitulo.innerHTML = "<H4>Remessas - ["+qtdRegistros+"]</H4>";
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
//
// Carregar SELECT
//
// ***************************************************************************************************
// ***************************************************************************************************