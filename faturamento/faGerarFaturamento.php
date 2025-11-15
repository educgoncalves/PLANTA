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

// Pegando os campos do formulário
$faturamento = carregarPosts("faturamento");
$idFaturamento = carregarPosts("idFaturamento");
$operador = carregarPosts("operador");
$matricula = carregarPosts("matricula");
$statusInicial = carregarPosts("statusInicial");
$statusFinal = carregarPosts("statusFinal");
$periodoInicio = carregarPosts("periodoInicio");
$periodoFinal = carregarPosts("periodoFinal");
$situacao = carregarPosts("situacao");

// Adicionais 
$filtro = carregarCookie($siglaAeroporto.'_faGF_filtro');
$descricao = carregarCookie($siglaAeroporto.'_faGF_descricao');
$ordenacao = carregarCookie($siglaAeroporto.'_faGF_ordenacao','operadorOperacao, status, faturamento');

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = "show"; //($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Calcular as mensagens e atualizando a tabela exibida
//
if ($evento == "calcularStatus") {
    $retorno = faturamentoEmProcessamento($aeroporto);    
    if ($retorno['tipo'] == 'success') {
        gravaXTrace('Calcular Faturamento - Filtro '.$filtro);
        $retorno = calcularStatus($filtro);
    } 
    gravaXTrace('Calcular Faturamento '.$retorno['tipo'].' '.$retorno['mensagem']);
    montarMensagem($retorno['tipo'],array($retorno['mensagem']));
}

// Confirmar faturamento e suas mensagens
//
if ($evento == "confirmarCalculo") {
    $retorno = confirmarCalculo($aeroporto, $faturamento, $idFaturamento);    
    if ($retorno['tipo'] == 'success') {
        $faturamento = null;
        $idFaturamento = null;
    } 
    gravaXTrace('Confirmar Faturamento '.$retorno['tipo'].' '.$retorno['mensagem']);
    montarMensagem($retorno['tipo'],array($retorno['mensagem']));
}

