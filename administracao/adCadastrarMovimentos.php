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

// Recuperando as informações do Aeroporto
$usuario = $_SESSION['plantaUsuario'];
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$aeroportoDestino = carregarGets('aeroportoDestino',carregarPosts('aeroportoDestino'));
$copiarAeroporto = array('titulo'=>'Copiar movimentos do aeroporto '.$siglaAeroporto,
                        'aviso'=>'O cadastro de movimentos do aeroporto selecionado será atualizado com as informações
                        deste aeroporto.<br><br>Novos registros poderão ser incluídos.<br>Registros sem correspondência 
                        serão mantidos.<br><br>Favor verificar ao final do processo!');

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $movimento = carregarPosts('movimento');
    $descricao = carregarPosts('descricao');
    $operacao = carregarPosts('operacao');
    $ordem = carregarPosts('ordem');
    $sucessora = carregarPosts('sucessora');
    $antes = carregarPosts('antes');
    $depois = carregarPosts('depois');
    $antecessoras = carregarPosts('antecessoras');
    $destaque = carregarPosts('destaque');
    $alerta = carregarPosts('alerta');
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
    $erros = camposPreenchidos(['movimento','operacao','descricao']);
    if (!$erros) {
        try {
            $ordem = mudarEmptyZeroMysql($ordem);
            $antes = mudarEmptyZeroMysql($antes);
            $depois = mudarEmptyZeroMysql($depois);
            $alerta = mudarEmptyZeroMysql($alerta);
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE gear_movimentos SET idAeroporto = ".$aeroporto.",movimento = '".$movimento."',descricao = '".$descricao.
                            "',operacao = '".$operacao."',ordem = ".$ordem.",sucessora = '".$sucessora."',antes = ".$antes.",depois = ".$depois.
                            ",antecessoras = '".$antecessoras."',destaque = '".$destaque."',alerta = ".$alerta.
                            ",situacao = '".$situacao."',cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO gear_movimentos (idAeroporto,movimento,descricao,operacao,ordem,sucessora,antes,".
                            "depois,antecessoras,destaque,alerta,situacao, cadastro) VALUES (".
                            $aeroporto.",'".$movimento."','".$descricao."','".$operacao."',".$ordem.",'".$sucessora."',".$antes.",".$depois.
                            ",'".$antecessoras."','".$destaque."',".$alerta.",'".$situacao."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_movimentos", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
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
    try {
        $conexao = conexao();
        $comando = selectDB("Movimentos"," AND mo.id = ".$id,"");
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $movimento = $dados['movimento'];
                $descricao = $dados['descricao'];
                $operacao = $dados['operacao'];
                $ordem = $dados['ordem'];
                $sucessora = $dados['sucessora'];
                $antes = $dados['antes'];
                $depois = $dados['depois'];
                $antecessoras = $dados['antecessoras'];
                $destaque = $dados['destaque'];
                $alerta = $dados['alerta'];
                $situacao = $dados['situacao'];
            }
            $limparCampos = false;
        } else {
            throw new PDOException("Não foi possível recuperar este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "DELETE FROM gear_movimentos WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_movimentos", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $id, $comando);   
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

// Copiando informações para outro aeroporto
if ($evento == "executarCopiarAeroporto" && $aeroportoDestino != "") {
    $limparCampos = true;
    try {
        // Abrindo a transação
        $conexao = conexao();
		$conexao->beginTransaction();

        // Inativar todas os movimentos do aeroporto destino
        // $comando = "UPDATE gear_movimentos SET situacao = 'INA' WHERE idAeroporto = ".$aeroportoDestino;
        // $sql = $conexao->prepare($comando);  
        // if ($sql->execute()) {

            // Seleciona os registros do aeroporto de origem
            $comando = "SELECT * FROM gear_movimentos WHERE idAeroporto = ".$aeroporto." AND situacao = 'ATV'";
            $sql = $conexao->prepare($comando);  
            if ($sql->execute()) {
                $origem = $sql->fetchAll(PDO::FETCH_ASSOC);
                foreach ($origem as $dados) {
                    $movimento = $dados['movimento'];
                    $descricao = $dados['descricao'];
                    $operacao = $dados['operacao'];
                    $ordem = $dados['ordem'];
                    $sucessora = $dados['sucessora'];
                    $antes = $dados['antes'];
                    $depois = $dados['depois'];
                    $antecessoras = $dados['antecessoras'];
                    $destaque = $dados['destaque'];
                    $alerta = $dados['alerta'];
                    $situacao = $dados['situacao'];

                    // Verifica se a informação existe no aeroporto de destino para insert ou update
                    $comando = "SELECT id FROM gear_movimentos WHERE idAeroporto = ".$aeroportoDestino.
                                " AND movimento = '".$movimento."' AND operacao = '".$operacao."' LIMIT 1";
                    $sql = $conexao->prepare($comando);  
                    if ($sql->execute()) {
                        if ($sql->rowCount() > 0) {
                            $id = $sql->fetch(PDO::FETCH_ASSOC)['id'];
                            $comando = "UPDATE gear_movimentos SET descricao = '".$descricao."',operacao = '".$operacao."',ordem = ".
                                        $ordem.",sucessora = '".$sucessora."',antes = ".$antes.",depois = ".$depois.",antecessoras = '".
                                        $antecessoras."',destaque = '".$destaque."',alerta = ".$alerta.
                                        ",situacao = '".$situacao."',cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
                        } else {
                            $id = "";
                            $comando = "INSERT INTO gear_movimentos (idAeroporto,movimento,descricao,operacao,ordem,sucessora,antes,".
                                            "depois,antecessoras,destaque,alerta,situacao,cadastro) VALUES (".
                                            $aeroportoDestino.",'".$movimento."','".$descricao."','".$operacao."',".$ordem.",'".$sucessora.
                                            "',".$antes.",".$depois.",'".$antecessoras."','".$destaque."',".$alerta.",'".$situacao."', UTC_TIMESTAMP())";
                        }
                        $sql = $conexao->prepare($comando);
                        if ($sql->execute()) {
                            if ($sql->rowCount() > 0) {
                                gravaDLog("gear_movimentos", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
                                            $_SESSION['plantaUsuario'], ($id != "" ? $id  : $conexao->lastInsertId()), $comando);  
                            } else {
                                throw new PDOException("Não foi possível efetivar esta ".($id != "" ? "alteração" : "inclusão")."!");
                            }
                        } else {
                            throw new PDOException("Não foi possível ".($id != "" ? "alterar" : "incluir")." este registro!");
                        } 
                    } else {
                        throw new PDOException("Não foi possível recuperar a informação do aeroporto destino!");
                    } 
                }
                montarMensagem("success",array("Cópia realizada com sucesso!"));
                $conexao->commit();
            } else {
                throw new PDOException("Não foi possível recuperar as informações do aeroporto origem!");
            } 
        // } else {
        //     throw new PDOException("Não foi possível iniciar o processo de cópia para o aeroporto!");
        // } 

    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
        if ($conexao->inTransaction()) {$conexao->rollBack();}
    }
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $movimento = null;
    $descricao = null;
    $operacao = null;
    $ordem = null;
    $sucessora = null;
    $antes = null;
    $depois = null;
    $antecessoras = null;
    $destaque = null;
    $alerta = null;
    $situacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adCM_ordenacao','mo.ordem,mo.movimento,mo.operacao');     
metaTagsBootstrap('');
$titulo = "Movimentos";
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
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\""?>/>
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                                
                <input type="hidden" id="hdEvento" name="evento" <?="value=\"{$evento}\"";?>/>

                <input type="hidden" class="cpoLimpar" id="hdOperacao" <?="value=\"{$operacao}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdDestaque" <?="value=\"{$destaque}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="d-flex justify-content-end pt-4">
                    <a href='?evento=copiarAeroporto' class="btn btn-outline-primary" role="button">Copiar informações para outro Aeroporto</a>
                </div>
                <div class="row pt-2">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">             
                        <div class="row mt-2">                                       
                            <div class="col-md-2">
                                <label for="txMovimento">Movimento</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txMovimento" name="movimento"
                                    <?php echo (!isNullOrEmpty($movimento)) ? "value=\"{$movimento}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="txDescricao">Descrição</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txDescricao" name="descricao"
                                    <?php echo (!isNullOrEmpty($descricao)) ? "value=\"{$descricao}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">                            
                            <div class="col-md-3">
                                <label for="slOperacao">Operação</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slOperacao" name="operacao">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="txOrdem">Ordenação</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txOrdem" name="ordem"
                                    <?php echo (!isNullOrEmpty($ordem)) ? "value=\"{$ordem}\"" : "";?>/>
                            </div>
                            <div class="col-md-1">
                                <label for="txSucessora">Sucessora</label>
                                <input type="text" class="form-control cpoLimpar caixaAlta input-lg" id="txSucessora" name="sucessora"
                                    <?php echo (!isNullOrEmpty($sucessora)) ? "value=\"{$sucessora}\"" : "";?>/>
                            </div>                           
                            <div class="col-md-1">
                                <label for="txAntes">Antes(min)</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txAntes" name="antes"
                                    <?php echo (!isNullOrEmpty($antes)) ? "value=\"{$antes}\"" : "";?>/>
                            </div>
                            <div class="col-md-1">
                                <label for="txDepois">Depois(min)</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txDepois" name="depois"
                                    <?php echo (!isNullOrEmpty($depois)) ? "value=\"{$depois}\"" : "";?>/>
                            </div>
                            <div class="col-md-1">
                                <label for="txAlerta">Alerta(min)</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txAlerta" name="alerta"
                                    <?php echo (!isNullOrEmpty($alerta)) ? "value=\"{$alerta}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">  
                            <div class="col-md-4">
                                <label for="txAntecessoras">Antecessoras</label>
                                <input type="text" class="form-control cpoLimpar caixaAlta input-lg" id="txAntecessoras" name="antecessoras"
                                    <?php echo (!isNullOrEmpty($antecessoras)) ? "value=\"{$antecessoras}\"" : "";?>/>
                            </div>                                                         
                            <div class="col-md-2">     
                                <label for="slDestaque">Destaque(fundo)</label>
                                <select class="form-select selLimpar input-lg" id="slDestaque" name="destaque">
                                </select> 
                            </div>
                            <div class="col-md-2">
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
<!-- Modal COPIAR PARA AEROPORTO -->
<!-- *************************************************** -->
<?php modalCopiarAeroporto($copiarAeroporto);?>

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
                    <div class="col-md-3">
                        <label for="ptxMovimento">Movimento</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxMovimento"/>
                    </div>
                </div>
                <div class="row mt-2">                  
                    <div class="col-md-6">
                        <label for="ptxDescricao">Descrição</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxDescricao"/>
                    </div>
                    <div class="col-md-6">
                        <label for="pslOperacao">Operação</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslOperacao">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">                      
                    <div class="col-md-3">
                        <label for="ptxSucessora">Sucessora</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxSucessora"/>
                    </div>                           
                    <div class="col-md-3">
                        <label for="ptxAntes">Antes (min)</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxAntes"/>
                    </div>
                    <div class="col-md-3">
                        <label for="ptxDepois">Depois (min)</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxDepois"/>
                    </div>
                    <div class="col-md-3">
                        <label for="ptxAlerta">Alerta (min)</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxAlerta"/>
                    </div>
                </div>
                <div class="row mt-2"> 
                    <div class="col-md-6">
                        <label for="ptxAntecessoras">Antecessoras</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxAntecessoras"/>
                    </div>                                            
                    <div class="col-md-3">
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
                            <option <?php echo ($ordenacao == 'mo.ordem,mo.movimento,mo.operacao') ? 'selected' : '';?> value='mo.ordem,mo.movimento,mo.operacao'>Ordem</option>
                            <option <?php echo ($ordenacao == 'mo.movimento,mo.operacao,mo.ordem') ? 'selected' : '';?> value='mo.movimento,mo.operacao,mo.ordem'>Movimento</option>
                            <option <?php echo ($ordenacao == 'mo.operacao,mo.movimento,mo.ordem') ? 'selected' : '';?> value='mo.operacao,mo.movimento,mo.ordem'>Operação</option>
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

<script src="../administracao/adFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            document.getElementById("slDestaque").style.backgroundColor = null;
            $("#txMovimento").focus();
        });

        // Chamando modal para eventos
        if ($('#hdEvento').val() == "copiarAeroporto") { $('#botaoCopiarAeroporto').trigger('click'); }
        await suCarregarSelectTodos('AeroportosClientes','#pslAeroportoDestino',''," AND ae.id <> "+$("#hdAeroporto").val(),'Cadastrar');

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND mo.idAeroporto = "+$("#hdAeroporto").val() : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxMovimento":
                            filtro += " AND mo.movimento LIKE '%"+$("#ptxMovimento").val()+"%'";
                            descricaoFiltro += " <br>Movimento : "+$("#ptxMovimento").val();
                        break;
                        case "ptxDescricao":
                            filtro += " AND mo.descricao LIKE '%"+$("#ptxDescricao").val()+"%'";
                            descricaoFiltro += " <br>Descrição : "+$("#ptxDescricao").val();
                        break;                            
                        case "pslOperacao":
                            filtro += " AND mo.operacao = '"+$("#pslOperacao").val()+"'";
                            descricaoFiltro += " <br>Operação : "+$("#pslOperacao :selected").text();
                        break;
                        case "ptxSucessora":
                            filtro += " AND mo.sucessora LIKE '%"+$("#ptxSucessora").val()+"%'";
                            descricaoFiltro += " <br>Sucessora : "+$("#ptxSucessora").val();
                        break; 
                        break;
                        case "ptxAntes":
                            filtro += " AND mo.antes = "+$("#ptxAntes").val();
                            descricaoFiltro += " <br>Antes (min) : "+$("#ptxAntes").val();
                        break; 
                        case "ptxDepois":
                            filtro += " AND mo.depois = "+$("#ptxDepois").val();
                            descricaoFiltro += " <br>Depois (min) : "+$("#ptxDepois").val();
                        break; 
                        case "ptxAlerta":
                            filtro += " AND mo.alerta = "+$("#ptxAlerta").val();
                            descricaoFiltro += " <br>Alerta (min) : "+$("#ptxAlerta").val();
                        break; 
                        case "ptxAntecessoras":
                            filtro += " AND mo.antecessoras LIKE '%"+$("#ptxAntecessoras").val()+"%'";
                            descricaoFiltro += " <br>Antecessoras : "+$("#ptxAntecessoras").val();
                        break;
                        case "pslSituacao":
                            filtro += " AND mo.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_adCM_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCM_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCM_descricao', descricaoFiltro);
                        
            await adCarregarMovimentos('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txMovimento").focus();
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
            $("#txMovimento").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adCM_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adCM_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adCM_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxMovimento").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodos('MovimentosOperacao','#pslOperacao','','','Consultar');
        }); 
        $("#ptxMovimento").mask('AAA', {'translation': {A: {pattern: /[A-Za-z]/} } });  
        $("#ptxOrdem").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#ptxSucessora").mask('AAA', {'translation': {A: {pattern: /[A-Za-z]/} } });  
        $("#ptxAntes").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#ptxDepois").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 

        // Adequações para o cadastro  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND mo.idAeroporto = "+$("#hdAeroporto").val() : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }
        await suCarregarSelectTodas('TodosDestaque','#slDestaque', $('#hdDestaque').val(),'','Cadastrar');
        await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await suCarregarSelectTodos('MovimentosOperacao','#slOperacao', $('#hdOperacao').val(),'','Cadastrar');
        await adCarregarMovimentos('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
    
        $("#txMovimento").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z]/} } });  
        $("#txOrdem").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txSucessora").mask('AAA', {'translation': {A: {pattern: /[A-Za-z]/} } }); 
        $("#txAntes").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txDepois").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txMovimento").focus();

        // Para mudar a classe quando o elemento for exibido
        const slDestaque = document.getElementById("slDestaque");
        removerClassesBg(slDestaque);
        slDestaque.classList.add('bg-'+$("#hdDestaque").val());

        // Para mudar a classe quando o elemento for alterado
        document.getElementById("slDestaque").addEventListener("change", function() {
            const slDestaque = document.getElementById("slDestaque");
            removerClassesBg(slDestaque);
            slDestaque.classList.add('bg-'+$("#slDestaque").val());
        });

        function removerClassesBg(elemento) {
            if (elemento && elemento.classList) {
                const classesToRemove = [];
                for (let i = 0; i < elemento.classList.length; i++) {
                    const className = elemento.classList[i];
                    if (className.startsWith('bg-')) {
                        classesToRemove.push(className);
                    }
                }
                classesToRemove.forEach(className => {
                elemento.classList.remove(className);
                });
            }
        }
    });
</script>