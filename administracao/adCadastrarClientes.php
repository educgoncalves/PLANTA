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
    $aeroporto = carregarPosts('aeroporto');
    $sistema = carregarPosts('sistema');
    $conexoes = carregarPosts('conexoes');
    $debug = carregarPosts('debug');
    $regPorPagina = carregarPosts('regPorPagina');
    $tmpIsencao = carregarPosts('tmpIsencao');
    $tmpReserva = carregarPosts('tmpReserva');
    $tmpRetorno = carregarPosts('tmpRetorno');
    $tmpTaxiG1 = carregarPosts('tmpTaxiG1');
    $tmpTaxiG2 = carregarPosts('tmpTaxiG2');
    $tmpRefreshPagina = carregarPosts('tmpRefreshPagina');
    $tmpRefreshTela = carregarPosts('tmpRefreshTela');
    $utc = carregarPosts('utc');
    $hrAbertura = carregarPosts('hrAbertura');
    $hrFechamento = carregarPosts('hrFechamento');
    $categoria = carregarPosts('categoria');
    $tipoOperador = carregarPosts('tipoOperador');
    $avsec = carregarPosts('avsec');
    $celular = carregarPosts('celular');
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
    $erros = camposPreenchidos(['aeroporto','sistema','conexoes','debug','regPorPagina','utc','categoria','tipoOperador','avsec',
                                'tmpTaxiG1','tmpTaxiG2']);
    if (!$erros) {
        try {
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE gear_clientes SET idAeroporto = ".$aeroporto.",sistema = '".$sistema."',conexoes = ".$conexoes.
                            ",tmpIsencao = ".$tmpIsencao.",tmpReserva = ".$tmpReserva.",tmpRetorno = ".$tmpRetorno.",tmpTaxiG1 = ".$tmpTaxiG1.
                            ",tmpTaxiG2 = ".$tmpTaxiG2.",tmpRefreshPagina = ".$tmpRefreshPagina.",tmpRefreshTela = ".$tmpRefreshTela.
                            ",debug = '".$debug."',regPorPagina = ".$regPorPagina.",utc = ".$utc.",categoria = '".
                            $categoria."',tipoOperador = '".$tipoOperador."' ,avsec = '".$avsec."',hrAbertura = '".$hrAbertura.
                            "', hrFechamento = '".$hrFechamento."',celular = '".$celular."',situacao = '".$situacao.
                            "',cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO gear_clientes (idAeroporto,sistema,conexoes,tmpIsencao,tmpReserva,tmpRetorno,tmpTaxiG1,tmpTaxiG2,".
                            "tmpRefreshPagina,tmpRefreshTela,debug,regPorPagina,utc,hrAbertura,hrFechamento,categoria,tipoOperador,avsec,". 
                            "celular,situacao,cadastro) VALUES (".
                            $aeroporto.",'".$sistema."',".$conexoes.",".$tmpIsencao.",".$tmpReserva.",".$tmpRetorno.",".$tmpTaxiG1.",".$tmpTaxiG2.
                            ",".$tmpRefreshPagina.",".$tmpRefreshTela.",'".$debug."',".$regPorPagina.",".$utc.",'".$hrAbertura."','".$hrFechamento.
                            "','".$categoria."','".$tipoOperador."','".$avsec."','".$celular."','".$situacao."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando);               
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_clientes", ($id != "" ? "Alteração" : "Inclusão"), $siglaAeroporto, 
                                 $usuario, ($id != "" ? $id  : $conexao->lastInsertId()), $comando);
                    montarMensagem("success",array("Registro ".($id != "" ? "alterado" : "incluído")." com sucesso!"));
                    //
                    // Atualiza váriaveis de sessão se alterou no próprio aeroporto
                    //
                    if ($_SESSION['plantaIDAeroporto'] == $aeroporto) {
                        $_SESSION['plantaDebug'] = $debug;
                        $_SESSION['plantaRegPorPagina'] = $regPorPagina;
                        $_SESSION['plantaUTCAeroporto'] = $utc;
                    }
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
    $dados = ['tabela'=>'Clientes','filtro'=>" AND cl.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $aeroporto = $dados['idAeroporto'];
            $sistema = $dados['sistema'];
            $conexoes = $dados['conexoes'];
            $debug = $dados['debug'];
            $regPorPagina = $dados['regPorPagina'];
            $tmpIsencao = $dados['tmpIsencao'];
            $tmpReserva = $dados['tmpReserva'];
            $tmpRetorno = $dados['tmpRetorno'];
            $tmpTaxiG1 = $dados['tmpTaxiG1'];
            $tmpTaxiG2 = $dados['tmpTaxiG2'];
            $tmpRefreshPagina = $dados['tmpRefreshPagina'];
            $tmpRefreshTela = $dados['tmpRefreshTela'];
            $utc = $dados['utc'];
            $hrAbertura = $dados['horaAbertura'];
            $hrFechamento = $dados['horaFechamento'];
            $categoria = $dados['categoria']; 
            $tipoOperador = $dados['tipoOperador'];
            $avsec = $dados['avsec'];  
            $celular = $dados['celular'];                                             
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
        $comando = "DELETE FROM gear_clientes WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_clientes", "Exclusão", $siglaAeroporto, $usuario, $id, $comando);            
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

// Copiando as informações
if ($evento == "copiar" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "INSERT INTO gear_clientes (idAeroporto, sistema, conexoes, tmpIsencao, tmpReserva, tmpRetorno, tmpTaxiG1, tmpTaxiG2, ".
                    "tmpRefreshPagina, tmpRefreshTela, debug, regPorPagina, utc, hrAbertura, hrFechamento, categoria, tipoOperador, avsec, ". 
                    "celular, situacao, cadastro) ".
                    "SELECT idAeroporto, sistema, conexoes, tmpIsencao, tmpReserva, tmpRetorno, tmpTaxiG1, tmpTaxiG2, ". 
                    "tmpRefreshPagina, tmpRefreshTela, debug, regPorPagina, utc, hrAbertura, hrFechamento, categoria, tipoOperador, avsec, ". 
                    "celular, situacao, UTC_TIMESTAMP() FROM gear_clientes WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLogAPI("gear_clientes", "Copiar", $siglaAeroporto, $usuario, $id, $comando);   
            $_retorno = ['status' => 'OK', 'msg'=> "Registro copiado com sucesso!"];
        } else {
            throw new PDOException("Não foi possível copiar este registro!");
        } 
    } catch (PDOException $e) {
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $aeroporto = null;
    $sistema = null;
    $conexoes = null;
    $debug = null;
    $regPorPagina = null;
    $tmpIsencao = null;
    $tmpReserva = null;
    $tmpRetorno = null;
    $tmpTaxiG1 = null;
    $tmpTaxiG2 = null;
    $tmpRefreshPagina = null;
    $tmpRefreshTela = null;
    $utc = null;
    $hrAbertura = '00:00';
    $hrFechamento = '23:59';
    $categoria = null;
    $tipoOperador = null;
    $avsec = null; 
    $celular = null;          
    $situacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adCC_ordenacao','ae.icao,cl.sistema');     
metaTagsBootstrap('');
$titulo = "Clientes";
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
                <input type="hidden" class="cpoLimpar" id="hdEvento" <?="value=\"{$evento}\"";?>/>

                <input type="hidden" class="cpoLimpar" id="hdSistema" <?="value=\"{$sistema}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdDebug" <?="value=\"{$debug}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdCategoria" <?="value=\"{$categoria}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdTipoOperador" <?="value=\"{$tipoOperador}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdAvsec" <?="value=\"{$avsec}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>    

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-9">
                                <label for="slAeroporto">Aeroporto</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slAeroporto" name="aeroporto">
                                </select>
                            </div>  
                        </div>                                      
                        <div class="row mt-2">  
                            <div class="col-md-6">
                                <label for="txCelular">Celulares</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txCelular" name="celular" maxlength="60" 
                                    <?=(!isNullOrEmpty($celular)) ? "value=\"{$celular}\"" : "";?>/>
                            </div>    
                            <small class="text-primary">Celular completo com código do pais e DDD, no máximo 4 separados por vírgula, digitar somente números</small>
                        </div>                                      
                        <div class="row mt-2">     
                            <div class="col-md-3">
                                <label for="slSistema">Sistema</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slSistema" name="sistema">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="txConexoes">Qtd de Conexões</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txConexoes" name="conexoes" maxlength="3"
                                    <?php echo (!isNullOrEmpty($conexoes)) ? "value=\"{$conexoes}\"" : "";?>/>
                            </div>                                                                         
                            <div class="col-md-2">
                                <label for="txRegPorPagina">Registros por Página</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txRegPorPagina" name="regPorPagina" maxlength="3"
                                    <?php echo (!isNullOrEmpty($regPorPagina)) ? "value=\"{$regPorPagina}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="slDebug">Grava debug</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slDebug" name="debug">
                                </select> 
                            </div>                             
                        </div> 
                        <div class="row mt-4">
                            <div class="col-md-3 text-center"><strong>________ Tolerâncias (min) ________</strong></div>   
                            <!-- <div class="col-md-1"></div>   -->
                            <div class="col-md-5 text-center"><strong>__________________________ UTC __________________________</strong></div>
                            <!-- <div class="col-md-1"></div> -->
                            <div class="col-md-2"><strong>___ Taxiamento (min) ___</strong></div>   
                            <div class="col-md-2"><strong>____ Refresh  (seg) ____</strong></div>   
                        </div>  
                        <div class="row">    
                            <div class="col-md-1">
                                <label for="txTmpIsencao">Isenção</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txTmpIsencao" name="tmpIsencao" maxlength="3"
                                    <?php echo (!isNullOrEmpty($tmpIsencao)) ? "value=\"{$tmpIsencao}\"" : "";?>/>
                            </div>
                            <div class="col-md-1">
                                <label for="txTmpReserva">Reserva</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txTmpReserva" name="tmpReserva" maxlength="3"
                                    <?php echo (!isNullOrEmpty($tmpReserva)) ? "value=\"{$tmpReserva}\"" : "";?>/>
                            </div>  
                            <div class="col-md-1">
                                <label for="txTmpRetorno">Retorno</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txTmpRetorno" name="tmpRetorno" maxlength="3"
                                    <?php echo (!isNullOrEmpty($tmpRetorno)) ? "value=\"{$tmpRetorno}\"" : "";?>/>
                            </div>  
                            <!-- <div class="col-md-1"></div> -->
                            <div class="col-md-1">
                                <label for="txUTC">Diferença</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txUTC" name="utc" maxlength="3"
                                    <?php echo (!isNullOrEmpty($utc)) ? "value=\"{$utc}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txHrAbertura">Abertura</label>
                                <input type="time" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txHrAbertura" name="hrAbertura" maxlength="5"
                                    <?php echo (!isNullOrEmpty($hrAbertura)) ? "value=\"{$hrAbertura}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txHrFechamento">Fechamento</label>
                                <input type="time" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txHrFechamento" name="hrFechamento" maxlength="5"
                                    <?php echo (!isNullOrEmpty($hrFechamento)) ? "value=\"{$hrFechamento}\"" : "";?>/>
                            </div>
                            <!-- <div class="col-md-1"></div> -->
                            <div class="col-md-1">
                                <label for="txTmpTaxiG1">Grupo I</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txTmpTaxiG1" name="tmpTaxiG1" maxlength="2"
                                    <?php echo (!isNullOrEmpty($tmpTaxiG1)) ? "value=\"{$tmpTaxiG1}\"" : "";?>/>
                            </div>
                            <div class="col-md-1">
                                <label for="txTmpTaxiG2">Grupo II</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txTmpTaxiG2" name="tmpTaxiG2" maxlength="2"
                                    <?php echo (!isNullOrEmpty($tmpTaxiG2)) ? "value=\"{$tmpTaxiG2}\"" : "";?>/>
                            </div>
                            <div class="col-md-1">
                                <label for="txTmpRefreshPagina">Pagina</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txTmpTaxiG1" name="tmpRefreshPagina" maxlength="3"
                                    <?php echo (!isNullOrEmpty($tmpRefreshPagina)) ? "value=\"{$tmpRefreshPagina}\"" : "";?>/>
                            </div>
                            <div class="col-md-1">
                                <label for="txTmpRefreshTela">Tela</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txTmpRefreshTela" name="tmpRefreshTela" maxlength="3"
                                    <?php echo (!isNullOrEmpty($tmpRefreshTela)) ? "value=\"{$tmpRefreshTela}\"" : "";?>/>
                            </div>
                        </div>                                      
                        <div class="row mt-4">                              
                            <div class="col-md-6">
                                <label for="slTipoOperador">Tipo Operador</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slTipoOperador" name="tipoOperador">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="slCategoria">Categoria</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slCategoria" name="categoria">
                                </select>
                            </div>                            
                        </div>                                      
                        <div class="row mt-2">                              
                            <div class="col-md-6">
                                <label for="slAvsec">AVSEC</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slAvsec" name="avsec">
                                </select>
                            </div>           
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
                    <div class="col-md-8">
                        <label for="pslAeroporto">Aeroporto</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslAeroporto">
                        </select>
                    </div>      
                </div>    
                <div class="row mt-2">                    
                    <div class="col-md-8">
                        <label for="ptxCelular">Celular</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxCelular"/>
                    </div>
                </div>                                  
                <div class="row mt-2">     
                    <div class="col-md-8">
                        <label for="pslSistema">Sistema</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSistema">
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="pslCategoria">Categoria</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslCategoria">
                        </select>
                    </div>                    
                </div>                                      
                <div class="row mt-2">   
                    <div class="col-md-12">
                        <label for="pslTipoOperador">Tipo Operador</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslTipoOperador">
                        </select>
                    </div>                      
                </div>                                      
                <div class="row mt-2">  
                    <div class="col-md-12">
                        <label for="pslAvsec">AVSEC</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslAvsec">
                        </select>
                    </div>                        
                </div>                                      
                <div class="row mt-2">     
                    <div class="col-md-8">
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
                            <option <?php echo ($ordenacao == 'ae.icao,cl.sistema') ? 'selected' : '';?> value='ae.icao,cl.sistema'>Aeroporto</option>
                            <option <?php echo ($ordenacao == 'cl.sistema,ae.icao') ? 'selected' : '';?> value='cl.sistema,ae.icao'>Sistema</option>
                            <option <?php echo ($ordenacao == 'cl.categoria,ae.icao') ? 'selected' : '';?> value='cl.categoria,ae.icao'>Categoria</option>                            
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
            $("#slAeroporto").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pslAeroporto":
                            filtro += " AND cl.idAeroporto = "+$("#pslAeroporto").val();
                            descricaoFiltro += " <br>Aeroporto : "+$("#pslAeroporto :selected").text();
                            break;
                        case "ptxCelular":
                            filtro += " AND cl.celular LIKE '%"+$("#ptxCelular").val()+"%'";
                            descricaoFiltro += " <br>Celular : "+$("#ptxCelular").val();
                            break; 
                        case "pslSistema":
                            filtro += " AND cl.sistema = '"+$("#pslSistema").val()+"'";
                            descricaoFiltro += " <br>Sistema : "+$("#pslSistema :selected").text();
                            break;         
                        case "pslCategoria":
                            filtro += " AND cl.categoria = '"+$("#pslCategoria").val()+"'";
                            descricaoFiltro += " <br>Categoria : "+$("#pslCategoria :selected").text();
                        break; 
                        case "pslTipoOperador":
                            filtro += " AND cl.tipoOperador = '"+$("#pslTipoOperador").val()+"'";
                            descricaoFiltro += " <br>Tipo Operador : "+$("#pslTipoOperador :selected").text();
                        break; 
                        case "pslAvsec":
                            filtro += " AND cl.avsec = '"+$("#pslAvsec").val()+"'";
                            descricaoFiltro += " <br>AVSEC : "+$("#pslAvsec :selected").text();
                        break;                                                                                                
                        case "pslSituacao":
                            filtro += " AND cl.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_adCC_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCC_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCC_descricao', descricaoFiltro);
                        
            await adCarregarClientes('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slAeroporto").focus();
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
            $("#slAeroporto").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adCC_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adCC_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adCC_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#pslAeroporto").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodos('TodosSistema','#pslSistema','','','Consultar');
            await suCarregarSelectTodos('AeroportosClientes','#pslAeroporto','X','','Consultar');
            await suCarregarSelectTodas('ClientesCategoria','#pslCategoria', '','','Consultar');
            await suCarregarSelectTodos('ClientesTipoOperador','#pslTipoOperador', '','','Consultar');
            await suCarregarSelectTodos('ClientesAvsec','#pslAvsec', '','','Consultar');
        });
        $("#ptxCelular").mask('Y', {'translation': {Y: {pattern: /[0-9,]/,recursive: true},}});

        // Adequações para o cadastro         
        await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await suCarregarSelectTodos('TodosSimNao','#slDebug', $('#hdDebug').val(), '','Cadastrar');
        await suCarregarSelectTodos('TodosSistema','#slSistema', $('#hdSistema').val(), '','Cadastrar');
        await suCarregarSelectTodos('Aeroportos','#slAeroporto', $('#hdAeroporto').val(), '','Cadastrar');
        await suCarregarSelectTodas('ClientesCategoria','#slCategoria', $('#hdCategoria').val(),'','Cadastrar');
        await suCarregarSelectTodos('ClientesTipoOperador','#slTipoOperador', $('#hdTipoOperador').val(),'','Cadastrar');
        await suCarregarSelectTodos('ClientesAvsec','#slAvsec', $('#hdAvsec').val(),'','Cadastrar');
        await adCarregarClientes('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));        
        $("#slAeroporto").focus();

        $("#txConexoes").mask('999', {'translation': {9: {pattern: /[0-9]/} } });         
        $("#txRegPorPagina").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txTmpIsencao").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txTmpReserva").mask('999', {'translation': {9: {pattern: /[0-9]/} } });
        $("#txTmpTaxiG1").mask('99', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txTmpTaxiG2").mask('99', {'translation': {9: {pattern: /[0-9]/} } });
        $("#txTmpRefreshPagina").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txTmpRefreshTela").mask('999', {'translation': {9: {pattern: /[0-9]/} } });
        $("#txCelular").mask('Y', {'translation': {Y: {pattern: /[0-9,\s]/,recursive: true},}});

        // Desabilitar os que não podem ser alterados
        //$("#slAeroporto").attr("readonly", ($("#hdEvento").val()=="recuperar"));
        //$("#slSistema").attr("readonly", ($("#hdEvento").val()=="recuperar"));
    });
</script>