// Cancelar faturamentos e suas mensagens
//
if ($evento == "cancelarCalculos") {
    $retorno = cancelarCalculosPendentes($aeroporto, $usuario);   
    if ($retorno['tipo'] == 'success') {
        $faturamento = null;
        $idFaturamento = null;
    }
    gravaXTrace('Cancelar Faturamento '.$retorno['tipo'].' '.$retorno['mensagem']);
    montarMensagem($retorno['tipo'],array($retorno['mensagem']));
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Gerar Faturamento";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>       
    <div class="container alert alert-padrao">
        <?php barraFuncoesCadastro($titulo, array("","","X","","X","","X")); ?>   
        <form method="POST"  class="form-group" autocomplete="off">
            <div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" class="cpoCookie" id="hdFaturamento" name="faturamento" <?="value=\"{$faturamento}\"";?>/> 
                <input type="hidden" class="cpoCookie" id="hdIdFaturamento" name="idFaturamento" <?="value=\"{$idFaturamento}\"";?>/> 
                
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="d-flex justify-content-end pt-4">
                    <div class="px-2"><a href='?evento=calcularStatus' class="btn btn-outline-primary" role="button">Calcular Status</a></div>
                    <div class="px-2"><a href='?evento=confirmarCalculo' class="btn btn-outline-success" role="button">Confirmar Cálculos (Faturamento)</a></div>
                    <div><a href='?evento=cancelarCalculos' class="btn btn-outline-danger" role="button">Cancelar Cálculos</a></div>
                </div>
                <div class="row pt-2">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">  
                        <div class="row mt-2"> 
                            <div class="col-md-2">
                                <label for="txMatricula">Matrícula</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txMatricula" name="matricula"
                                    <?php echo (!isNullOrEmpty($matricula)) ? "value=\"{$matricula}\"" : "";?>/>
                            </div>
                            <div class="col-md-6">
                                <label for="txOperador">Operador</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txOperador" name="operador"
                                    <?php echo (!isNullOrEmpty($operador)) ? "value=\"{$operador}\"" : "";?>/>
                            </div>                    
                        </div>   
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="txStatusInicial">Intervalo de Status</label>
                                <input type="text" class="form-control cpoCookie input-lg" id="txStatusInicial" name="statusInicial" placeholder="aaaa/mm/nnnnnn"
                                    <?php echo (!isNullOrEmpty($statusInicial)) ? "value=\"{$statusInicial}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txStatusFinal"></label>
                                <input type="text" class="form-control cpoCookie input-lg" id="txStatusFinal" name="statusFinal" placeholder="aaaa/mm/nnnnnn"
                                    <?php echo (!isNullOrEmpty($statusFinal)) ? "value=\"{$statusFinal}\"" : "";?>/>
                            </div>
                        </div>   
                        <div class="row mt-2">                     
                            <div class="col-md-2">
                                <label for="dtPeriodoInicio">Último Movimento</label>
                                <input type="date" class="form-control cpoCookie input-lg" id="dtPeriodoInicio" size="10" name="periodoInicio" 
                                    <?php echo (!isNullOrEmpty($periodoInicio)) ? "value=\"{$periodoInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtPeriodoFinal"> </label>
                                <input type="date" class="form-control cpoCookie input-lg" id="dtPeriodoFinal" size="10" name="periodoFinal"
                                    <?php echo (!isNullOrEmpty($periodoFinal)) ? "value=\"{$periodoFinal}\"" : "";?>/>
                            </div>   
                            <div class="col-md-2">
                                <label for="slSituacao">Situação</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slSituacao" name="situacao">
                                    <option <?php echo ($situacao == '') ? 'selected' : '';?> value=''>Todas</option>
                                    <option <?php echo ($situacao == 'Pendente') ? 'selected' : '';?> value='Pendente'>Pendente</option>
                                    <option <?php echo ($situacao == 'A confirmar') ? 'selected' : '';?> value='A confirmar'>A confirmar</option>
                                    <option <?php echo ($situacao == 'Faturado') ? 'selected' : '';?> value='Faturado'>Faturado</option>
                                </select> 
                            </div>
                            <div class="col-md-2">
                                <label for="slOrdenacao">Ordenação da lista</label>
                                <select class="form-select selCookie input-lg" id="slOrdenacao" name="ordenacao">
                                    <option <?php echo ($ordenacao == 'operadorOperacao, status, faturamento') ? 'selected' : '';?> value='operadorOperacao, status, faturamento'>Operador</option>
                                    <option <?php echo ($ordenacao == 'status, faturamento') ? 'selected' : '';?> value='status, faturamento'>Status</option>
                                    <option <?php echo ($ordenacao == 'mt.matricula, status, faturamento') ? 'selected' : '';?> value='mt.matricula, status, faturamento'>Matrícula</option>
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
            $("#txMatricula").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase());});

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            // filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND st.idAeroporto = "+$("#hdAeroporto").val()+ 
            //                 " AND st.situacao = 'ATV' AND mult.movimento = 'DEC'" : "");
            // filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND st.idAeroporto = "+$("#hdAeroporto").val()+ 
            //                 " AND st.faturado = 'NAO' AND st.situacao = 'ATV' AND mult.movimento = 'DEC'" : "");
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND st.idAeroporto = "+$("#hdAeroporto").val()+ 
                        " AND st.situacao = 'ATV' AND (mult.movimento = 'DEC'"+
                        "  OR (mult.movimento NOT IN ('DEC','PRV') AND TIMESTAMPDIFF(DAY, mult.dhMovimento, UTC_TIMESTAMP()) % 30 <> 0))" : "");                          
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "txMatricula":
                            filtro += " AND mt.matricula LIKE '%"+$("#txMatricula").val()+"%'";
                            descricaoFiltro += " <br>Matrícula : "+$("#txMatricula").val();
                        break; 
                        case "txOperador":
                            filtro += " AND op.operador LIKE '%"+$("#txOperador").val()+"%'";
                            descricaoFiltro += " <br>Operador : "+$("#txOperador").val();
                        break;
                        case "txStatusInicial":
                            filtro += " AND CONCAT(st.ano,'/',st.mes,'/',st.numero) >= '"+$("#txStatusInicial").val()+"'"+
                                        " AND CONCAT(st.ano,'/',st.mes,'/',st.numero) <= '"+$("#txStatusFinal").val()+"'";
                            descricaoFiltro += " <br>Intervalo de Status : "+$("#txStatusInicial").val()+' a '+$("#txStatusFinal").val();
                        break;
                        case "dtPeriodoInicio":
                            filtro += " AND (DATE_FORMAT(mult.dhMovimento,'%Y-%m-%d')  >= '"+mudarDataAMD($("#dtPeriodoInicio").val())+"'"+
                                        " AND DATE_FORMAT(mult.dhMovimento,'%Y-%m-%d') <= '"+mudarDataAMD($("#dtPeriodoFinal").val())+"')";
                            descricaoFiltro += " <br>Último Movimento : "+mudarDataDMA($("#dtPeriodoInicio").val())+' a '+
                                                                            mudarDataDMA($("#dtPeriodoFinal").val());
                        break;
                        case "slSituacao":
                            switch ($("#slSituacao").val())
                            {
                                case "Pendente":
                                    filtro += " AND fa.situacao = 'PEN'";
                                    descricaoFiltro += " <br>Situação : "+$("#slSituacao :selected").text();
                                break;
                                case "A confirmar":
                                    filtro += " AND ca.situacao = 'NCN'";
                                    descricaoFiltro += " <br>Situação : "+$("#slSituacao :selected").text();
                                break;
                                case "Faturado":
                                    filtro += " AND fa.situacao = 'CNF'";
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
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_faGF_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_faGF_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_faGF_descricao', descricaoFiltro);

            await faCarregarStatusNaoFaturados('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txMatricula").focus();
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
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_faGF_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_faGF_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_faGF_descricao');

        // Adequações para o formulario  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaOrdem = 'operadorOperacao, status, faturamento';
            // pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND st.idAeroporto = "+$("#hdAeroporto").val()+ 
            //                     " AND st.situacao = 'ATV' AND mult.movimento = 'DEC'" : "");
            // pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND st.idAeroporto = "+$("#hdAeroporto").val()+ 
            //                     " AND st.faturado = 'NAO' AND st.situacao = 'ATV' AND mult.movimento = 'DEC'" : "");
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND st.idAeroporto = "+$("#hdAeroporto").val()+ 
                                " AND st.situacao = 'ATV' AND (mult.movimento = 'DEC'"+
                                "  OR (mult.movimento NOT IN ('DEC','PRV') "+
                                "      AND TIMESTAMPDIFF(DAY, mult.dhMovimento, UTC_TIMESTAMP()) % 30 <> 0))" : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');

            await criarCookie($('#hdSiglaAeroporto').val()+'_faGF_ordenacao', pesquisaOrdem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_faGF_filtro', pesquisaFiltro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_faGF_descricao', pesquisaDescricao);
        }  
        await faCarregarStatusNaoFaturados('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));

        $("#txStatusInicial").mask('9999/99/999999', {'translation': {9: {pattern: /[0-9]/}}});   
        $("#txStatusFinal").mask('9999/99/999999', {'translation': {9: {pattern: /[0-9]/}}});   
        $("#txMatricula").focus();
    });
</script>