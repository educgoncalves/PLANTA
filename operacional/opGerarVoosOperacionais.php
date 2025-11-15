<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../tarefas/trFuncoes.php");
require_once("../tarefas/trGerarVoosOperacionais.php");

verificarExecucao();

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']);  

// Recuperando as informações do Aeroporto
$usuario = $_SESSION['plantaUsuario'];
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Adicionais 
$filtro = carregarCookie($siglaAeroporto.'_opGVO_filtro');
$descricao = carregarCookie($siglaAeroporto.'_opGVP_descricao');
$ordenacao = carregarCookie($siglaAeroporto.'_opGVP_ordenacao');

// Verificar se foi enviando dados via POST ou inicializa as variáveis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dtMovimento = carregarPosts('dtMovimento');
} else {
    // Recupera a última data de movimento escolhida
    // Data e hora local do aeroporto
    $date = dateTimeUTC($utcAeroporto)->format('Y-m-d');  
    $dtMovimento = carregarCookie('opGVO_movimento',$date);
}

// Gerar voos operacionais
if ($evento == "gerarMovimentoOperacional" && !empty($dtMovimento)) {
    // Data e hora local do aeroporto
    $identificacao = dateTimeUTC($utcAeroporto)->format('Ymd_His');
    gerarVoosOperacionais($identificacao, $aeroporto, $siglaAeroporto, $dtMovimento, $usuario, 'MNL');

    // Verifica se tem arquivo log gerado
    $log = lerXLogProcesso('../logs/trGerarVoosOperacionais_'.$identificacao.'.txt');
    montarMensagem($log[0], $log[1]);
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Gerar Voos Operacionais";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>    
    <div class="container alert alert-padrao" >
        <form  action="?evento=gerarMovimentoOperacional" method="POST" class="form-group" autocomplete="off">
            <?php barraFuncoesCadastro($titulo, array("","","X","","X","","X")); ?>          
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                <input type="hidden" id="hdOrdenacao" <?="value=\"{$ordenacao}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div class ="col-lg-10">
                        <div class="row mt-2" >
                            <div class="col-md-3">
                                <label for="dtMovimento">Movimento Operacional</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtMovimento" size="10" name="dtMovimento" 
                                    <?php echo (!isNullOrEmpty($dtMovimento)) ? "value=\"{$dtMovimento}\"" : "";?>/>
                            </div>
                        </div>  
                    </div>
                    <div class ="col-lg-2">
                        <div class="row pt-4 px-2">
                            <button class="btn btn-padrao" type="submit" title="Gerar Movimento" id="gerarMovimento" 
                                onclick="return confirm('Confirma a exclusão do movimento existente para esta data?');">Gerar Movimento</button>
                        </div>
                    </div>
                </div>
                <?php destacarTarefaVoosANAC(); ?>  
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

<script src="../operacional/opFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#dtMovimento").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            var busca = $("#hdSiglaAeroporto").val();

            // Monta filtro fixo da indentificação do aeroporto
            filtro = " AND (vp.icaoOrigem = '"+$("#hdSiglaAeroporto").val()+"' OR vp.icaoDestino = '"+$("#hdSiglaAeroporto").val()+"')";
            descricaoFiltro = ' <br>Aeroporto: '+$("#hdNomeAeroporto").val();

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "dtMovimento":
                            filtro += " AND NOT (DATE_FORMAT(vp.inicioOperacao,'%Y-%m-%d')  > '"+mudarDataAMD($("#dtMovimento").val())+"'"+
                                        " OR DATE_FORMAT(vp.fimOperacao,'%Y-%m-%d') < '"+mudarDataAMD($("#dtMovimento").val())+"')"
                            descricaoFiltro += ' <br>Movimento Operacional : '+mudarDataDMA($("#dtMovimento").val());
                        break;                            
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });

            // Montagem da ordem
            var ordem = 'horarioOperacao,vp.inicioOperacao,vp.operador,vp.numeroVoo,vp.numeroEtapa';

            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVO_movimento', $("#dtMovimento").val());
            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVO_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVO_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVO_descricao', descricaoFiltro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVO_busca', busca);
            
            await opCarregarVoosPlanejados('Consultar', filtro, ordem, descricaoFiltro, busca, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#dtMovimento").focus();
        }

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
            $("#slOperador").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_opGVO_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_opGVO_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_opGVO_descricao');
        var pesquisaBusca = await valorCookie($('#hdSiglaAeroporto').val()+'_opGVO_busca');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#dtMovimento").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });

        // Adequações para o cadastro          
        await opCarregarVoosPlanejados('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, pesquisaBusca, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#dtMovimento").focus();
    });
</script>