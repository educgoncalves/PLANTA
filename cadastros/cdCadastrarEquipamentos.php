<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
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
$parametros = array('evento'=>$evento);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $equipamento = carregarPosts('equipamento');
    $modelo = carregarPosts('modelo');
    $iataEquipamento = carregarPosts('iataEquipamento');
    $icaoCategoria = carregarPosts('icaoCategoria');
    $tipoMotor = carregarPosts('tipoMotor');
    $fabricante = carregarPosts('fabricante');
    $envergadura = carregarPosts('envergadura');
    $comprimento = carregarPosts('comprimento');
    $assentos = carregarPosts('assentos');
    $asa = carregarPosts('asa');
    $situacao = carregarPosts('situacao');
    $txEquipamentosAsa = carregarPosts('txEquipamentosAsa');
    $txTodosSituacao = carregarPosts('txTodosSituacao');
    $txEquipamentosTipoMotor = carregarPosts('txEquipamentosTipoMotor');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
        
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
	// Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['equipamento','modelo','fabricante','envergadura','comprimento','assentos','tipoMotor','asa','situacao']); 
    if (!$erros) {
        try {
            $conexao = conexao();
            $vlrEnvergadura = mudarDecimalMysql($envergadura);
            $vlrComprimento = mudarDecimalMysql($comprimento);
            $assentos = (!empty($assentos) ? $assentos : 0);
            if ($id != "") {
                $comando = "UPDATE planta_equipamentos SET equipamento='".$equipamento."',modelo='".$modelo."',iataEquipamento='".$iataEquipamento.
                            "',icaoCategoria='".$icaoCategoria."',tipoMotor='".$tipoMotor."',fabricante='".$fabricante."',envergadura=".$vlrEnvergadura.
                            ",comprimento=".$vlrComprimento.",assentos=".$assentos.",asa='".$asa."',situacao='".$situacao."',fonte='".$siglaAeroporto.
                            "',origem='MNL',cadastro=UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO planta_equipamentos(equipamento,modelo,iataEquipamento,icaoCategoria,tipoMotor,fabricante,envergadura,comprimento,".
                            "assentos,asa,situacao,origem,fonte,cadastro) VALUES ('".$equipamento."','".$modelo."','".
                            $iataEquipamento."','".$icaoCategoria."','".$tipoMotor."','".$fabricante."',".$vlrEnvergadura.",".$vlrComprimento.",".
                            $assentos.",'".$asa."','".$situacao."','MNL','".$siglaAeroporto."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando);         
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("planta_equipamentos", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaSite'], 
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
            montarMensagem("danger",array(traduzPDO($e->getMessage())));
        }
    } else {
        montarMensagem("danger", $erros);
    } 
}

// Recuperando as informações
if ($evento == "recuperar" && $id != "") {
    try {
        $conexao = conexao();
        $comando = selectDB("Equipamentos"," AND eq.id = ".$id,"");
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $equipamento = $dados['equipamento'];
                $modelo = $dados['modelo'];
                $iataEquipamento = $dados['iataEquipamento'];
                $icaoCategoria = $dados['icaoCategoria'];
                $tipoMotor = $dados['tipoMotor'];
                $fabricante = $dados['fabricante'];
                $envergadura = $dados['envergadura'];
                $comprimento = $dados['comprimento'];
                $assentos = $dados['assentos'];
                $asa = $dados['asa'];
                $situacao = $dados['situacao'];
                $txEquipamentosAsa = $dados['descAsa'];
                $txTodosSituacao = $dados['descSituacao'];
                $txEquipamentosTipoMotor = $dados['descTipoMotor'];
            }
            $limparCampos = false;
        } else {
            throw new PDOException("Não foi possível recuperar este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())));
    }
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "DELETE FROM planta_equipamentos WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("planta_equipamentos", "Exclusão", $_SESSION['plantaSite'], $_SESSION['plantaUsuario'], $id, $comando);   
            montarMensagem("success",array("Registro excluído com sucesso!"));
            $id = null;
            $limparCampos = true;
    } else {
        throw new PDOException("Não foi possível excluir este registro!");
    } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())));
    }
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $equipamento = null;
    $modelo = null;
    $iataEquipamento = null;
    $icaoCategoria = null;
    $tipoMotor = null;
    $fabricante = null;
    $envergadura = null;
    $comprimento = null;
    $assentos = null;
    $asa = null;
    $situacao = null;
    $txEquipamentosAsa = null;
    $txTodosSituacao = null;
    $txEquipamentosTipoMotor = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_cdCE_ordenacao','eq.equipamento,eq.modelo,eq.fabricante');
