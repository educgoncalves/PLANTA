<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../modais/mdModais.php");
verificarExecucao();

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']);    

// Token
$token = gerarToken($_SESSION['plantaSistema']);

// Recuperando as informações do Aeroporto
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
    $id = carregarPosts('id'); 
    $usuario = carregarPosts('usuario');
    $reserva = carregarPosts('reserva');
    $solicitacao = carregarPosts('solicitacao');
    $matricula = carregarPosts('matricula');
    $origem = carregarPosts('origem');
    $pob = carregarPosts('pob');
    $destino = carregarPosts('destino');
    $situacao = carregarPosts('situacao');        
    $observacao = carregarPosts('observacao');
    $enviar = carregarPosts('enviar');
    $envio = carregarPosts('envio');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
        
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}
 
// Salvando as informações
if ($evento == "salvar") {
    // Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['situacao','enviar']);
    if (!$erros) {
        try {
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE gear_reservas SET observacao = '".$observacao."', enviar= '".$enviar.
                            "' , situacao = '".$situacao."' WHERE id = ".$id;
            } else {
                throw new PDOException("Não é permitida a inclusão de reserva por este formulário!");
            }
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_reservas", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
                                $_SESSION['plantaUsuario'], ($id != "" ? $id  : $conexao->lastInsertId()), $comando);                
                    montarMensagem("success",array("Registro ".($id != "" ? "alterado" : "incluído")." com sucesso!"));
                    $id = null;
                    $limparCampos = true;
                } else {
                    throw new PDOException("Não foi possível efetivar esta ".($id != "" ? "alteração" : "inclusão")."!");
                }
            } else {
                throw new PDOException("Não foi possível ".($id != "" ? "alterar" : "incluir")." este registro!");
            } 
        } catch (PDOException $e) {
            montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
        }
    } else {
        montarMensagem("danger", $erros);
    } 
}

// Recuperando as informações
if ($evento == "recuperar" && $id != "") {
    // Preparando chamada da API apiConsultas
    $dados = ['tabela'=>'Reservas','filtro'=>" AND rs.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $usuario = $dados['usuarioCompleto'];
            $reserva = $dados['reserva'];
            $solicitacao = $dados['dataHoraCadastro'];
            $matricula = $dados['matriculaCompleta'];
            $origem = $dados['origem'].' '.$dados['dataHoraChegada'];
            $pob = $dados['pob'];
            $destino = $dados['destino'].' '.$dados['dataHoraPartida'];
            $situacao = $dados['situacao'];             
            $observacao = $dados['observacao'];
            $enviar = $dados['enviar'];
            $envio = $dados['dataHoraEnvio'];
        }
        $limparCampos = false;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    } 
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "DELETE FROM gear_reservas WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_reservas", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $id, $comando);   
            montarMensagem("success",array("Registro excluído com sucesso!"));
            $id = null;
            $limparCampos = true;
    } else {
        throw new PDOException("Não foi possível excluir este registro!");
    } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $usuario = null;
    $reserva = null;
    $solicitacao = null;
    $matricula = null;
    $origem = null;
    $pob = null;
    $destino = null;
    $situacao = null;        
    $observacao = null;
    $enviar = null;
    $envio = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_opAR_ordenacao','us.cadastro desc,reserva');     
