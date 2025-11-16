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
$operadorCOB = carregarGets('operadorCOB',carregarPosts('operadorCOB'));
$parametros = array('evento'=>$evento);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $operador = carregarPosts('operador');
    $nome = carregarPosts('nome');
    $cpfCnpj = carregarPosts('cpfCnpj');
    $endereco = carregarPosts('endereco');
    $complemento = carregarPosts('complemento');
    $bairro = carregarPosts('bairro');
    $municipio = carregarPosts('municipio');
    $cidade = carregarPosts('cidade');
    $estado = carregarPosts('estado');
    $cep = carregarPosts('cep');
    $contato = carregarPosts('contato');
    $email = carregarPosts('email');
    $telefone = carregarPosts('telefone');
    $situacao = carregarPosts('situacao');
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
    if (!$erros) {
        try {
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE planta_operadores_cobranca SET operador='".$operador."',nome='".$nome."',cpfCnpj='".$cpfCnpj.
                            "',endereco='".$endereco."',complemento='".$complemento."',bairro='".$bairro."',municipio='".$municipio.
                            "',cidade='".$cidade."',estado='".$estado."',cep='".$cep."',contato='".$contato."',email='".$email.
                            "',telefone='".$telefone."',situacao='".$situacao."',fonte='".$siglaAeroporto.
                            "',origem='MNL',cadastro=UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO planta_operadores_cobranca (operador,nome,cpfCnpj,endereco,complemento,bairro,municipio,cidade,
                            estado,cep,contato,email,telefone,situacao, origem, fonte, cadastro) VALUES ('".$operador."','".$nome."','".$cpfCnpj.
                            "','".$endereco."','".$complemento."','".$bairro."','".$municipio."','".$cidade."','".$estado."','".$cep.
                            "','".$contato."','".$email."','".$telefone."','".$situacao."','MNL','".$siglaAeroporto."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("planta_operadores_cobranca", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaSite'], 
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
        $comando = selectDB("OperadoresCobranca"," AND opc.id = ".$id,"");
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $operador = $dados['operador'];
                $nome = $dados['nome'];
                $cpfCnpj = $dados['cpfCnpj'];
                $endereco = $dados['endereco'];
                $complemento = $dados['complemento'];
                $bairro = $dados['bairro'];
                $municipio = $dados['municipio'];
                $cidade = $dados['cidade'];
                $estado = $dados['estado'];
                $cep = $dados['cep'];
                $contato = $dados['contato'];
                $email = $dados['email'];
                $telefone = $dados['telefone'];
                $situacao = $dados['situacao'];
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
        $comando = "DELETE FROM planta_operadores_cobranca WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("planta_operadores_cobranca", "Exclusão", $_SESSION['plantaSite'], $_SESSION['plantaUsuario'], $id, $comando);   
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
    $cpfCnpj = null;
    $endereco = null;
    $complemento = null;
    $bairro = null;
    $municipio = null;
    $cidade = null;
    $estado = null;
    $cep = null;
    $contato = null;
    $email = null;
    $telefone = null;
    $situacao = null;
    $txTodosSituacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_cdCOC_ordenacao','opc.operador,opc.cpfCnpj');
metaTagsBootstrap('');
$titulo = "Operadores Aéreos - Cobrança";
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
                
                <input type="hidden" id="hdEvento" <?="value=\"{$evento}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdOperadorCOB" <?="value=\"{$operadorCOB}\"";?>/>

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
                                    onfocus="javascript: retirarSimbolos(this);" onblur="javascript: formatarCpfCnpj(this);" maxlength="14"
                                    <?php echo (!isNullOrEmpty($cpfCnpj)) ? "value=\"{$cpfCnpj}\"" : "";?>/>                                
                            </div>
                        </div>
                        <div class="row mt-2">                            
                            <div class="col-md-4">
                                <label for="txOperador">Nome curto</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txOperador" name="operador" maxlength="25"
                                    <?php echo (!isNullOrEmpty($operador)) ? "value=\"{$operador}\"" : "";?>/>
                            </div>
                            <div class="col-md-8">
                                <label for="txNome">Nome completo</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txNome" name="nome"
                                    <?php echo (!isNullOrEmpty($nome)) ? "value=\"{$nome}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="txEndereco">Endereço</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txEndereco" name="endereco"
                                    <?php echo (!isNullOrEmpty($endereco)) ? "value=\"{$endereco}\"" : "";?>/>
                            </div>
                            <div class="col-md-4">
                                <label for="txComplemento">Complemento</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txComplemento" name="complemento"
                                    <?php echo (!isNullOrEmpty($complemento)) ? "value=\"{$complemento}\"" : "";?>/>
                            </div>
                            <div class="col-md-4">
                                <label for="txBairro">Bairro</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txBairro" name="bairro"
                                    <?php echo (!isNullOrEmpty($bairro)) ? "value=\"{$bairro}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="txMunicipio">Município</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txMunicipio" name="municipio"
                                    <?php echo (!isNullOrEmpty($municipio)) ? "value=\"{$municipio}\"" : "";?>/>
                            </div>
                            <div class="col-md-4">
                                <label for="txCidade">Cidade</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txCidade" name="cidade"
                                    <?php echo (!isNullOrEmpty($cidade)) ? "value=\"{$cidade}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txEstado">Estado</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txEstado" name="estado"
                                    <?php echo (!isNullOrEmpty($estado)) ? "value=\"{$estado}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txCep">CEP</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txCep" name="cep"
                                    onfocus="javascript: retirarSimbolos(this);" onblur="javascript: formatarCEP(this);" maxlength="8"
                                    <?php echo (!isNullOrEmpty($cep)) ? "value=\"{$cep}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-5">
                                <label for="txContato">Contato</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txContato" name="contato"
                                    <?php echo (!isNullOrEmpty($contato)) ? "value=\"{$contato}\"" : "";?>/>
                            </div>
                            <div class="col-md-5">
                                <label for="txEmail">Email</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txEmail" name="email"
                                    <?php echo (!isNullOrEmpty($email)) ? "value=\"{$email}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txTelefone">Telefone</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txTelefone" name="telefone"
                                    onfocus="javascript: retirarSimbolos(this);" onblur="javascript: formatarTelefone(this);" maxlength="11"
                                    <?php echo (!isNullOrEmpty($telefone)) ? "value=\"{$telefone}\"" : "";?>/>  
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
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxCpfCnpj" maxlength="14"/>  
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
                    <div class="col-md-6">
                        <label for="ptxEndereco">Endereço</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEndereco"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxComplemento">Complemento</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxComplemento"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxBairro">Bairro</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxBairro"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxMunicipio">Município</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxMunicipio"/>
                    </div>
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="ptxCidade">Cidade</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxCidade"/>
                    </div>
                    <div class="col-md-2">
                        <label for="ptxEstado">Estado</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxEstado"/>
                    </div>
                    <div class="col-md-4">
                        <label for="ptxCep">CEP</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="txCep"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxContato">Contato</label>
                        <input type="text" class="form-control cpoCookie cpoLimpar input-lg" id="ptxContato"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxEmail">Email</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEmail"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxTelefone">Telefone</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxTelefone"/>  
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
                            <option <?php echo ($ordenacao == 'opc.operador,opc.cpfCnpj') ? 'selected' : '';?> value='opc.operador,opc.cpfCnpj'>Nome curto</option>
                            <option <?php echo ($ordenacao == 'opc.nome,opc.operador,opc.cpfCnpj') ? 'selected' : '';?> value='opc.nome,opc.operador,opc.cpfCnpj'>Nome completo</option>
                            <option <?php echo ($ordenacao == 'opc.cpfCnpj,opc.operador') ? 'selected' : '';?> value='opc.cpfCnpj,opc.operador'>CPF/CNPJ</option>
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
                        case "ptxCpfCnpj":
                            filtro += " AND opc.cpfCnpj LIKE '%"+$("#ptxCpfCnpj").val()+"%'";
                            descricaoFiltro += " <br>CPF/CNPJ : "+$("#ptxCpfCnpj").val();
                        break;
                        case "ptxOperador":
                            filtro += " AND opc.operador LIKE '%"+$("#ptxOperador").val()+"%'";
                            descricaoFiltro += " <br>Nome curto : "+$("#ptxOperador").val();
                        break;
                        case "ptxNome":
                            filtro += " AND opc.nome LIKE '%"+$("#ptxNome").val()+"%'";
                            descricaoFiltro += " <br>Nome completo : "+$("#ptxNome").val();
                        break;
                        case "ptxEndereco":
                            filtro += " AND opc.endereco LIKE '%"+$("#ptxEndereco").val()+"%'";
                            descricaoFiltro += " <br>Endereço : "+$("#ptxEndereco").val();
                        break;
                        case "ptxComplemento":
                            filtro += " AND opc.complemento LIKE '%"+$("#ptxComplemento").val()+"%'";
                            descricaoFiltro += " <br>Complemento : "+$("#ptxComplemento").val();
                        break;
                        case "ptxBairro":
                            filtro += " AND opc.bairro LIKE '%"+$("#ptxBairro").val()+"%'";
                            descricaoFiltro += " <br>Bairro : "+$("#ptxBairro").val();
                        break;
                        case "ptxMunicipio":
                            filtro += " AND opc.municipio LIKE '%"+$("#ptxMunicipio").val()+"%'";
                            descricaoFiltro += " <br>Município : "+$("#ptxMunicipio").val();
                        break;
                        case "ptxCidade":
                            filtro += " AND opc.cidade LIKE '%"+$("#ptxCidade").val()+"%'";
                            descricaoFiltro += " <br>Cidade : "+$("#ptxCidade").val();
                        break;
                        case "ptxEstado":
                            filtro += " AND opc.estado LIKE '%"+$("#ptxEstado").val()+"%'";
                            descricaoFiltro += " <br>Estado : "+$("#ptxEstado").val();
                        break;
                        case "ptxCep":
                            filtro += " AND opc.cep LIKE '%"+$("#ptxCep").val()+"%'";
                            descricaoFiltro += " <br>CEP : "+$("#ptxCep").val();
                        break;
                        case "ptxContato":
                            filtro += " AND opc.contato LIKE '%"+$("#ptxContato").val()+"%'";
                            descricaoFiltro += " <br>Contato : "+$("#ptxContato").val();
                        break;
                        case "ptxEmail":
                            filtro += " AND opc.email LIKE '%"+$("#ptxEmail").val()+"%'";
                            descricaoFiltro += " <br>Email : "+$("#ptxEmail").val();
                        break;
                        case "ptxTelefone":
                            filtro += " AND opc.telefone LIKE '%"+$("#ptxTelefone").val()+"%'";
                            descricaoFiltro += " <br>Telefone : "+$("#ptxTelefone").val();
                        break;
                        case "pslSituacao":
                            filtro += " AND opc.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCOC_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCOC_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCOC_descricao', descricaoFiltro);

            await cdCarregarOperadoresCobranca('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
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
            case "operadores":
                await cdVisualizarOperadoresRAB(" AND op.idCobranca = "+$('#hdId').val(),$('#hdOperadorCOB').val());
                $('#botaoVisualizar').trigger('click');
            break;
        }
        
        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCOC_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_cdCOC_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCOC_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxCpfCnpj").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
        });
       
        // Adequações para o cadastro     
        await cdCarregarOperadoresCobranca('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txCpfCnpj").focus();
    });
</script>