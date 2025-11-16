<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

// Modal para a inclusão rápida de informações
require_once("../modais/mdFormulario.php");

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']); 

// Recuperando as informações do Aeroporto
$utcAeroporto = $_SESSION['plantaUTCSite'];
$siglaAeroporto = $_SESSION['plantaSite'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $aeroporto = carregarPosts('site'); 
    $formulario = carregarPosts('formulario'); 
    $grupo = carregarPosts('grupo'); 

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
          
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
	// Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['site','formulario','grupo']); 
    if (!$erros) {
        try {
            $conexao = conexao();
            $array = explode('#', $formulario);
            if ($id != "") {
                $comando = "UPDATE planta_restricoes SET idSite = ".$aeroporto.", sistema = '".$array[0]."', formulario = '".$array[1].
                            "', grupo = '".$grupo."', cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO planta_restricoes (idSite, sistema, formulario, grupo, cadastro) VALUES (".
                            $aeroporto.", '".$array[0]."', '".$array[1]."', '".$grupo."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando);               
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("planta_restricoes", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaSite'], 
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
        $comando = "SELECT * FROM planta_restricoes WHERE id = ".$id;
        $sql = $conexao->prepare($comando);     
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $aeroporto = $dados['idSite'];
                $formulario = $dados['sistema']."#".$dados['formulario'];
                $grupo = $dados['grupo'];
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
        $comando = "DELETE FROM planta_restricoes WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("planta_restricoes", "Exclusão", $_SESSION['plantaSite'], $_SESSION['plantaUsuario'], $id, $comando);            
            montarMensagem("success",array("Registro excluído com sucesso!"));
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
    $aeroporto = null;
    $formulario = null;
    $grupo = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adCRS_ordenacao','ae.iata,re.sistema,me.descricao,re.grupo');
metaTagsBootstrap('');
$titulo = "Restrições para Formulários";
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
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdFormulario" <?="value=\"{$formulario}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdGrupo" <?="value=\"{$grupo}\"";?>/>
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">                
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="slAeroporto">Aeroporto</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slAeroporto" name="aeroporto">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                            <label for="slFormulario">Formulário</label>
                            <div class="input-group">
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slFormulario" name="formulario">
                                </select>
                                <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mdFormulario"> + </button> -->
                            </div>
                            </div>
                            <div class="col-md-4">
                                <label for="slGrupo">Grupo</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slGrupo" name="grupo">
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
                    <div class="col-md-8">
                        <label for="pslAeroporto">Aeroporto</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslAeroporto">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">                      
                    <div class="col-md-8">
                        <label for="pslSistema">Sistema</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSistema">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">                                                
                    <div class="col-md-12">
                        <label for="pslFormulario">Formulário</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslFormulario">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">                      
                    <div class="col-md-8">
                        <label for="pslGrupo">Grupo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslGrupo">
                        </select>
                    </div>
                </div>
                <br>
                <div class="row mt-2">                            
                    <div class="col-md-8">
                        <label for="pslOrdenacao">Ordenação da lista</label>
                        <select class="form-select selCookie input-lg" id="pslOrdenacao">
                            <option <?php echo ($ordenacao == 'ae.iata,re.sistema,me.descricao,re.grupo') ? 'selected' : '';?> value='ae.iata,re.sistema,me.descricao,re.grupo'>Aeroporto</option>
                            <option <?php echo ($ordenacao == 're.sistema,ae.iata,me.descricao,re.grupo') ? 'selected' : '';?> value='re.sistema,ae.iata,me.descricao,re.grupo'>Sistema</option>
                            <option <?php echo ($ordenacao == 're.grupo,ae.iata,re.sistema,me.descricao') ? 'selected' : '';?> value='re.grupo,ae.iata,re.sistema,me.descricao'>Grupo</option>
                            <option <?php echo ($ordenacao == 'me.descricao,ae.iata,re.sistema,re.grupo') ? 'selected' : '';?> value='me.descricao,ae.iata,re.sistema,re.grupo'>Formulário</option>
                        </select> 
                    </div>
                </div>  
            </div>
            <?php barraFuncoesPesquisa($titulo); ?>
        </div>
    </div>
</div>
<!-- *************************************************** -->

<!-- *************************************************** -->
<!-- Modal Cadastros Rápidos -->
<!-- *************************************************** -->
<?php mdFormulario() ?>
<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** --> 

<!-- *************************************************** -->
<!-- Script para  Cadastros Rápidos -->
<!-- *************************************************** -->
<script src="../modais/mdFormulario.js"></script>
<!-- *************************************************** -->

<script src="../administracao/adFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#slAeroporto").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pslAeroporto":
                            filtro += " AND re.idSite = "+$("#pslAeroporto").val();
                            descricaoFiltro += " <br>Aeroporto : "+$("#pslAeroporto :selected").text();
                            break;
                        case "pslSistema":
                            filtro += " AND re.sistema = '"+$("#pslSistema").val()+"'";
                            descricaoFiltro += " <br>Sistema : "+$("#pslSistema :selected").text();
                            break;                            
                        case "pslFormulario":
                            filtro += " AND CONCAT(me.sistema,'#',me.formulario) = '"+$("#pslFormulario").val()+"'";
                            descricaoFiltro += " <br>Formulário : "+$("#slFormulario :selected").text();
                        break; 
                        case "pslGrupo":
                            filtro += " AND re.grupo = '"+$("#pslGrupo").val()+"'";
                            descricaoFiltro += " <br>Grupo : "+$("#pslFormulario :selected").text();
                            break;                           
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });            

            // Montagem da ordem
            var ordem = $("#pslOrdenacao").val();
                        
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCRS_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCRS_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCRS_descricao', descricaoFiltro);

            await adCarregarRestricoes('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slFormulario").focus();
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
            $("#slFormulario").focus();
        });      
        
        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adCRS_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adCRS_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adCRS_fescricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#pslAeroporto").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){         
            await suCarregarSelectTodos('TodosSistema','#pslSistema', '', '','Consultar');
            await suCarregarSelectTodos('AcessosGrupos','#pslGrupo','','','Consultar');
            await adCarregarSelectMenusFormulario('#pslFormulario', '','','Consultar');
            await suCarregarSelectTodos('AeroportosClientes','#pslAeroporto', '', '','Consultar');
        });

        // Adequações para o cadastro
        await suCarregarSelectTodos('AcessosGrupos','#slGrupo', $('#hdGrupo').val(),'','Cadastrar');
        await adCarregarSelectMenusFormulario('#slFormulario', $('#hdFormulario').val(),'','Cadastrar');
        await suCarregarSelectTodos('AeroportosClientes','#slAeroporto', $('#hdSite').val(), '','Cadastrar');
        await adCarregarRestricoes('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#slAeroporto").focus();
    });
</script>