metaTagsBootstrap('');
$titulo = "Avaliar Reservas";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>    
    <div class="container alert alert-padrao" >
        <form action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;">
            <?php barraFuncoesCadastro($titulo); ?>         
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\""?>/>
                <input type="hidden" class="cpoLimpar" id="hdEnviar" <?="value=\"{$enviar}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>

                <input type="hidden" id="hdEvento" name="evento" <?="value=\"{$evento}\"";?>/>
                <input type="hidden" id="hdTitulo" <?="value=\"{$titulo}\"";?>/>
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">                
                        <div class="row mt-2">  
                            <div class="col-md-2">
                                <label for="txReserva">Reserva</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txReserva" name="reserva" readonly
                                    <?php echo (!isNullOrEmpty($reserva)) ? "value=\"{$reserva}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txSolicitacao">Solicitação UTC</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txSolicitacao" name="solicitacao" readonly
                                    <?php echo (!isNullOrEmpty($solicitacao)) ? "value=\"{$solicitacao}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txMatricula">Matrícula</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txMatricula" name="matricula" readonly
                                    <?php echo (!isNullOrEmpty($matricula)) ? "value=\"{$matricula}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2"> 
                            <div class="col-md-3">
                                <label for="txOrigem">Origem UTC</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txOrigem" name="origem" readonly
                                    <?php echo (!isNullOrEmpty($origem)) ? "value=\"{$origem}\"" : "";?>/>
                            </div> 
                            <div class="col-md-1">
                                <label for="txPOB">POB</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txPOB" name="pob" readonly
                                    <?php echo (!isNullOrEmpty($pob)) ? "value=\"{$pob}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="txDestino">Destino UTC</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txDestino" name="destino" readonly
                                    <?php echo (!isNullOrEmpty($destino)) ? "value=\"{$destino}\"" : "";?>/>
                            </div>     
                        </div>
                        <div class="row mt-2">                                       
                            <div class="col-md-4">
                                <label for="txSolicitante">Solicitante</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txSolicitante" name="solicitante" readonly
                                    <?php echo (!isNullOrEmpty($usuario)) ? "value=\"{$usuario}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="txEnvio">Último comunicado UTC</label>
                                <input type="text" class="form-control input-lg" id="txEnvio" name="envio" readonly
                                    <?php echo (!isNullOrEmpty($envio)) ? "value=\"{$envio}\"" : "";?>/>
                            </div>  

                        </div>
                        <div class="row mt-2">  
                            <div class="col-md-2">
                                <label for="slSituacao">Situação</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slSituacao" name="situacao">
                                </select> 
                            </div>                         
                            <div class="col-md-8">
                                <label for="txObservacao">Observacao</label>
                                <textarea class="form-control cpoObrigatorio input-lg" id="txObservacao" name="observacao" maxlength="250"
                                    rows="3" cols="50"><?php echo (!isNullOrEmpty($observacao)) ? $observacao : "";?></textarea>
                            </div>
                            <div class="col-md-2">
                                <label for="slEnviar">Comunicar ao solicitante</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slEnviar" name="enviar">
                                </select> 
                                <small class="text-primary">Enviar mensagem WhatsApp para o solicitante</small>
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
                        <label for="ptxReserva">Reserva</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxReserva"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxSolicitacao">Solicitacao UTC</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxSolicitacao"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxMatricula">Matrícula</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxMatricula"/>
                    </div>
                </div>
                <div class="row mt-2"> 
                    <div class="col-md-6">
                        <label for="ptxOrigem">Origem UTC</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxOrigem"/>
                    </div> 
                    <div class="col-md-6">
                        <label for="ptxDestino">Destino UTC</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxDestino"/>
                    </div>     
                </div>
                <div class="row mt-2">                                       
                    <div class="col-md-6">
                        <label for="ptxSolicitante">Solicitante</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxSolicitante"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxEnvio">Comunicado em UTC</label>
                        <input type="text" class="form-control input-lg" id="ptxEnvio"/>
                    </div> 
                </div>
                <div class="row mt-2">                           
                    <div class="col-md-6">
                        <label for="pslSituacao">Situação</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSituacao">
                        </select> 
                    </div>
                    <div class="col-md-6">
                        <label for="ptxObservacao">Observacao</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxObservacao"/>
                    </div>
                </div>
                <br>
                <div class="row mt-2">                     
                    <div class="col-md-8">
                        <label for="pslOrdenacao">Ordenação da lista</label>
                        <select class="form-select selCookie input-lg" id="pslOrdenacao">
                            <option <?php echo ($ordenacao == 'us.cadastro desc,reserva') ? 'selected' : '';?> value='us.cadastro desc,reserva'>Solicitação</option>
                            <option <?php echo ($ordenacao == 'reserva') ? 'selected' : '';?> value='reserva'>Reserva</option>
                            <option <?php echo ($ordenacao == 'rs.matricula,reserva') ? 'selected' : '';?> value='matricula,reserva'>Matricula</option>
                            <option <?php echo ($ordenacao == 'us.nome,reserva') ? 'selected' : '';?> value='us.nome,reserva'>Solicitante</option>
                        </select> 
                    </div>                            
                </div>
            </div>
            <?php barraFuncoesPesquisa($titulo); ?>
        </div>
    </div>
