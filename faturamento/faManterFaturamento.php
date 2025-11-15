<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../faturamento/faFuncoes.php");
verificarExecucao();

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']);  

// Recuperando as informações do Aeroporto
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];
$usuario = $_SESSION['plantaUsuario'];
$idUsuario = $_SESSION['plantaIDUsuario'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = "show"; // ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $faturamento = carregarPosts("faturamento", "");
    $idFaturamento = carregarPosts("idFaturamento","");
    $dhPagamento = carregarPosts("dhPagamento","");
    $dhFatura = carregarPosts("dhFatura","");

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest('')) { goto formulario; }
            
} else  {
    $faturamento = carregarGets('faturamento', ""); 
    $idFaturamento = carregarGets('idFaturamento', ""); 
    $dhPagamento = carregarGets("dhPagamento","");
    $dhFatura = carregarGets("dhFatura","");
}

// Pegando os campos do formulário
$fatura = carregarPosts("fatura");
$remessa = carregarPosts("remessa");
$operador = carregarPosts("operador");
$matricula = carregarPosts("matricula");
$statusInicial = carregarPosts("statusInicial");
$statusFinal = carregarPosts("statusFinal");
$faturamentoInicio = carregarPosts("faturamentoInicio");
$faturamentoFinal = carregarPosts("faturamentoFinal");
$ultimoInicio = carregarPosts("ultimoInicio");
$ultimoFinal = carregarPosts("ultimoFinal");
$faturaInicio = carregarPosts("faturaInicio");
$faturaFinal = carregarPosts("faturaFinal");
$pagamentoInicio = carregarPosts("pagamentoInicio");
$pagamentoFinal = carregarPosts("pagamentoFinal");
$situacao = carregarPosts("situacao");

// Adicionais 
$filtro = carregarCookie($siglaAeroporto.'_faMF_filtro');
$descricao = carregarCookie($siglaAeroporto.'_faMF_descricao');
$ordenacao = carregarCookie($siglaAeroporto.'_faMF_ordenacao','faturamento desc, status');

// Cancelar faturamentos confirmado e suas mensagens
//
if ($evento == "cancelarFaturamentoConfirmado" && $idFaturamento != '') {
    $retorno = cancelarFaturamentoConfirmado($faturamento, $idFaturamento);   
    if ($retorno['tipo'] == 'success') {
        $faturamento = null;
        $idFaturamento = null;
    }
    gravaXTrace('Cancelar Faturamento Confirmado '.$retorno['tipo'].' '.$retorno['mensagem']);
    montarMensagem($retorno['tipo'],array($retorno['mensagem']));
}

// Cancelar emissao da fatura
//
if ($evento == "cancelarEmissaoFaturamento" && $idFaturamento != '') {
    // Se a data dhFatura vier montada é para cancelar o faturamento 
    // Data e hora local do aeroporto
    $date = dateTimeUTC($utcAeroporto)->format('Y-m-d H:i');
    $retorno = atualizarEmissaoFatura($idFaturamento, $faturamento, ($dhFatura == '' ? $date : null)); 
    if ($retorno['tipo'] == 'success') {
        $faturamento = null;
        $idFaturamento = null;
    }
    gravaXTrace('Cancelamento da Emissão da Fatura '.$retorno['tipo'].' '.$retorno['mensagem']);
    montarMensagem($retorno['tipo'],array($retorno['mensagem']));
}

// Atualizar pagamento da fatura
//
if ($evento == "atualizarPagamento" && $idFaturamento != '') {
    // Se a data dhPagamento vier montada é para cancelar o pagamento 
    // Data e hora local do aeroporto
    $date = dateTimeUTC($utcAeroporto)->format('Y-m-d H:i');
    $retorno = atualizarPagamentoFatura($idFaturamento, $faturamento, ($dhPagamento == '' ? $date : null)); 
    if ($retorno['tipo'] == 'success') {
        $faturamento = null;
        $idFaturamento = null;
    }
    gravaXTrace('Pagamento da Fatura '.$retorno['tipo'].' '.$retorno['mensagem']);
    montarMensagem($retorno['tipo'],array($retorno['mensagem']));
}