metaTagsBootstrap('');
$titulo = "Equipamentos";
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
                            <div class="col-md-2">
                                <label for="txEquipamento">ICAO</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txEquipamento" name="equipamento"
                                    <?php echo (!isNullOrEmpty($equipamento)) ? "value=\"{$equipamento}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txModelo">Modelo</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txModelo" name="modelo"
                                    <?php echo (!isNullOrEmpty($modelo)) ? "value=\"{$modelo}\"" : "";?>/>
                            </div>
                            <div class="col-md-4">
                                <label for="txFabricante">Fabricante</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txFabricante" name="fabricante" maxlength="30"
                                    <?php echo (!isNullOrEmpty($fabricante)) ? "value=\"{$fabricante}\"" : "";?>/>
                            </div> 
                        </div>
                        <div class="row mt-2">                            
                            <div class="col-md-2">
                                <label for="txIataEquipamento">IATA</label>
                                <input type="text" class="form-control cpoLimpar caixaAlta input-lg" id="txIataEquipamento" name="iataEquipamento"
                                    <?php echo (!isNullOrEmpty($iataEquipamento)) ? "value=\"{$iataEquipamento}\"" : "";?>/>
                            </div> 
                            <div class="col-md-2">
                                <label for="txIcaoCategoria">Categoria</label>
                                <input type="text" class="form-control cpoLimpar caixaAlta input-lg" id="txIcaoCategoria" name="icaoCategoria"
                                    <?php echo (!isNullOrEmpty($icaoCategoria)) ? "value=\"{$icaoCategoria}\"" : "";?>/>
                            </div>      
                            <div class="col-md-2">
                                <label for="txEnvergadura">Envergadura</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txEnvergadura" name="envergadura" maxlength="6"
                                    <?php echo (!isNullOrEmpty($envergadura)) ? "value=\"{$envergadura}\"" : "";?>/>
                            </div>                            
                            <div class="col-md-2">
                                <label for="txComprimento">Comprimento</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txComprimento" name="comprimento" maxlength="6"
                                    <?php echo (!isNullOrEmpty($comprimento)) ? "value=\"{$comprimento}\"" : "";?>/>
                            </div>                            
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="txAssentos">Assentos</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txAssentos" name="assentos" maxlength="3"
                                    <?php echo (!isNullOrEmpty($assentos)) ? "value=\"{$assentos}\"" : "";?>/>
                            </div>                              
                            <div class="col-md-3">
                                <label for="idEquipamentosTipoMotor">Tipo Motor</label>
                                <input type="text" class="form-select cpoLimpar input-lg" id="txEquipamentosTipoMotor" placeholder="Selecionar" name="txEquipamentosTipoMotor"
                                    <?php echo (!isNullOrEmpty($txEquipamentosTipoMotor)) ? "value=\"{$txEquipamentosTipoMotor}\"" : "";?>
                                    onfocus="iniciarPesquisa('EquipamentosTipoMotor',this.value)" 
                                    oninput="executarPesquisa('EquipamentosTipoMotor',this.value)" 
                                    onblur="finalizarPesquisa('EquipamentosTipoMotor')"        
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimpar cpoObrigatorio" id="idEquipamentosTipoMotor" name="tipoMotor" <?="value=\"{$tipoMotor}\"";?>/>                                        
                                <span id="spantxEquipamentosTipoMotor"></span>                                
                            </div>
                            <div class="col-md-3">
                                <label for="idEquipamentosAsa">Asa</label>
                                <input type="text" class="form-select cpoLimpar input-lg" id="txEquipamentosAsa" placeholder="Selecionar" name="txEquipamentosAsa"
                                    <?php echo (!isNullOrEmpty($txEquipamentosAsa)) ? "value=\"{$txEquipamentosAsa}\"" : "";?>
                                    onfocus="iniciarPesquisa('EquipamentosAsa',this.value)" 
                                    oninput="executarPesquisa('EquipamentosAsa',this.value)" 
                                    onblur="finalizarPesquisa('EquipamentosAsa')"        
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimpar cpoObrigatorio" id="idEquipamentosAsa" name="asa" <?="value=\"{$asa}\"";?>/>                                        
                                <span id="spantxEquipamentosAsa"></span>                                 
                            </div>                            
                            <div class="col-md-3">
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
                <?php destacarTarefaEquipamentosANAC(); ?>  
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
                    <div class="col-md-4">
                        <label for="ptxEquipamento">ICAO</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxEquipamento"/>
                    </div>
                    <div class="col-md-4">
                        <label for="ptxModelo">Modelo</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxModelo"/>
                    </div>
                    <div class="col-md-4">
                        <label for="ptxFabricante">Fabricante</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxFabricante"/>
                    </div> 
                </div>
                <div class="row mt-2">                            
                    <div class="col-md-4">
                        <label for="ptxIataEquipamento">IATA</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxIataEquipamento"/>
                    </div> 
                    <div class="col-md-4">
                        <label for="ptxIcaoCategoria">Categoria</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxIcaoCategoria"/>
                    </div>  
                    <div class="col-md-4">
                        <label for="ptxEnvergadura">Envergadura</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEnvergadura" maxlength="6"/>
                    </div>  
                </div>
                <div class="row mt-2">                                              
                    <div class="col-md-4">
                        <label for="ptxComprimento">Comprimento</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxComprimento" maxlength="6"/>
                    </div> 
                    <div class="col-md-4">
                        <label for="ptxAssentos">Assentos</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxAssentos" maxlength="3"/>
                    </div>    
                    <div class="col-md-4">
                        <label for="pslAsa">Asa</label>
                        <div class="input-group">
                            <select class="form-select cpoCookie selCookie input-lg" id="pslAsa">
                            </select>
                        </div> 
                    </div>                                                       
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-4">
                        <label for="pslTipoMotor">Tipo Motor</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslTipoMotor">
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="pslFonte">Fonte</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslFonte" name="fonte">
                        </select> 
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
                            <option <?php echo ($ordenacao == 'eq.equipamento,eq.modelo,eq.fabricante') ? 'selected' : '';?> 
                                        value='eq.equipamento,eq.modelo,eq.fabricante'>Equipamento</option>
                            <option <?php echo ($ordenacao == 'eq.modelo,eq.fabricante,eq.equipamento') ? 'selected' : '';?> 
                                        value='eq.modelo,eq.fabricante,eq.equipamento'>Modelo</option>
                            <option <?php echo ($ordenacao == 'eq.fabricante,eq.equipamento,eq.modelo') ? 'selected' : '';?> 
                                        value='eq.fabricante,eq.equipamento,eq.modelo'>Fabricante</option>
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
            $("#txEquipamento").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });


        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxEquipamento":
                            filtro += " AND eq.equipamento LIKE '%"+$("#ptxEquipamento").val()+"%'";
                            descricaoFiltro += " <br>Equipamento : "+$("#ptxEquipamento").val();
                            break;
                        case "ptxIataEquipamento":
                            filtro += " AND eq.iataEquipamento = '"+$("#ptxIataEquipamento").val()+"'";
                            descricaoFiltro += " <br>IATA Equipamento : "+$("#ptxIataEquipamento").val();
                            break;
                        case "ptxModelo":
                            filtro += " AND eq.modelo LIKE '%"+$("#ptxModelo").val()+"%'";
                            descricaoFiltro += " <br>Modelo : "+$("#ptxModelo").val();
                            break;
                        case "ptxIcaoCategoria":
                            filtro += " AND eq.icaoCategoria = '"+$("#ptxIcaoCategoria").val()+"'";
                            descricaoFiltro += " <br>Categoria : "+$("#ptxIcaoCategoria").val();
                            break;
                        case "pslTipoMotor":
                            filtro += " AND eq.tipoMotor = '"+$("#pslTipoMotor").val()+"'";
                            descricaoFiltro += " <br>Tipo Motor : "+$("#pslTipoMotor :selected").text();
                            break;
                        case "pslAsa":
                            filtro += " AND eq.asa = '"+$("#pslAsa").val()+"'";
                            descricaoFiltro += " <br>Asa : "+$("#pslAsa :selected").text();
                            break;
                        case "ptxEnvergadura":
                            filtro += " AND eq.envergadura = "+mudarDecimalMysql($("#ptxEnvergadura").val());
                            descricaoFiltro += " <br>Envergadura : "+$("#ptxEnvergadura").val();
                            break;
                        case "ptxComprimento":
                            filtro += " AND eq.comprimento = "+mudarDecimalMysql($("#ptxComprimento").val());
                            descricaoFiltro += " <br>Comprimento : "+$("#ptxComprimento").val();
                            break;                    
                        case "txAssentos":
                            filtro += " AND eq.assentos = "+$("#ptxAssentos").val();
                            descricaoFiltro += " <br>Equipamento : "+$("#ptxAssentos").val();
                            break;                    
                        case "ptxFabricante":
                            filtro += " AND eq.fabricante LIKE '%"+$("#ptxFabricante").val()+"%'";
                            descricaoFiltro += " <br>Fabricante : "+$("#ptxFabricante").val();
                            break;     
                        case "pslFonte":
                            filtro += " AND CONCAT(eq.fonte,' - ',dm4.descricao) = '"+$("#pslFonte").val()+"'";
                            descricaoFiltro += " <br>Fonte : "+$("#pslFonte :selected").text();
                        break;                                                
                        case "pslSituacao":
                            filtro += " AND eq.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCE_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCE_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCE_descricao', descricaoFiltro);

            await cdCarregarEquipamentos('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txEquipamento").focus();
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
            $("#txEquipamento").focus();
        });
        
        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCE_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_cdCE_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCE_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxEquipamento").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodos('EquipamentosTipoMotor','#pslTipoMotor','','','Consultar');
            await suCarregarSelectTodas('EquipamentosAsa','#pslAsa','','','Consultar');
            await suCarregarSelectTodas('FonteEquipamentos','#pslFonte','','', 'Consulta');
        });
        $("#ptxEquipamento").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
        $("#ptxIataEquipamento").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
        $("#ptxIcaoCategoria").mask('YY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
        $("#ptxEnvergadura").mask('##0,00', {reverse: true});
        $("#ptxComprimento").mask('##0,00', {reverse: true});
        $("#ptxAssentos").mask('##0', {reverse: true});

        // Adequações para o cadastro
        await cdCarregarEquipamentos('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txEquipamento").focus();
        $("#txEquipamento").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
        $("#txIataEquipamento").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
        $("#txIcaoCategoria").mask('YY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
        $("#txEnvergadura").mask('##0,00', {reverse: true});
        $("#txComprimento").mask('##0,00', {reverse: true});
        $("#txAssentos").mask('000', {reverse: true});
    });
</script>