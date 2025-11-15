<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../tarefas/trFuncoes.php");
require_once("../tarefas/trGerarVoosPlanejados.php");
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
$filtro = carregarCookie($siglaAeroporto.'_opGVP_filtro');
$descricao = carregarCookie($siglaAeroporto.'_opGVP_descricao');
$ordenacao = carregarCookie($siglaAeroporto.'_opGVP_ordenacao');

// Verificar se foi enviando dados via POST ou inicializa as variáveis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inicioOperacao = carregarPosts('inicioOperacao');
    $fimOperacao = carregarPosts('fimOperacao');
} else {
    $inicioOperacao = carregarCookie('opGVP_inicioOperacao',(new DateTime())->format('Y-m-d'));
    $fimOperacao = carregarCookie('opGVP_fimOperacao',(new DateTime())->format('Y-m-d'));
}

// Gerar planejamento 
if ($evento == "gerarPlanejamento" && (!empty($inicioOperacao) && !empty($fimOperacao))) {
    // Data e hora local do aeroporto
    $identificacao = dateTimeUTC($utcAeroporto)->format('Ymd_His');
    gerarVoosPlanejados($identificacao, $aeroporto, $siglaAeroporto, $inicioOperacao, $fimOperacao, $usuario, 'MNL');

    // Verifica se tem arquivo log gerado
    $log = lerXLogProcesso('../logs/trGerarVoosPlanejados_'.$identificacao.'.txt');
    montarMensagem($log[0], $log[1]);
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Gerar Voos Planejados";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>    
    <div class="container alert alert-padrao" >
        <form action="?evento=gerarPlanejamento" method="POST" class="form-group" autocomplete="off">
            <?php barraFuncoesCadastro($titulo, array("","","X","X","X","")); ?>          
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
                                <label for="dtInicioOperacao">Período de Operação</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtInicioOperacao" size="10" name="inicioOperacao" 
                                    <?php echo (!isNullOrEmpty($inicioOperacao)) ? "value=\"{$inicioOperacao}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="dtFimOperacao"> </label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtFimOperacao" size="10" name="fimOperacao"
                                    <?php echo (!isNullOrEmpty($fimOperacao)) ? "value=\"{$fimOperacao}\"" : "";?>/>
                            </div>   
                        </div>  
                    </div>
                    <div class ="col-lg-2">
                        <div class="row pt-4 px-2">
                            <button class="btn btn-padrao" type="submit" title="Gerar Planejamento" id="gerarMovimento" 
                                onclick="return confirm('Confirma a exclusão do planejamento existente para este período?');">Gerar Planejamento</button>
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
<!-- Modal PESQUISA -->
<!-- *************************************************** -->
<div class="modal fade" id="pesquisarCadastro" tabindex="-1" aria-labelledby="sobreLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sobreLabel">Pesquisar <?php echo $titulo ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="pslOperador">Operador</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslOperador">
                        </select>
                    </div>    
                    <div class="col-md-6">
                        <label for="ptxNumero">Número</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNumero">
                    </div>                         
                </div>
                <div class="row mt-2" >
                    <div class="col-md-6">
                        <label for="pdtInicioOperacao">Período de Operação</label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtInicioOperacao" size="10"/>
                    </div>
                    <div class="col-md-6">
                        <label for="pdtFimOperacao"></label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtFimOperacao" size="10"/>
                    </div>   
                </div>   
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="pslSituacaoSiros">Situação</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSituacaoSiros">
                        </select>
                    </div>                             
                    <div class="col-md-6">
                        <label for="pslNaturezaOperacao">Natureza</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslNaturezaOperacao">
                        </select>
                    </div>
                </div>   
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="pslObjetoTransporte">Objeto de Transporte</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslObjetoTransporte">
                        </select>
                    </div>  
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label for="pslServico">Serviço</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslServico">
                        </select>
                    </div>
                </div>
                <br>
                <div class="row mt-2">                    
                    <div class="col-md-8">
                        <label for="pslOrdenacao">Ordenação da lista</label>
                        <select class="form-select selCookie input-lg" id="pslOrdenacao">
                            <option <?php echo ($ordenacao == 'vr.operador,vr.numeroVoo,vr.inicioOperacao,vr.numeroEtapa') ? 'selected' : '';?> 
                                                        value='vr.operador,vr.numeroVoo,vr.inicioOperacao,vr.numeroEtapa'>Voo</option>
                            <option <?php echo ($ordenacao == 'horarioOperacao,vr.inicioOperacao,vr.operador,vr.numeroVoo,vr.numeroEtapa') ? 'selected' : '';?> 
                                                        value='horarioOperacao,vr.inicioOperacao,vr.operador,vr.numeroVoo,vr.numeroEtapa'>Planejamento</option>                                                                
                        </select> 
                    </div>
                </div>
            </div>
            <?php barraFuncoesPesquisa($titulo); ?>
        </div>
    </div>
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
            $("#dtInicioOperacao").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            var busca = $("#hdSiglaAeroporto").val();

            // Monta filtro fixo da indentificação do aeroporto
            filtro = " AND (vr.icaoOrigem = '"+$("#hdSiglaAeroporto").val()+"' OR vr.icaoDestino = '"+$("#hdSiglaAeroporto").val()+"')";
            descricaoFiltro += ' <br>Aeroporto: '+$("#hdNomeAeroporto").val();

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pslOperador":
                            filtro += " AND vr.operador = '"+$("#pslOperador").val()+"'";
                            descricaoFiltro += ' <br>Operador : '+$("#pslOperador :selected").text();
                        break; 
                        case "ptxNumero":
                            filtro += " AND vr.numeroVoo = '"+$("#ptxNumero").val()+"'";
                            descricaoFiltro += " <br>Número : "+$("#ptxNumero").val();
                        break;
                        case "pdtInicioOperacao":
                            filtro += " AND NOT (DATE_FORMAT(vr.inicioOperacao,'%Y-%m-%d')  > '"+mudarDataAMD($("#pdtFimOperacao").val())+"'"+
                                        " OR DATE_FORMAT(vr.fimOperacao,'%Y-%m-%d') < '"+mudarDataAMD($("#pdtInicioOperacao").val())+"')"
                            descricaoFiltro += ' <br>Período de Operação : '+mudarDataDMA($("#pdtInicioOperacao").val())+' a '+
                                                                            mudarDataDMA($("#pdtFimOperacao").val());
                        break;                            
                        case "pslSituacaoSiros":
                            filtro += " AND vr.situacaoSiros = '"+$("#pslSituacaoSiros").val()+"'";
                            descricaoFiltro += ' <br>Situação : '+$("#pslSituacaoSiros :selected").text();
                        break;
                        case "pslNaturezaOperacao":
                            filtro += " AND vr.naturezaOperacao = '"+$("#pslNaturezaOperacao").val()+"'";
                            descricaoFiltro += ' <br>Natureza da Operação : '+$("#pslNaturezaOperacao :selected").text();
                        break;  
                        case "pslServico":
                            filtro += " AND vr.servico = '"+$("#pslServico").val()+"'";
                            descricaoFiltro += ' <br>Serviço : '+$("#pslServico :selected").text();
                        break;                        
                        case "pslObjetoTransporte":                            
                            filtro += " AND vr.objetoTransporte = '"+$("#pslObjetoTransporte").val()+"'";
                            descricaoFiltro += ' <br>Objeto de Transporte : '+$("#pslObjetoTransporte :selected").text();
                        break;
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });

            // Montagem da ordem
            var ordem = 'horarioOperacao,vr.inicioOperacao,vr.operador,vr.numeroVoo,vr.numeroEtapa';

            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVP_inicioOperacao', $("#dtInicioOperacao").val());
            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVP_fimOperacao', $("#dtFimOperacao").val());
            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVP_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVP_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVP_descricao', descricaoFiltro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opGVP_busca', busca);
            
            await opCarregarVoosANAC('Consultar', filtro, ordem, descricaoFiltro, busca, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#dtInicioOperacao").focus();
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
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_opGVP_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_opGVP_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_opGVP_descricao');
        var pesquisaBusca = await valorCookie($('#hdSiglaAeroporto').val()+'_opGVP_busca');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#dtInicioOperacao").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodos('OperadorANAC','#pslOperador','','', 'Consulta', 'codigo');
            await suCarregarSelectTodas('SituacaoSiros','#pslSituacaoSiros','','', 'Consulta');
            await suCarregarSelectTodas('NaturezaOperacao','#pslNaturezaOperacao','','', 'Consulta');
            await suCarregarSelectTodos('ServicoAnac','#pslServico','','', 'Consulta');
            await suCarregarSelectTodos('ObjetoTransporte','#pslObjetoTransporte','','', 'Consulta');  
            await suCarregarSelectTodos('Servico','#pslServico','','', 'Consulta');  
        });      

        // Adequações para o cadastro          
        await opCarregarVoosANAC('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, pesquisaBusca, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#dtInicioOperacao").focus();
    });
</script>