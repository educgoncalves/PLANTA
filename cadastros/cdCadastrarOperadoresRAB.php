<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../cadastros/cdFuncoes.php");
require_once("../modais/mdModais.php");
verificarExecucao();

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
$operadorRAB = carregarGets('operadorRAB',carregarPosts('operadorRAB'));
$parametros = array('evento'=>$evento);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $operador = carregarPosts('operador');
    $nome = carregarPosts('nome');
    $iata = carregarPosts('iata');
    $icao = carregarPosts('icao');
    $grupo = carregarPosts('grupo');
    $cpfCnpj = carregarPosts('cpfCnpj');
    $matriz = carregarPosts('matriz');
    $situacao = carregarPosts('situacao');
    $cobranca = carregarPosts('cobranca');
    $txCobranca = carregarPosts('txCobranca');
    $txMatriz = carregarPosts('txMatriz');
    $txTodosGrupo = carregarPosts('txTodosGrupo');
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
    $erros = camposPreenchidos(['cpfCnpj','operador','nome','situacao']);
    ($iata != "" && ($_erros = siglaProprietarioDuplicada('IATA', $id, $iata)) != "" ? $erros[] = $_erros : "");
    ($icao != "" && ($_erros = siglaProprietarioDuplicada('ICAO', $id, $icao)) != "" ? $erros[] = $_erros : "");
    if (!$erros) {
        try {
            $conexao = conexao();
            $matriz = (isNullOrEmpty($matriz) ? 'null' : $matriz);
            if ($id != "") {
                $comando = "UPDATE planta_operadores SET operador='".$operador."',nome= '".$nome."',iata='".$iata."',icao='".$icao.
                            "',grupo='".$grupo."',cpfCnpj='".$cpfCnpj."',idMatriz=".$matriz.",idCobranca=".$cobranca.
                            ",situacao='".$situacao."',fonte='".$siglaAeroporto."',origem='MNL',cadastro=UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO planta_operadores (operador,nome,iata,icao,grupo,cpfCnpj,idMatriz,idCobranca,situacao,origem,fonte,cadastro) VALUES ('".
                            $operador."','".$nome."','".$iata."','".$icao."','".$grupo."','".$cpfCnpj.
                            "',".$matriz.",".$cobranca.",'".$situacao."','MNL','".$siglaAeroporto."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("planta_operadores", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaSite'], 
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
        $comando = selectDB("OperadoresRAB"," AND op.id = ".$id,"");
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $operador = $dados['operador'];
                $nome = $dados['nome'];
                $matriz = $dados['idMatriz'];
                $iata = $dados['iata'];
                $icao = $dados['icao'];
                $grupo = $dados['grupo'];
                $cpfCnpj = $dados['cpfCnpj'];
                $situacao = $dados['situacao'];
                $cobranca = $dados['idCobranca'];
                $txCobranca = $dados['descCobranca'];
                $txMatriz = $dados['matrizCompleta'];
                $txTodosGrupo = $dados['descGrupo'];
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
        $comando = "DELETE FROM planta_operadores WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("planta_operadores", "Exclusão", $_SESSION['plantaSite'], $_SESSION['plantaUsuario'], $id, $comando);   
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
    $operador = null;
    $nome = null;
    $matriz = null;
    $iata = null;
    $icao = null;
    $grupo = null;
    $cpfCnpj = null;
    $situacao = null;
    $cobranca = carregarPosts('cobranca');
    $txCobranca = carregarPosts('txCobranca');
    $txMatriz = null;
    $txTodosGrupo = null;
    $txTodosSituacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_cdCOR_ordenacao','op.operador,op.cpfCnpj');
metaTagsBootstrap('');
$titulo = "Operadores Aéreos - RAB";
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
                
                <input type="hidden" id="hdEvento" name="evento" <?="value=\"{$evento}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdOperadorRAB" name="operadorRAB" <?="value=\"{$operadorRAB}\"";?>/>
                
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">  
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label for="txCpfCnpj">CPF/CNPJ</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txCpfCnpj" name="cpfCnpj"
                                onfocus="javascript: retirarCpfCnpj(this);" onblur="javascript: formatarCpfCnpj(this);" maxlength="14"
                                    <?php echo (!isNullOrEmpty($cpfCnpj)) ? "value=\"{$cpfCnpj}\"" : "";?>/>                                
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="txOperador">Nome curto</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txOperador" name="operador" maxlength="30"
                                    <?php echo (!isNullOrEmpty($operador)) ? "value=\"{$operador}\"" : "";?>/>
                            </div>
                            <div class="col-md-8">
                                <label for="txNome">Nome completo</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txNome" name="nome"
                                    <?php echo (!isNullOrEmpty($nome)) ? "value=\"{$nome}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">                            
                            <div class="col-md-1">
                                <label for="txIcao">ICAO</label>
                                <input type="text" class="form-control caixaAlta cpoLimpar input-lg" id="txIcao" name="icao"
                                    <?php echo (!isNullOrEmpty($icao)) ? "value=\"{$icao}\"" : "";?>/>
                            </div>                             
                            <div class="col-md-1">
                                <label for="txIata">IATA</label>
                                <input type="text" class="form-control caixaAlta cpoLimpar input-lg" id="txIata" name="iata"
                                    <?php echo (!isNullOrEmpty($iata)) ? "value=\"{$iata}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="idTodosGrupo">Grupo</label>
                                <input type="text" class="form-select cpoLimpar input-lg" id="txTodosGrupo" placeholder="Selecionar" name="txTodosGrupo"
                                    <?php echo (!isNullOrEmpty($txTodosGrupo)) ? "value=\"{$txTodosGrupo}\"" : "";?>
                                    onfocus="iniciarPesquisa('TodosGrupo',this.value)" 
                                    oninput="executarPesquisa('TodosGrupo',this.value)" 
                                    onblur="finalizarPesquisa('TodosGrupo')"        
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimpar cpoObrigatorio" id="idTodosGrupo" name="grupo" <?="value=\"{$grupo}\"";?>/>                                        
                                <span id="spantxTodosGrupo"></span>  
                            </div> 
                            <div class="col-md-4">
                                <label for="idMatriz">Matriz</label>
                                <input type="text" class="form-select caixaAlta cpoLimpar input-lg" id="txMatriz" placeholder="Selecionar" name="txMatriz"
                                    <?php echo (!isNullOrEmpty($txMatriz)) ? "value=\"{$txMatriz}\"" : "";?>
                                    onfocus="iniciarPesquisa('Matriz',this.value)" 
                                    oninput="executarPesquisa('Matriz',this.value)" 
                                    onblur="finalizarPesquisa('Matriz')"                                      
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimpar" id="idMatriz" name="matriz" <?="value=\"{$matriz}\"";?>/>                                        
                                <span id="spantxMatriz"></span>  
                            </div>                 
                            <div class="col-md-4">
                                <label for="idCobranca">Cobrança</label>
                                <input type="text" class="form-select caixaAlta cpoLimpar input-lg" id="txCobranca" placeholder="Selecionar" name="txCobranca"
                                    <?php echo (!isNullOrEmpty($txCobranca)) ? "value=\"{$txCobranca}\"" : "";?>
                                    onfocus="iniciarPesquisa('Cobranca',this.value)" 
                                    oninput="executarPesquisa('Cobranca',this.value)" 
                                    onblur="finalizarPesquisa('Cobranca')"                                      
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimpar" id="idCobranca" name="cobranca" <?="value=\"{$cobranca}\"";?>/>                                        
                                <span id="spantxCobranca"></span>  
                            </div>  
                        </div>
                        <div class="row mt-2">                            
                            <div class="col-md-2">
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
<!-- Modal VISUALIZAR -->
<!-- *************************************************** -->
<?php modalVisualizar(); ?>
<!-- *************************************************** -->

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
                        <label for="ptxCpfCnpj">CPF/CNPJ</label>
                        <input type="text" class="form-control cpoCookie caixaAlta cpoLimpar input-lg" id="ptxCpfCnpj" maxlength="14"/>  
                    </div>
                    <div class="col-md-6">
                        <label for="ptxOperador">Nome curto</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxOperador"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label for="ptxNome">Nome completo</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNome"/>
                    </div>
                <div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label for="ptxIcao">ICAO</label>
                        <input type="text" class="form-control cpoCookie caixaAlta cpoLimpar input-lg" id="ptxIcao"/>
                    </div>    
                    <div class="col-md-4">
                        <label for="ptxIata">IATA</label>
                        <input type="text" class="form-control cpoCookie caixaAlta cpoLimpar input-lg" id="ptxIata"/>
                    </div>      
                    <div class="col-md-4">
                        <label for="pslGrupo">Grupo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslGrupo">
                        </select> 
                    </div>                                        

                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxMatriz">Matriz</label>
                        <input type="text" class="form-control cpoCookie caixaAlta cpoLimpar input-lg" id="ptxMatriz"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxCobranca">Cobrança</label>
                        <input type="text" class="form-control cpoCookie caixaAlta cpoLimpar input-lg" id="ptxCobranca"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="pslFonte">Fonte</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslFonte" name="fonte">
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
                            <option <?php echo ($ordenacao == 'op.operador,op.cpfCnpj') ? 'selected' : '';?> value='op.operador,op.cpfCnpj'>Nome curto</option>
                            <option <?php echo ($ordenacao == 'op.nome,op.operador,op.cpfCnpj') ? 'selected' : '';?> value='op.nome,op.operador,op.cpfCnpj'>Nome completo</option>
                            <option <?php echo ($ordenacao == 'op.cpfCnpj,op.operador') ? 'selected' : '';?> value='op.cpfCnpj,op.operador'>CPF/CNPJ</option>
                        </select> 
                    </div>
                </div>
                <br>
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
            $("#txCpfCnpj").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
        
        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxOperador":
                            filtro += " AND op.operador LIKE '%"+$("#ptxOperador").val()+"%'";
                            descricaoFiltro += " <br>Nome curto : "+$("#ptxOperador").val();
                        break;
                        case "ptxNome":
                            filtro += " AND op.nome LIKE '%"+$("#ptxNome").val()+"%'";
                            descricaoFiltro += " <br>Nome completo : "+$("#ptxNome").val();
                        break;
                        case "ptxIata":
                            filtro += " AND op.iata LIKE '%"+$("#ptxIata").val()+"%'";
                            descricaoFiltro += " <br>IATA : "+$("#ptxIata").val();
                        break;
                        case "ptxIcao":
                            filtro += " AND op.icao LIKE '%"+$("#ptxIcao").val()+"%'";
                            descricaoFiltro += " <br>ICAO : "+$("#ptxIcao").val();
                        break;
                        case "pslGrupo":
                            filtro += " AND op.grupo = '"+$("#pslGrupo").val()+"'";
                            descricaoFiltro += " <br>Grupo : "+$("#pslGrupo :selected").text();
                        break;
                        case "ptxMatriz":
                            filtro += " AND CONCAT(mz.icao,' - ',mz.operador) LIKE '%"+$("#ptxMatriz").val()+"%'";
                            descricaoFiltro += " <br>Matriz : "+$("#ptxMatriz").val();
                        break;
                        case "ptxCobranca":
                            filtro += " AND CONCAT(opc.cpfCnpj,' - ',opc.operador) LIKE '%"+$("#ptxCobranca").val()+"%'";
                            descricaoFiltro += " <br>Cobrança : "+$("#ptxCobranca").val();
                        break;
                        case "ptxCpfCnpj":
                            filtro += " AND op.cpfCnpj LIKE '%"+$("#ptxCpfCnpj").val()+"%'";
                            descricaoFiltro += " <br>CPF/CNPJ : "+$("#ptxCpfCnpj").val();
                        break;
                        case "pslFonte":
                            filtro += " AND CONCAT(op.fonte,' - ',dm3.descricao) = '"+$("#pslFonte").val()+"'";
                            descricaoFiltro += " <br>Fonte : "+$("#pslFonte :selected").text();
                        break;                         
                        case "pslSituacao":
                            filtro += " AND op.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCOR_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCOR_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCOR_descricao', descricaoFiltro);

            await cdCarregarOperadoresRAB('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txCpfCnpj").focus();
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
            $("#txCpfCnpj").focus();
        });

        // Visualizar informações complementares
        switch ($('#hdEvento').val()) {
            case "matriculas":
                await cdVisualizarMatriculas(" AND mt.idOperador = "+$('#hdId').val());
                $('#botaoVisualizar').trigger('click');
            break;
            case "cobranca":
                await cdVisualizarOperadoresCobranca(" AND opc.id = "+$('#hdId').val(),$('#hdOperadorRAB').val());
                $('#botaoVisualizar').trigger('click');
            break;
        }

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCOR_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_cdCOR_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCOR_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxCpfCnpj").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodos('TodosGrupo','#pslGrupo','','','Consultar');
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodas('FonteOperadores','#pslFonte','','', 'Consulta'); 
        });
        $("#ptxIata").mask('YY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
        $("#ptxIcao").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
       
        // Adequações para o cadastro     
        await cdCarregarOperadoresRAB('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txCpfCnpj").focus();
        $("#txIata").mask('YY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
        $("#txIcao").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
    });
</script>