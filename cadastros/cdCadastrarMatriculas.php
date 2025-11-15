<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

// Controle de paginação
$_page = isset($_GET['page']) ? $_GET['page'] : 1;
$_paginacao = isset($_GET['paginacao']) ? $_GET['paginacao'] : 'NAO'; 
$_limite = isset($_GET['limite']) ? $_GET['limite'] : $_SESSION['plantaRegPorPagina'];  

// Recuperando as informações do Aeroporto
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $matricula = carregarPosts('matricula');
    $equipamento = carregarPosts('equipamento');
    $operador = carregarPosts('operador');
    $assentos = carregarPosts('assentos');
    $pmd = carregarPosts('pmd');
    $categoria = carregarPosts('categoria');
    $situacao = carregarPosts('situacao');
    $txMatriculasCategoria = carregarPosts('txMatriculasCategoria');
    $txEquipamento = carregarPosts('txEquipamento');
    $txOperador = carregarPosts('txOperador');
    $txTodosSituacao = carregarPosts('txTodosSituacao');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
    
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
	// Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['matricula','operador','equipamento']);
    if (!$erros) {
        try {
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE gear_matriculas SET matricula='".$matricula."',idEquipamento=".$equipamento.",idOperador=".$operador.
                            ",assentos=".$assentos.",pmd=".$pmd.",categoria='".$categoria."',situacao='".$situacao."',fonte='".$siglaAeroporto.
                            "',origem='MNL',cadastro=UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO gear_matriculas(matricula,idEquipamento,idOperador,assentos,pmd,categoria,situacao,origem,fonte,cadastro) VALUES ('".
                            $matricula."',".$equipamento.",".$operador.",".$assentos.",".$pmd.",'".$categoria."','".$situacao.
                            "','MNL','".$siglaAeroporto."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_matriculas", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
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
        $comando = selectDB("Matriculas"," AND mt.id = ".$id,"");
        $sql = $conexao->prepare($comando);   
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $matricula = $dados['matricula'];
                $equipamento = $dados['idEquipamento'];
                $operador = $dados['idOperador'];
                $assentos = $dados['assentos'];
                $pmd = $dados['pmd'];
                $categoria = $dados['categoria'];
                $situacao = $dados['situacao'];
                $txMatriculasCategoria = $dados['descCategoria'];
                $txEquipamento = $dados['equipamentoCompleto'];
                $txOperador = $dados['operadorCompleto'];
                $txTodosSituacao = $dados['descSituacao'];
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
        $comando = "DELETE FROM gear_matriculas WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_matriculas", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $id, $comando);   
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
    $matricula = null;
    $equipamento = null;
    $operador = null;
    $txOperador = null;
    $assentos = null;
    $pmd = null;
    $categoria = null;
    $situacao = null;
    $txMatriculasCategoria = null;
    $txEquipamento = null;
    $txOperador = null;
    $txTodosSituacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_cdCM_ordenacao','mt.matricula');
metaTagsBootstrap('');
$titulo = "Matrículas";
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
                <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\""?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="txMatricula">Matrícula</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txMatricula" name="matricula"
                                    <?php echo (!isNullOrEmpty($matricula)) ? "value=\"{$matricula}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">                            
                            <div class="col-md-4">
                                <label for="idOperador">Operador Aéreo</label>
                                <input type="text" class="form-select caixaAlta cpoLimpar input-lg" id="txOperador" placeholder="Selecionar" name="txOperador"
                                    <?php echo (!isNullOrEmpty($txOperador)) ? "value=\"{$txOperador}\"" : "";?>
                                    onfocus="iniciarPesquisa('Operador',this.value)" 
                                    oninput="executarPesquisa('Operador',this.value)" 
                                    onblur="finalizarPesquisa('Operador')"        
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimpar cpoObrigatorio" id="idOperador" name="operador" <?="value=\"{$operador}\"";?>/>                                        
                                <span id="spantxOperador"></span>   
                            </div>
                            <div class="col-md-8">
                                <label for="idEquipamento">Equipamento</label>
                                <input type="text" class="form-select caixaAlta cpoLimpar input-lg" id="txEquipamento" placeholder="Selecionar" name="txEquipamento"
                                    <?php echo (!isNullOrEmpty($txEquipamento)) ? "value=\"{$txEquipamento}\"" : "";?>
                                    onfocus="iniciarPesquisa('Equipamento',this.value)" 
                                    oninput="executarPesquisa('Equipamento',this.value)" 
                                    onblur="finalizarPesquisa('Equipamento')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimpar cpoObrigatorio" id="idEquipamento" name="equipamento" <?="value=\"{$equipamento}\"";?>/>                                        
                                <span id="spantxEquipamento"></span>     
                            </div>                            
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="txAssentos">Assentos</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txAssentos" name="assentos" maxlength="3"
                                    <?php echo (!isNullOrEmpty($assentos)) ? "value=\"{$assentos}\"" : "";?>/>
                            </div>  
                            <div class="col-md-2">
                                <label for="txPmd">PMD</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txPmd" name="pmd" maxlength="7"
                                    <?php echo (!isNullOrEmpty($pmd)) ? "value=\"{$pmd}\"" : "";?>/>
                            </div>  
                            <div class="col-md-4">
                                <label for="idMatriculasCategoria">Categoria</label>
                                <input type="text" class="form-select caixaAlta cpoLimpar input-lg" id="txMatriculasCategoria" placeholder="Selecionar" name="txMatriculasCategoria"
                                    <?php echo (!isNullOrEmpty($txMatriculasCategoria)) ? "value=\"{$txMatriculasCategoria}\"" : "";?>
                                    onfocus="iniciarPesquisa('MatriculasCategoria',this.value)" 
                                    oninput="executarPesquisa('MatriculasCategoria',this.value)" 
                                    onblur="finalizarPesquisa('MatriculasCategoria')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimpar cpoObrigatorio" id="idMatriculasCategoria" name="categoria" <?="value=\"{$categoria}\"";?>/>                                        
                                <span id="spantxMatriculasCategoria"></span>                                 
                            </div>                             
                            <div class="col-md-4">
                                <label for="idTodosSituacao">Situação</label>
                                <input type="text" class="form-select cpoLimpar input-lg" id="txTodosSituacao" placeholder="Selecionar" name="txTodosSituacao"
                                    <?php echo (!isNullOrEmpty($txTodosSituacao)) ? "value=\"{$txTodosSituacao}\"" : "";?>
                                    onfocus="iniciarPesquisa('TodosSituacao',this.value)" 
                                    oninput="executarPesquisa('TodosSituacao',this.value)" 
                                    onblur="finalizarPesquisa('TodosSituacao')"        
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimpar cpoObrigatorio" id="idTodosSituacao" name="situacao" <?="value=\"{$situacao}\"";?>/>                                        
                                <span id="spantxTodosSituacao"></span>  
                            </div>
                        </div>
                    </div>
                </div>
                <?php destacarTarefaRAB(); ?>  
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
                        <label for="ptxMatricula">Matrícula</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxMatricula"/>
                    </div>
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="ptxOperador">Operador Aéreo</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxOperador"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxEquipamento">Equipamento</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxEquipamento"/>
                    </div>                            
                    <div class="col-md-6">
                        <label for="ptxAssentos">Assentos</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxAssentos" maxlength="3"/>
                    </div>  
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="ptxPmd">PMD</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxPmd" maxlength="7"/>
                    </div>  
                    <div class="col-md-6">
                        <label for="pslCategoria">Categoria</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslCategoria">
                        </select> 
                    </div> 
                </div>
                <div class="row mt-2">                      
                    <div class="col-md-6">
                        <label for="pslFonte">Fonte</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslFonte">
                        </select> 
                    </div>    
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
                            <option <?php echo ($ordenacao == 'mt.matricula') ? 'selected' : '';?> value='mt.matricula'>Matrícula</option>
                            <option <?php echo ($ordenacao == 'op.nome,mt.matricula') ? 'selected' : '';?> value='op.nome,mt.matricula'>Operador</option>
                            <option <?php echo ($ordenacao == 'eq.modelo,mt.matricula') ? 'selected' : '';?> value='eq.modelo,mt.matricula'>Modelo</option>
                            <option <?php echo ($ordenacao == 'eq.fabricante,mt.matricula') ? 'selected' : '';?> value='eq.fabricante,mt.matricula'>Fabricante</option>
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

<script src="../cadastros/cdFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script src="../pesquisas/pqPesquisa.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#txMatricula").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
        
        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxMatricula":
                            filtro += " AND mt.matricula LIKE '%"+$("#ptxMatricula").val()+"%'";
                            descricaoFiltro += " <br>Matrícula : "+$("#ptxMatricula").val();
                        break;
                        case "ptxOperador":
                            filtro +=  " AND CONCAT(op.icao, ' - ', op.operador) LIKE '%"+$("#ptxOperador").val()+"%'";
                            descricaoFiltro += " <br>Operador : "+$("#ptxOperador").val();
                        break;
                        case "ptxEquipamento":
                            filtro += " AND CONCAT(eq.equipamento,eq.modelo,eq.fabricante) LIKE '%"+$("#ptxEquipamento").val()+"%'";
                            descricaoFiltro += " <br>Equipamento : "+$("#ptxEquipamento").val();
                        break;
                        case "ptxAssentos":
                            filtro += " AND mt.assentos = "+$("#ptxAssentos").val();
                            descricaoFiltro += " <br>Assentos : "+$("#ptxAssentos").val();
                        break;
                        case "ptxPmd":
                            filtro += " AND mt.pmd = "+$("#ptxPmd").val();
                            descricaoFiltro += " <br>PMD : "+$("#ptxPmd").val();
                        break;
                        case "pslCategoria":
                            filtro += " AND mt.categoria = '"+$("#pslCategoria").val()+"'";
                            descricaoFiltro += " <br>Categoria : "+$("#pslCategoria :selected").text();
                        break;   
                        case "pslFonte":
                            filtro += " AND CONCAT(mt.fonte,' - ',dm3.descricao) = '"+$("#pslFonte").val()+"'";
                            descricaoFiltro += " <br>Fonte : "+$("#pslFonte :selected").text();
                        break;                                                  
                        case "pslSituacao":
                            filtro += " AND mt.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCM_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCM_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCM_descricao', descricaoFiltro);

            await cdCarregarMatriculas('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txMatricula").focus();
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
            $("#txMatricula").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCM_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_cdCM_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCM_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxMatricula").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('MatriculasCategoria','#pslCategoria','','','Consultar');
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodas('FonteMatriculas','#pslFonte','','', 'Consultar');        
        });
        $("#ptxAssentos").mask('##0', {reverse: true});
        $("#ptxPmd").mask('######0', {reverse: true});

        // Adequações para o cadastro        
        await cdCarregarMatriculas('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txMatricula").focus();
        $("#txAssentos").mask('##0', {reverse: true});
        $("#txPmd").mask('######0', {reverse: true});
    });
</script>