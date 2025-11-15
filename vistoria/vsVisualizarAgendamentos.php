<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']);  

// Token
$token = gerarToken($_SESSION['plantaSistema']);

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

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id');              // Agendamento
    $idPlano = carregarPosts('idPlano');

    $numero = carregarPosts('numero');
    $agendamentoInicio = carregarPosts('agendamentoInicio');
    $agendamentoFinal = carregarPosts('agendamentoFinal');
    $execucaoInicio = carregarPosts('execucaoInicio');
    $execucaoFinal = carregarPosts('execucaoFinal');
    $situacao = carregarPosts("situacao");

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
     
} else  {
    $id = carregarGets('id');               // Agendamento
    $idPlano = carregarGets('idPlano');
    $limparCampos = true;
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $idItem = null;
    $local = null;
    $numero = null;
    $agendamentoInicio = null;
    $agendamentoFinal = null;
    $execucaoInicio = null;
    $execucaoFinal = null;
    $situacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_vsVA_ordenacao','va.numero, va.inicio');
metaTagsBootstrap('');
$titulo = "Visualizar Agendamentos";
?>
<head>
    <title><?=$_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto)?>    
    <div class="container alert alert-padrao" >
        <form action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;"> 
            <?php barraFuncoesCadastro($titulo, array("","","X","","X","X","X")); ?>      
	    	<div class="form-group">
                <!-- Campos hidden -->
                <input type="hidden" name="usuario" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" name="aeroporto" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" name="siglaAeroporto" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" name="id" id="hdId" class="cpoLimpar" <?="value=\"{$id}\"";?>/>
                <input type="hidden" name="idPlano" id="hdIdPlano" <?="value=\"{$idPlano}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row"> 
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">                      
                            <div class="col-md-2">
                                <label for="txNumero">Número</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txNumero" name="numero" 
                                    <?php echo (!isNullOrEmpty($numero)) ? "value=\"{$numero}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2"> 
                            <div class="col-md-2">
                                <label for="dtAgendamentoInicio">Período Agendamento</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtAgendamentoInicio" size="10" name="agendamentoInicio" 
                                    <?php echo (!isNullOrEmpty($agendamentoInicio)) ? "value=\"{$agendamentoInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtAgendamentoFinal"> </label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtAgendamentoFinal" size="10" name="agendamentoFinal"
                                    <?php echo (!isNullOrEmpty($agendamentoFinal)) ? "value=\"{$agendamentoFinal}\"" : "";?>/>
                            </div> 
                        </div> 
                        <div class="row mt-2"> 
                            <div class="col-md-2">
                                <label for="dtExecucaoInicio">Período Execução</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtExecucaoInicio" size="10" name="execucaoInicio" 
                                    <?php echo (!isNullOrEmpty($execucaoInicio)) ? "value=\"{$execucaoInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtExecucaoFinal"> </label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtExecucaoFinal" size="10" name="execucaoFinal"
                                    <?php echo (!isNullOrEmpty($execucaoFinal)) ? "value=\"{$execucaoFinal}\"" : "";?>/>
                            </div> 
                            <div class="col-md-2">
                                <label for="slUsuario">Usuário</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slUsuario">
                                </select> 
                            </div>
                            <div class="col-md-2">
                                <label for="slSituacao">Situação</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slSituacao" name="situacao">
                                    <option <?php echo ($situacao == '') ? 'selected' : '';?> value=''>Todas</option>
                                    <option <?php echo ($situacao == 'Pendente') ? 'selected' : '';?> value='Pendente'>Pendente</option>
                                    <option <?php echo ($situacao == 'Executada') ? 'selected' : '';?> value='Executada'>Executada</option>
                                </select> 
                            </div>
                            <div class="col-md-2">
                                <label for="slOrdenacao">Ordenação da lista</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slOrdenacao" name="ordenacao">
                                    <option <?php echo ($ordenacao == 'va.numero, va.inicio') ? 'selected' : '';?> value='va.numero, va.inicio, va.periodo'>Agendamento</option>
                                    <option <?php echo ($ordenacao == 'va.inicio, va.numero, va.periodo') ? 'selected' : '';?> value='va.inicio, va.numero, va.periodo'>Data Agendamento</option>
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

<script src="../vistoria/vsFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparPesquisa();
            $("#txNumero").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND vp.idAeroporto = "+$("#hdAeroporto").val()+" AND vp.id = "+$("#hdIdPlano").val(): "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "txNumero":
                            filtro += " AND va.numero = '"+$("#txNumero").val()+"'";
                            descricaoFiltro += " <br>Número : "+$("#txNumero").val();
                        break;
                        case "dtAgendamentoInicio":
                            filtro += " AND (DATE_FORMAT(va.inicio,'%Y-%m-%d')  >= '"+mudarDataAMD($("#dtAgendamentoInicio").val())+"'"+
                                        " AND DATE_FORMAT(va.inicio,'%Y-%m-%d') <= '"+mudarDataAMD($("#dtAgendamentoFinal").val())+"')";
                            descricaoFiltro += " <br>Período de Agendamento : "+mudarDataDMA($("#dtAgendamentoInicio").val())+" a "+
                                                    mudarDataDMA($("#dtAgendamentoFinal").val());
                        break;
                        case "dtExecucaoInicio":
                            filtro += " AND (DATE_FORMAT(va.execucao,'%Y-%m-%d')  >= '"+mudarDataAMD($("#dtExecucaoInicio").val())+"'"+
                                        " AND DATE_FORMAT(va.execucaoo,'%Y-%m-%d') <= '"+mudarDataAMD($("#dtExecucaoFinal").val())+"')";
                            descricaoFiltro += " <br>Período de Execução : "+mudarDataDMA($("#dtExecucaoInicio").val())+" a "+
                                                    mudarDataDMA($("#dtExecucaoFinal").val());
                        break;
                        case "slUsuario":
                            filtro += " AND va.idUsuario = '"+$("#slUsuario :selected").val()+"'";
                            descricaoFiltro += " <br>Situação : "+$("#slUsuario :selected").text();
                        break;
                        case "slSituacao":
                            switch ($("#slSituacao").val())
                            {
                                case "Pendente":
                                    filtro += " AND va.execucao is null";
                                    descricaoFiltro += " <br>Situação : "+$("#slSituacao :selected").text();
                                break;
                                case "Executada":
                                    filtro += " AND va.execucao is not null";
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
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsVA_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsVA_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsVA_descricao', descricaoFiltro);

            await vsCarregarVistoriaPlanosAgendamentos('Consulta', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txNumero").focus();
        };

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="'+$("#hdSiglaAeroporto").val()+'">';
            form += '<input type="hidden" name="titulo" value="' + $('#divTitulo').text() + '">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divImpressao').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="P">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
            $("#slTipo").focus();
        });
        
        $("#retornarFormulario").click(function(){ window.location.href = urlSemQueryString(document.referrer); });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_vsVA_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_vsVA_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_vsVA_descricao');

        // Adequações para o cadastro
        pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND vp.idAeroporto = "+$("#hdAeroporto").val()+" AND vp.id = "+$("#hdIdPlano").val(): "");
        pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        await suCarregarSelectTodos('VistoriaUsuarios','#slUsuario', '', pesquisaFiltro, 'Consultar');
        await vsCarregarVistoriaPlanosAgendamentos('Consulta', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txNumero").focus();
    });
</script>