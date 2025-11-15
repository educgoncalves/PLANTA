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
$usuario = $_SESSION['plantaIDUsuario'];
$nomeUsuario = $_SESSION['plantaUsuario'];
$sistema = $_SESSION['plantaSistema'];
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
    $data = carregarPosts('data');
    $ntSistema = carregarPosts('sistema');
    $ntAeroporto = carregarPosts('aeroporto');
    $notificacao = carregarPosts('notificacao');
    $situacao = carregarPosts('situacao');        

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
        
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
    // Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['situacao']);
    if (!$erros) {
        try {
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE gear_notificacoes SET situacao = '".$situacao."' WHERE id = ".$id;
            } else {
                throw new PDOException("Não é permitida a inclusão de reserva por este formulário!");
            }
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_notificacoes", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
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
    $dados = ['tabela'=>'Notificacoes','filtro'=>" AND nt.id = ".$id,'ordem'=>'nt.cadastro','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $data = $dados['dataHoraCadastro'];
            $ntSistema = $dados['sistema'];
            $ntAeroporto = $dados['aeroporto'];
            $notificacao = $dados['notificacao'];
            $situacao = $dados['situacao'];             
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
        $comando = "DELETE FROM gear_notificacoes WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_notificacoes", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $id, $comando);   
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
if ($limparCampos == true) {
    $data = null;
    $ntSistema = null;
    $ntAeroporto = null;
    $notificacao = null;
    $situacao = null;        
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_svMN_ordenacao','nt.cadastro');     
metaTagsBootstrap('');
$titulo = "Notificacoes para o usuário ".$_SESSION['plantaUsuario'];
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <form action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;">
            <?php barraFuncoesCadastro($titulo); ?>         
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                <input type="hidden" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" id="hdNomeUsuario" <?="value=\"{$nomeUsuario}\"";?>/>

                <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\""?>/>
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">                
                        <div class="row mt-2">  
                            <div class="col-md-2">
                                <label for="txData">Data</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txData" name="data" readonly
                                    <?php echo (!isNullOrEmpty($data)) ? "value=\"{$data}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txNtSistema">Sistema</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txNtSistema" name="ntSistema" readonly
                                    <?php echo (!isNullOrEmpty($ntSistema)) ? "value=\"{$ntSistema}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txNtAeroporto">Aeroporto</label>
                                <input type="text" class="form-control cpoObrigatorio input-lg" id="txNtAeroporto" name="ntAeroporto" readonly
                                    <?php echo (!isNullOrEmpty($ntAeroporto)) ? "value=\"{$ntAeroporto}\"" : "";?>>
                            </div>
                        </div>
                        <div class="row mt-2"> 
                            <div class="col-md-10">
                                <label for="txNotificacao">Notificação</label>
                                <textarea class="form-control cpoObrigatorio input-lg" id="txNotificacao" name="notificacao" readonly
                                    rows="2" cols="50"><?php echo (!isNullOrEmpty($notificacao)) ? $notificacao : "";?></textarea>
                            </div> 
                        </div>
                        <div class="row mt-2">                           
                            <div class="col-md-3">
                                <label for="slSituacao">Situação</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slSituacao" name="situacao">
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
                        <label for="ptxData">Data</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxData"/>
                    </div>
                    <div class="col-md-3">
                        <label for="ptxNtSistema">Sistema</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxNtSistema"/>
                    </div>
                    <div class="col-md-3">
                        <label for="txNtAeroporto">Aeroporto</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxNtAeroporto" name="ntAeroporto"/>
                    </div>
                </div>
                <div class="row mt-2"> 
                    <div class="col-md-12">
                        <label for="ptxNotificacao">Notificação</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNotificacao"/>
                    </div> 
                </div>
                <div class="row mt-2">                           
                    <div class="col-md-6">
                        <label for="pslSituacao">Situação</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSituacao">
                        </select> 
                    </div>
                </div>
                <br>
                <div class="row mt-2">                     
                    <div class="col-md-8">
                        <label for="pslOrdenacao">Ordenação da lista</label>
                        <select class="form-select selCookie input-lg" id="pslOrdenacao">
                            <option <?php echo ($ordenacao == 'nt.cadastro') ? 'selected' : '';?> value='nt.cadastro'>Data</option>
                            <option <?php echo ($ordenacao == 'nt.situacao,nt.cadastro') ? 'selected' : '';?> value='nt.situacao,nt.cadastro'>Situação</option>
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

<script src="../servicos/svFuncoes.js"></script>
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
            filtro = (!isEmpty($("#hdUsuario").val()) ? " AND nt.idUsuario = "+$("#hdUsuario").val() : "");
            descricaoFiltro = (!isEmpty($("#hdUsuario").val()) ? ' <br>Usuario : '+$("#hdNomeUsuario").val(): '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxData":
                            filtro += " AND DATE_FORMAT(nt.cadastro,'%d/%m/%Y %H:%i') LIKE '%"+$("#ptxData").val()+"%'";
                            descricaoFiltro += " <br>Data : "+$("#ptxData").val();
                        break;
                        case "ptxNtSistema":
                            filtro += " AND nt.sistema = '"+$("#ptxNtSistema").val()+"'";
                            descricaoFiltro += " <br>Aeroporto : "+$("#ptxNtSistema").val();
                        break;
                        case "ptxNtAeroporto":
                            filtro += " AND ae.icao = '"+$("#ptxNtAeroporto").val()+"'";
                            descricaoFiltro += " <br>Aeroporto : "+$("#ptxNtAeroporto").val();
                        break;
                        case "ptxNotificacao":
                            filtro += " AND nt.notificacao LIKE '%"+$("#ptxNotificacao").val()+"%'";
                            descricaoFiltro += " <br>Notificação : "+$("#ptxNotificacao").val();
                        break; 
                        case "pslSituacao":
                            filtro += " AND nt.situacao = '"+$("#pslSituacao").val()+"'";
                            descricaoFiltro += " <br>Situação : "+$("#pslSituacao :selected").text();
                        break;        
                  
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });

            // Montagem da ordem
            var ordem = $("#pslOrdenacao").val();

            await criarCookie($('#hdSiglaAeroporto').val()+'_svMN_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_svMN_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_svMN_descricao', descricaoFiltro);
                        
            await svCarregarNotificacoes('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slSituacao").focus();
        }

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
            $("#slSituacao").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_svMN_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_svMN_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_svMN_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxRecurso").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('NotificacoesSituacao','#pslSituacao','','','Consultar');
        });

        // Adequações para o cadastro  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdUsuario").val()) ? " AND nt.idUsuario = "+$("#hdUsuario").val() : "");
            pesquisaDescricao = (!isEmpty($("#hdUsuario").val()) ? ' <br>Usuario : '+$("#hdNomeUsuario").val(): '');
        }
        await suCarregarSelectTodas('NotificacoesSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await svCarregarNotificacoes('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
        $("#slSituacao").focus();
    });
</script>