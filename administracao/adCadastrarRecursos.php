<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
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

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $recurso = carregarPosts('recurso');
    $descricao = carregarPosts('descricao');
    $tipo = carregarPosts('tipo');
    $utilizacao = carregarPosts('utilizacao');
    $natureza = carregarPosts('natureza');
    $classe = carregarPosts('classe');
    $sentido = carregarPosts('sentido');
    $capacidade = carregarPosts('capacidade');
    $unidade = carregarPosts('unidade');
    $envergadura = carregarPosts('envergadura');
    $comprimento = carregarPosts('comprimento');
    $direita = carregarPosts('direita');
    $esquerda = carregarPosts('esquerda');
    $grupamento = carregarPosts('grupamento');
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
    $erros = camposPreenchidos(['recurso','tipo','natureza']);
    if (!$erros) {
        try {
            $capacidade = mudarEmptyZeroMysql($capacidade);
            $direita = mudarEmptyNuloMysql($direita);
            $esquerda = mudarEmptyNuloMysql($esquerda);
            $grupamento = mudarEmptyNuloMysql($grupamento);
            $vlrEnvergadura = mudarDecimalMysql($envergadura);
            $vlrComprimento = mudarDecimalMysql($comprimento);
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE gear_recursos SET idAeroporto = ".$aeroporto.", recurso = '".$recurso."', descricao = '".$descricao."', tipo = '".
                            $tipo."', utilizacao = '".$utilizacao."', natureza = '".$natureza."', classe = '".$classe."', capacidade = ".$capacidade.
                            ", unidade = '".$unidade."',envergadura=".$vlrEnvergadura.",comprimento=".$vlrComprimento.", situacao = '".$situacao.
                            "', sentido = '".$sentido."', idDireita = ".$direita.", idEsquerda = ".$esquerda.", idGrupamento = ".$grupamento.
                            ", cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO gear_recursos (idAeroporto, recurso, descricao, tipo, utilizacao, natureza, classe, capacidade, unidade, ".
                            "situacao, sentido, envergadura, comprimento, idDireita, idEsquerda, idGrupamento, cadastro) VALUES (".
                            $aeroporto.", '".$recurso."', '".$descricao."', '".$tipo."', '".$utilizacao."', '".$natureza."', '".
                            $classe."', ".$capacidade.", '".$unidade."', '".$situacao."', '".$sentido."',".$vlrEnvergadura.",".
                            $vlrComprimento.",".$direita.",".$esquerda.",".$grupamento.", UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_recursos", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
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
        $comando = "SELECT * FROM gear_recursos WHERE id = ".$id;
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $recurso = $dados['recurso'];
                $descricao = $dados['descricao'];
                $tipo = $dados['tipo'];
                $utilizacao = $dados['utilizacao'];
                $natureza = $dados['natureza'];
                $classe = $dados['classe'];
                $sentido = $dados['sentido'];                
                $capacidade = $dados['capacidade'];
                $unidade = $dados['unidade'];
                $envergadura = $dados['envergadura'];
                $comprimento = $dados['comprimento'];
                $direita = $dados['idDireita'];
                $esquerda = $dados['idEsquerda'];
                $grupamento = $dados['idGrupamento'];
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
        $comando = "DELETE FROM gear_recursos WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_recursos", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $id, $comando);   
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
    $recurso = null;
    $descricao = null;
    $tipo = null;
    $utilizacao = null;
    $natureza = null;
    $classe = null;
    $sentido = null;
    $capacidade = 0;
    $unidade = null;
    $envergadura = 0;
    $comprimento = 0;
    $direita = null;
    $esquerda = null;
    $grupamento = null;
    $situacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adCR_ordenacao','re.tipo,re.recurso');     
metaTagsBootstrap('');
$titulo = "Recursos";
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
                <input type="hidden" class="cpoLimpar" id="hdTipo" <?="value=\"{$tipo}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdUtilizacao" <?="value=\"{$utilizacao}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdNatureza" <?="value=\"{$natureza}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdClasse" <?="value=\"{$classe}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSentido" <?="value=\"{$sentido}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdUnidade" <?="value=\"{$unidade}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdDireita" <?="value=\"{$direita}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdEsquerda" <?="value=\"{$esquerda}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdGrupamento" <?="value=\"{$grupamento}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">                
                        <div class="row mt-2">  
                            <div class="col-md-2">
                                <label for="slTipo">Tipo</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slTipo" name="tipo">
                                </select>
                            </div>     
                        </div>
                        <div class="row mt-2">                                       
                            <div class="col-md-2">
                                <label for="txRecurso">Identificação</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txRecurso" name="recurso"
                                    <?php echo (!isNullOrEmpty($recurso)) ? "value=\"{$recurso}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txDescricao">Descrição</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txDescricao" name="descricao"
                                    <?php echo (!isNullOrEmpty($descricao)) ? "value=\"{$descricao}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="slUtilizacao">Utilização</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slUtilizacao" name="utilizacao">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="slNatureza">Natureza</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slNatureza" name="natureza">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="slClasse">Classe</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slClasse" name="classe">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="slSentido">Sentido</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slSentido" name="sentido">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">                           
                            <div class="col-md-2">
                                <label for="txCapacidade">Capacidade</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txCapacidade" name="capacidade" maxlength="3"
                                    <?php echo (!isNullOrEmpty($capacidade)) ? "value=\"{$capacidade}\"" : "";?>/>
                            </div>  
                            <div class="col-md-2">
                                <label for="slUnidade">Unidade</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slUnidade" name="unidade">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="txEnvergadura">Envergadura</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txEnvergadura" name="envergadura" maxlength="6"
                                    <?php echo (!isNullOrEmpty($envergadura)) ? "value=\"{$envergadura}\"" : "";?>/>
                            </div>                            
                            <div class="col-md-2">
                                <label for="txComprimento">Comprimento</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txComprimento" name="comprimento" maxlength="6"
                                    <?php echo (!isNullOrEmpty($comprimento)) ? "value=\"{$comprimento}\"" : "";?>/>
                            </div> 
                            <div class="col-md-2">
                                <label for="slDireita">Direita</label>
                                <select class="form-select selLimpar input-lg" id="slDireita" name="direita">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="slEsquerda">Esquerda</label>
                                <select class="form-select selLimpar input-lg" id="slEsquerda" name="esquerda">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">  
                            <div class="col-md-2">
                                <label for="slGrupamento">Grupamento</label>
                                <select class="form-select selLimpar input-lg" id="slGrupamento" name="grupamento">
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
                        <label for="pslTipo">Tipo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslTipo">
                        </select>
                    </div>     
                </div>
                <div class="row mt-2">                                       
                    <div class="col-md-6">
                        <label for="ptxRecurso">Identificação</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxRecurso"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxDescricao">Descrição</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxDescricao"/>
                    </div>
                </div>
                <div class="row mt-2">                      
                    <div class="col-md-6">
                        <label for="pslUtilizacao">Utilização</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslUtilizacao">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="pslNatureza">Natureza</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslNatureza">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">                       
                    <div class="col-md-6">
                        <label for="pslClasse">Classe</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslClasse">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="pslSentido">Sentido</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSentido">
                        </select>
                    </div>
                </div>
                <div class="row mt-2"> 
                    <div class="col-md-6">
                        <label for="ptxCapacidade">Capacidade</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxCapacidade" maxlength="3"/>
                    </div>  
                    <div class="col-md-6">
                        <label for="pslUnidade">Unidade</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslUnidade">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">     
                    <div class="col-md-4">
                        <label for="ptxEnvergadura">Envergadura</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEnvergadura" maxlength="6"/>
                    </div>                                          
                    <div class="col-md-4">
                        <label for="ptxComprimento">Comprimento</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxComprimento" maxlength="6"/>
                    </div> 
                    <div class="col-md-4">
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
                            <option <?php echo ($ordenacao == 're.tipo,re.recurso') ? 'selected' : '';?> value='re.tipo,re.recurso'>Tipo</option>
                            <option <?php echo ($ordenacao == 're.recurso,re.tipo') ? 'selected' : '';?> value='re.recurso,re.tipo'>Identificação</option>
                            <option <?php echo ($ordenacao == 're.natureza,re.tipo,re.recurso') ? 'selected' : '';?> value='re.natureza,re.tipo,re.recurso'>Natureza</option>
                            <option <?php echo ($ordenacao == 're.situacao,re.tipo,re.recurso') ? 'selected' : '';?> value='re.situacao,re.tipo,re.recurso'>Situacão</option>
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
            $("#slTipo").change();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND re.idAeroporto = "+$("#hdAeroporto").val() : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxRecurso":
                            filtro += " AND re.recurso LIKE '%"+$("#ptxRecurso").val()+"%'";
                            descricaoFiltro += " <br>Identificação : "+$("#ptxRecurso").val();
                        break;
                        case "ptxDescricao":
                            filtro += " AND re.descricao LIKE '%"+$("#ptxDescricao").val()+"%'";
                            descricaoFiltro += " <br>Descrição : "+$("#ptxDescricao").val();
                        break;                            
                        case "pslTipo":
                            filtro += " AND re.tipo = '"+$("#pslTipo").val()+"'";
                            descricaoFiltro += " <br>Tipo : "+$("#pslTipo :selected").text();
                        break;
                        case "pslUtilizacao":
                            filtro += " AND re.utilizacao = '"+$("#pslUtilizacao").val()+"'";
                            descricaoFiltro += " <br>Utilização : "+$("#pslUtilizacao :selected").text();
                        break;
                        case "pslNatureza":
                            filtro += " AND re.natureza = '"+$("#pslNatureza").val()+"'";
                            descricaoFiltro += " <br>Natureza : "+$("#pslNatureza").val();
                        break;
                        case "pslClasse":
                            filtro += " AND re.classe = '"+$("#pslClasse").val()+"'";
                            descricaoFiltro += " <br>Classe : "+$("#pslClasse :selected").text();
                        break;
                        case "pslSentido":
                            filtro += " AND re.sentido = '"+$("#pslSentido").val()+"'";
                            descricaoFiltro += " <br>Sentido : "+$("#pslSentido :selected").text();
                        break;                               
                        case "ptxCapacidade":
                            filtro += " AND re.capacidade = "+$("#ptxCapacidade").val();
                            descricaoFiltro += " <br>Capacidade : "+$("#ptxCapacidade").val();
                        break;
                        case "pslUnidade":
                            filtro += " AND re.unidade = '"+$("#pslUnidade").val()+"'";
                            descricaoFiltro += " <br>Unidade : "+$("#pslUnidade :selected").text();
                        break;
                        case "ptxEnvergadura":
                            filtro += " AND re.envergadura = "+mudarDecimalMysql($("#ptxEnvergadura").val());
                            descricaoFiltro += " <br>Envergadura : "+$("#ptxEnvergadura").val();
                            break;
                        case "ptxComprimento":
                            filtro += " AND re.comprimento = "+mudarDecimalMysql($("#ptxComprimento").val());
                            descricaoFiltro += " <br>Comprimento : "+$("#ptxComprimento").val();
                            break; 
                        case "pslSituacao":
                            filtro += " AND re.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_adCR_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCR_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCR_descricao', descricaoFiltro);
                        
            await adCarregarRecursos('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slTipo").focus();
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
            $("#slTipo").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adCR_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adCR_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adCR_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxRecurso").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('RecursosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodos('RecursosSentido','#pslSentido','','','Consultar');
            await suCarregarSelectTodas('RecursosUnidade','#pslUnidade','','','Consultar');
            await suCarregarSelectTodas('RecursosClasse','#pslClasse','','','Consultar');
            await suCarregarSelectTodas('RecursosNatureza','#pslNatureza','','','Consultar');
            await suCarregarSelectTodos('RecursosUtilizacao','#pslUtilizacao','','','Consultar');
            await suCarregarSelectTodos('RecursosTipo','#pslTipo','','','Consultar');
        });
        $("#ptxCapacidade").mask('##0', {reverse: true});
        $("#ptxEnvergadura").mask('##0,00', {reverse: true});
        $("#ptxComprimento").mask('##0,00', {reverse: true});

        // Adequações para o cadastro  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND re.idAeroporto = "+$("#hdAeroporto").val() : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }
        await suCarregarSelectTodas('RecursosSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await suCarregarSelectTodos('RecursosSentido','#slSentido', $('#hdSentido').val(),'','Cadastrar',false);
        await suCarregarSelectTodas('RecursosUnidade','#slUnidade', $('#hdUnidade').val(),'','Cadastrar',false);
        await suCarregarSelectTodas('RecursosClasse','#slClasse', $('#hdClasse').val(),'','Cadastrar',false);
        await suCarregarSelectTodas('RecursosNatureza','#slNatureza', $('#hdNatureza').val(),'','Cadastrar', false);
        await suCarregarSelectTodas('RecursosUtilizacao','#slUtilizacao', $('#hdUtilizacao').val(),'','Cadastrar',false);
        await suCarregarSelectTodos('RecursosTipo','#slTipo', $('#hdTipo').val(),'','Cadastrar');
        await suCarregarSelectTodos('Recursos','#slDireita', $('#hdDireita').val()," AND re.idAeroporto = "+$("#hdAeroporto").val()+" AND re.Tipo = '"+$('#hdTipo').val()+"'",'Cadastrar');
        await suCarregarSelectTodos('Recursos','#slEsquerda', $('#hdEsquerda').val()," AND re.idAeroporto = "+$("#hdAeroporto").val()+" AND re.Tipo = '"+$('#hdTipo').val()+"'",'Cadastrar');
        await suCarregarSelectTodos('Recursos','#slGrupamento', $('#hdGrupamento').val()," AND re.idAeroporto = "+$("#hdAeroporto").val(),'Cadastrar');
        await adCarregarRecursos('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#slTipo").focus();
        $("#txCapacidade").mask('##0', {reverse: true});
        $("#txEnvergadura").mask('##0,00', {reverse: true});
        $("#txComprimento").mask('##0,00', {reverse: true});
        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

        $('#slTipo').change(async function(){
            await suCarregarSelectTodos('Recursos','#slDireita',''," AND re.idAeroporto = "+$("#hdAeroporto").val()+" AND re.Tipo = '"+$('#slTipo').val()+"'",'Cadastrar');
            await suCarregarSelectTodos('Recursos','#slEsquerda',''," AND re.idAeroporto = "+$("#hdAeroporto").val()+" AND re.Tipo = '"+$('#slTipo').val()+"'",'Cadastrar');
		});
    });
</script>