// Baixar arquivo CSV da consulta de acordo com o Filtro
//
if ($evento == "baixarArquivoCSV" && $filtro != "") {
    $retorno = baixarArquivoCSV($filtro, $utcAeroporto);   
    if ($retorno['tipo'] == 'success') {
        $faturamento = null;
        $idFaturamento = null;
        if (file_exists($retorno['arquivo'])) {
            header("Location: ../suporte/suDownload.php?arquivo=".$retorno['arquivo']."&excluir=sim");
        }
    }
    gravaXTrace('Baixar Arquivo CSV '.$retorno['tipo'].' '.$retorno['mensagem'].' '.$retorno['arquivo']);
    montarMensagem($retorno['tipo'],array($retorno['mensagem']));
}

// Integracao ASAAS - Gera a respectiva tabela de acordo com o Filtro
//
if ($evento == "integracaoASAAS" && $filtro != "") {
    $retorno = integracaoASAAS($aeroporto, $siglaAeroporto, $idUsuario, $filtro, $utcAeroporto);   
    if ($retorno['tipo'] == 'success') {
        $faturamento = null;
        $idFaturamento = null;
    }
    gravaXTrace('Integração ASAAS '.$retorno['tipo'].' '.$retorno['mensagem'].' '.$retorno['remessa']);
    montarMensagem($retorno['tipo'],array($retorno['mensagem'],$retorno['remessa']));
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Manter Faturamento";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao">
        <?php barraFuncoesCadastro($titulo, array("","","X","","X","","X")); ?>   
        <form action="#" method="POST" class="form-group" autocomplete="off">
            <div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>

                <!-- Para sempre exibir a página de cadastro -->
                <!-- <input type="hidden" id="hdExibirCadastro" value="SIM"/>  -->
                <!--***************************************************************** -->
                <div class="d-flex justify-content-end pt-4">
                    <div class="px-2"><a href='?evento=baixarArquivoCSV' class="btn btn-outline-success" role="button">
                        <img src="../ativos/img/exportarCSV.png" style="padding-right: 10px; height: 24px;"/>Baixar Arquivo CSV</a></div>
                    <div><a href='?evento=integracaoASAAS' class="btn btn-outline-primary" role="button">
                        <img src="../ativos/img/asaas.png" style="padding-right: 10px; height: 24px;"/>Integração Faturamento ASAAS</a></div>
                </div>
                <div class="row pt-2">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">  
                        <div class="row mt-2">                           
                            <div class="col-md-2">
                                <label for="txFaturamento">Faturamento</label>
                                <input type="text" class="form-control cpoCookie cpoLimpar caixaAlta input-lg" id="txFaturamento" name="fatura"  placeholder="aaaa/nnnnnn"
                                    <?php echo (!isNullOrEmpty($fatura)) ? "value=\"{$fatura}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txRemessa">Remessa ASAAS</label>
                                <input type="text" class="form-control cpoCookie cpoLimpar caixaAlta input-lg" id="txRemessa" name="remessa"  placeholder="aaaa/nnnnnn"
                                    <?php echo (!isNullOrEmpty($remessa)) ? "value=\"{$remessa}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txMatricula">Matrícula</label>
                                <input type="text" class="form-control cpoCookie cpoLimpar caixaAlta input-lg" id="txMatricula" name="flMatricula"
                                    <?php echo (!isNullOrEmpty($matricula)) ? "value=\"{$matricula}\"" : "";?>/>
                            </div> 
                        </div>   
                        <div class="row mt-2"> 
                            <div class="col-md-4">
                                <label for="txOperador">Operador</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txOperador" name="operador"
                                    <?php echo (!isNullOrEmpty($operador)) ? "value=\"{$operador}\"" : "";?>/>
                            </div>                                               
                            <div class="col-md-2">
                                <label for="txStatusInicial">Intervalo de Status</label>
                                <input type="text" class="form-control cpoCookie cpoLimpar input-lg" id="txStatusInicial" name="statusInicial" placeholder="aaaa/mm/nnnnnn"
                                    <?php echo (!isNullOrEmpty($statusInicial)) ? "value=\"{$statusInicial}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txStatusFinal"></label>
                                <input type="text" class="form-control cpoCookie cpoLimpar input-lg" id="txStatusFinal" name="statusFinal" placeholder="aaaa/mm/nnnnnn"
                                    <?php echo (!isNullOrEmpty($statusFinal)) ? "value=\"{$statusFinal}\"" : "";?>/>
                            </div>
                        </div>   
                        <div class="row mt-2">                     
                            <div class="col-md-2">
                                <label for="dtFaturamentoInicio">Período Faturamento</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtFaturamentoInicio" size="10" name="faturamentoInicio" 
                                    <?php echo (!isNullOrEmpty($faturamentoInicio)) ? "value=\"{$faturamentoInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtFaturamentoFinal"> </label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtFaturamentoFinal" size="10" name="faturamentoFinal"
                                    <?php echo (!isNullOrEmpty($faturamentoFinal)) ? "value=\"{$faturamentoFinal}\"" : "";?>/>
                            </div>   
                            <div class="col-md-2">
                                <label for="dtUltimoInicio">Último Movimento</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtUltimoInicio" size="10" name="ultimoInicio" 
                                    <?php echo (!isNullOrEmpty($ultimoInicio)) ? "value=\"{$ultimoInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtUltimoFinal"> </label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtUltimoFinal" size="10" name="ultimoFinal"
                                    <?php echo (!isNullOrEmpty($ultimoFinal)) ? "value=\"{$ultimoFinal}\"" : "";?>/>
                            </div>  
                        </div> 
                        <div class="row mt-2">                     
                            <div class="col-md-2">
                                <label for="dtFaturaInicio">Emissão Fatura</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtFaturaInicio" size="10" name="faturaInicio" 
                                    <?php echo (!isNullOrEmpty($faturaInicio)) ? "value=\"{$faturaInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtFaturaFinal"> </label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtFaturaFinal" size="10" name="faturaFinal"
                                    <?php echo (!isNullOrEmpty($faturaFinal)) ? "value=\"{$faturaFinal}\"" : "";?>/>
                            </div> 
                            <div class="col-md-2">
                                <label for="dtPagamentoInicio">Pagamento</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtPagamentoInicio" size="10" name="pagamentoInicio" 
                                    <?php echo (!isNullOrEmpty($pagamentoInicio)) ? "value=\"{$pagamentoInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtPagamentoFinal"> </label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtPagamentoFinal" size="10" name="pagamentoFinal"
                                    <?php echo (!isNullOrEmpty($pagamentoFinal)) ? "value=\"{$pagamentoFinal}\"" : "";?>/>
                            </div> 
                            <div class="col-md-2">
                                <label for="slSituacao">Situação</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slSituacao" name="situacao">
                                    <option <?php echo ($situacao == '') ? 'selected' : '';?> value=''>Todas</option>
                                    <option <?php echo ($situacao == 'Pendente') ? 'selected' : '';?> value='Pendente'>Pendente</option>
                                    <option <?php echo ($situacao == 'Emitida') ? 'selected' : '';?> value='Emitida'>Emitida</option>
                                    <option <?php echo ($situacao == 'Paga') ? 'selected' : '';?> value='Paga'>Paga</option>
                                    <option <?php echo ($situacao == 'Fechada') ? 'selected' : '';?> value='Fechada'>Fechada</option>
                                </select> 
                            </div>
                            <div class="col-md-2">
                                <label for="slOrdenacao">Ordenação da lista</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slOrdenacao" name="ordenacao">
                                    <option <?php echo ($ordenacao == 'faturamento desc, status') ? 'selected' : '';?> value='faturamento desc, status'>Faturamento</option>
                                    <option <?php echo ($ordenacao == 'operadorOperacao, faturamento, status') ? 'selected' : '';?> value='operadorOperacao, faturamento, status'>Operador</option>
                                    <option <?php echo ($ordenacao == 'status, faturamento') ? 'selected' : '';?> value='status'>Status</option>
                                    <option <?php echo ($ordenacao == 'mt.matricula, faturamento') ? 'selected' : '';?> value='mt.matricula, faturamento'>Matrícula</option>
                                </select> 
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="container">
        <div class="row" >
            <div class="col-md-8" id="divTitulo"></div>
            <div class="col-md-4" id="divPagina"></div>
        </div>
    </div>
    <div class="container table-responsive" id="divTabela"></div>
    <div class="container" id="divImpressao" style="display:none"></div>
</div>
<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->   

<script src="../faturamento/faFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparPesquisa();
            $("#txFaturamento").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase());});

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND fa.idAeroporto = "+$("#hdAeroporto").val()+ 
                        " AND fa.situacao = 'CNF'" : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');                        

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "txFaturamento":
                            filtro += " AND CONCAT(fa.ano,'/',fa.numero) = '"+$("#txFaturamento").val()+"'";
                            descricaoFiltro += " <br>Faturamento : "+$("#txFaturamento").val();
                        break;
                        case "txRemessa":
                            filtro += " AND CONCAT(fa.ano,'/',fa.remessa) = '"+$("#txRemessa").val()+"'";
                            descricaoFiltro += " <br>Remessa : "+$("#txRemessa").val();
                        break;
                        case "txOperador":
                            filtro += " AND op.operador LIKE '%"+$("#txOperador").val()+"%'";
                            descricaoFiltro += " <br>Operador : "+$("#txOperador").val();
                        break;
                        case "txMatricula":
                            filtro += " AND mt.matricula LIKE '%"+$("#txMatricula").val()+"%'";
                            descricaoFiltro += " <br>Matrícula : "+$("#txMatricula").val();
                        break;
                        case "txStatusInicial":
                            filtro += " AND CONCAT(st.ano,'/',st.mes,'/',st.numero) >= '"+$("#txStatusInicial").val()+"'"+
                                        " AND CONCAT(st.ano,'/',st.mes,'/',st.numero) <= '"+$("#txStatusFinal").val()+"'";
                            descricaoFiltro += " <br>Intervalo de Status : "+$("#txStatusInicial").val()+" a "+$("#txStatusFinal").val();
                        break;
                        case "dtFaturamentoInicio":
                            filtro += " AND (DATE_FORMAT(fa.cadastro,'%Y-%m-%d')  >= '"+mudarDataAMD($("#dtFaturamentoInicio").val())+"'"+
                                        " AND DATE_FORMAT(fa.cadastro,'%Y-%m-%d') <= '"+mudarDataAMD($("#dtFaturamentoFinal").val())+"')";
                            descricaoFiltro += " <br>Período Faturamento : "+mudarDataDMA($("#dtFaturamentoInicio").val())+" a "+
                                                    mudarDataDMA($("#dtFaturamentoFinal").val());
                        break;
                        case "dtUltimoInicio":
                            filtro += " AND (DATE_FORMAT(mult.dhMovimento,'%Y-%m-%d')  >= '"+mudarDataAMD($("#dtUltimoInicio").val())+"'"+
                                        " AND DATE_FORMAT(mult.dhMovimento,'%Y-%m-%d') <= '"+mudarDataAMD($("#dtUltimoFinal").val())+"')";
                            descricaoFiltro += " <br>Último Movimento : "+mudarDataDMA($("#dtUltimoInicio").val())+" a "+
                                                    mudarDataDMA($("#dtUltimoFinal").val());
                        break;
                        case "dtFaturaInicio":
                            filtro += " AND (DATE_FORMAT(fa.fatura,'%Y-%m-%d')  >= '"+mudarDataAMD($("#dtFaturaInicio").val())+"'"+
                                        " AND DATE_FORMAT(fa.fatura,'%Y-%m-%d') <= '"+mudarDataAMD($("#dtFaturaFinal").val())+"')";
                            descricaoFiltro += " <br>Emissão Fatura : "+mudarDataDMA($("#dtFaturaInicio").val())+" a "+
                                                    mudarDataDMA($("#dtFaturaFinal").val());
                        break;
                        case "dtPagamentoInicio":
                            filtro += " AND (DATE_FORMAT(fa.pagamento,'%Y-%m-%d')  >= '"+mudarDataAMD($("#dtPagamentoInicio").val())+"'"+
                                        " AND DATE_FORMAT(fa.pagamento,'%Y-%m-%d') <= '"+mudarDataAMD($("#dtPagamentoFinal").val())+"')";
                            descricaoFiltro += " <br>Último Movimento : "+mudarDataDMA($("#dtPagamentoInicio").val())+" a "+
                                                    mudarDataDMA($("#dtPagamentoFinal").val());
                        break;
                        case "slSituacao":
                            switch ($("#slSituacao").val())
                            {
                                case "Pendente":
                                    filtro += " AND (fa.fatura is null AND fa.pagamento is null)";
                                    descricaoFiltro += " <br>Situação : "+$("#slSituacao :selected").text();
                                break;
                                case "Emitida":
                                    filtro += " AND (fa.fatura is not null AND fa.pagamento is null)";
                                    descricaoFiltro += " <br>Situação : "+$("#slSituacao :selected").text();
                                break;
                                case "Paga":
                                    filtro += " AND fa.situacao is null AND fa.pagamento is not null)";
                                    descricaoFiltro += " <br>Situação : "+$("#slSituacao :selected").text();
                                break;
                                case "Fechada":
                                    filtro += " AND (fa.fatura is not null AND fa.pagamento is not null)";
                                    descricaoFiltro += " <br>Situação : "+$("#slSituacao :selected").text();
                                break;
                            }
                        break;
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            }); 

            // Montagem da ordem
            var ordem = $("#slOrdenacao").val();

            await criarCookie($('#hdSiglaAeroporto').val()+'_faMF_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_faMF_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_faMF_descricao', descricaoFiltro);

            await faCarregarStatusFaturados('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txFaturamento").focus();
        };

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="'+$("#hdSiglaAeroporto").val()+'">';
            form += '<input type="hidden" name="titulo" value="' + $('#divTitulo').text() + '">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divImpressao').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="L">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
            $("#txFaturamento").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_faMF_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_faMF_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_faMF_descricao');

        // Adequações para o formulario  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaOrdem = 'faturamento desc, status';
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND fa.idAeroporto = "+$("#hdAeroporto").val()+ 
                        " AND fa.situacao = 'CNF'" : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : ''); 

            await criarCookie($('#hdSiglaAeroporto').val()+'_faMF_ordenacao', pesquisaOrdem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_faMF_filtro', pesquisaFiltro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_faMF_descricao', pesquisaDescricao);
        }  
        await faCarregarStatusFaturados('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));

        $("#txFaturamento").mask('9999/999999', {'translation': {9: {pattern: /[0-9]/}}});           
        $("#txStatusInicial").mask('9999/99/999999', {'translation': {9: {pattern: /[0-9]/}}});   
        $("#txStatusFinal").mask('9999/99/999999', {'translation': {9: {pattern: /[0-9]/}}});   
        $("#txFaturamento").focus();

        // // Executa a paginação
        // if ($('#hdPaginacao').val() == 'SIM' || $('#hdExibirCadastro').val() == 'SIM') {
        //     buscarCadastro();
        // }

        // // Interceptar a tecla enter e submeter o botão principal
        // //
        // document.addEventListener("keypress", function(e) {
        //     if(e.key === 'Enter') {
        //         event.preventDefault();
        //         buscarCadastro();
        //     }
        // });
    });
</script>