</div>

<!-- *************************************************** -->
<!-- Modal Geral -->
<!-- *************************************************** -->
<?php modalVisualizar(); ?>

<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** --> 

<script src="../reserva/rsFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#slSituacao").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND rs.idAeroporto = "+$("#hdAeroporto").val() : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxReserva":
                            filtro += " AND CONCAT(rs.ano,'/',rs.mes,'/',rs.numero) LIKE '%"+$("#ptxReserva").val()+"%'";
                            descricaoFiltro += " <br>Reserva : "+$("#ptxReserva").val();
                        break;
                        case "ptxSolicitacao":
                            filtro += " AND DATE_FORMAT(rs.cadastro,'%d/%m/%Y %H:%i') LIKE '%"+$("#ptxSolicitacao").val()+"%'";
                            descricaoFiltro += " <br>Solicitação : "+$("#ptxSolicitacao").val();
                        break;
                        case "ptxMatricula":
                            filtro += " AND CONCAT(rs.matricula,' - ',eq.equipamento) LIKE '%"+$("#ptxMatricula").val()+"%'";
                            descricaoFiltro += " <br>Matrícula : "+$("#ptxMatricula").val();
                        break;                            
                        case "ptxOrigem":
                            filtro += " AND CONCAT(rs.origem,' ',DATE_FORMAT(rs.chegada,'%d/%m/%Y %H:%i')) LIKE '%"+$("#ptxOrigem").val()+"%'";
                            descricaoFiltro += " <br>Origem : "+$("#ptxOrigem").val();
                        break;
                        case "ptxDestino":
                            filtro += " AND CONCAT(rs.destino,' ',DATE_FORMAT(rs.partida,'%d/%m/%Y %H:%i')) LIKE '%"+$("#ptxDestino").val()+"%'";
                            descricaoFiltro += " <br>Destino : "+$("#ptxDestino").val();
                        break;
                        case "ptxSolicitante":
                            filtro += " AND CONCAT(us.usuario,' - ',us.nome) LIKE '%"+$("#ptxSolicitante").val()+"%'";
                            descricaoFiltro += " <br>Solicitante : "+$("#ptxSolicitante").val();
                        break;
                        case "ptxEnvio":
                            filtro += " AND DATE_FORMAT(rs.envio,'%d/%m/%Y %H:%i') LIKE '%"+$("#ptxEnvio").val()+"%'";
                            descricaoFiltro += " <br>Envio : "+$("#ptxEnvio").val();
                        break;
                        case "pslSituacao":
                            filtro += " AND rs.situacao = '"+$("#pslSituacao").val()+"'";
                            descricaoFiltro += " <br>Situação : "+$("#pslSituacao :selected").text();
                        break;        
                        case "ptxObservacao":
                            filtro += " AND rs.observacao LIKE '%"+$("#ptxObservacao").val()+"%'";
                            descricaoFiltro += " <br>Observação : "+$("#ptxObservacao").val();
                        break;                   
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });

            // Montagem da ordem
            var ordem = $("#pslOrdenacao").val();

            await criarCookie($('#hdSiglaAeroporto').val()+'_opAR_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opAR_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opAR_descricao', descricaoFiltro);
                        
            await rsCarregarReservas('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slTipo").focus();
        }

        // Preparando e chamando o modal de acordo com o tipo
        if ($('#hdEvento').val() == "visualizar") {
            await rsVisualizarReservasHistoricos(" AND rs.id = "+$('#hdId').val(),"reserva,rh.cadastro");
            $('#botaoVisualizar').trigger('click');
        }

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="Reservas">';
            form += '<input type="hidden" name="titulo" value="' + $('#divTitulo').text() + '">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divImpressao').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="P">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
            $("#slSituacao").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_opAR_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_opAR_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_opAR_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxRecurso").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('ReservasSituacao','#pslSituacao','','','Consultar');
        });

        // Adequações para o cadastro  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND rs.idAeroporto = "+$("#hdAeroporto").val() : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }
        await suCarregarSelectTodas('ReservasSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await suCarregarSelectTodos('TodosSimNao','#slEnviar', $('#hdEnviar').val(),'','Cadastrar');
        await rsCarregarReservas('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#slSituacao").focus();
    });
</script>