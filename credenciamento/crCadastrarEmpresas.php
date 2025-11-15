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
    $empresa = carregarPosts('empresa');
    $atividade = carregarPosts('atividade');
    $endereco = carregarPosts('endereco');
    $bairro = carregarPosts('bairro');
    $email = carregarPosts('email');
    $telefone = carregarPosts('telefone');
    $situacao = carregarPosts('situacao','ATV');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
       
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
    // Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['empresa','atividade','email']);
    if (!$erros) {
        try {
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE gear_empresas SET idAeroporto = ".$aeroporto.", empresa = '".$empresa."', atividade = '".$atividade."', endereco = '".
                            $endereco."', bairro = '".$bairro."', email = '".$email."', telefone = '".$telefone."', situacao = '".$situacao.
                            "', cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO gear_empresas (idAeroporto, empresa, atividade, endereco, bairro, email, telefone, situacao, cadastro) VALUES (".
                            $aeroporto.", '".$empresa."', '".$atividade."', '".$endereco."', '".$bairro."', '".
                            $email."', '".$telefone."', '".$situacao."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_empresas", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
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
        $comando = "SELECT * FROM gear_empresas WHERE id = ".$id;
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $empresa = $dados['empresa'];
                $atividade = $dados['atividade'];
                $endereco = $dados['endereco'];
                $bairro = $dados['bairro'];
                $email = $dados['email'];
                $telefone = $dados['telefone'];
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
        $comando = "DELETE FROM gear_empresas WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_empresas", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $id, $comando);   
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
    $empresa = null;
    $atividade = null;
    $endereco = null;
    $bairro = null;
    $email = null;
    $telefone = null;
    $situacao = 'ATV';
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_crCE_ordenacao','em.empresa');    
metaTagsBootstrap('');
$titulo = "Empresas";
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
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">                
                        <div class="row mt-2">  
                            <div class="col-md-6">
                                <label for="txEmpresa">Empresa</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txEmpresa" name="empresa"
                                    <?php echo (!isNullOrEmpty($empresa)) ? "value=\"{$empresa}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="txEndereco">Endereço</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txEndereco" name="endereco"
                                    <?php echo (!isNullOrEmpty($endereco)) ? "value=\"{$endereco}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="txBairro">Bairro</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txBairro" name="bairro"
                                    <?php echo (!isNullOrEmpty($bairro)) ? "value=\"{$bairro}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="txTelefone">Telefone</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar phone" id="txTelefone" name="telefone" maxlength="15"
                                    <?php echo (!isNullOrEmpty($telefone)) ? "value=\"{$telefone}\"" : "";?>/>
                            </div>                             
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="txEmail">Email</label>
                                <input type="email" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txEmail" name="email"
                                    <?php echo (!isNullOrEmpty($email)) ? "value=\"{$email}\"" : "";?>/>
                            </div>                            
                            <div class="col-md-3">
                                <label for="txAtividade">Atividade</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txAtividade" name="atividade"
                                    <?php echo (!isNullOrEmpty($atividade)) ? "value=\"{$atividade}\"" : "";?>/>
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
                    <div class="col-md-6">
                        <label for="ptxEmpresa">Empresa</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxEmpresa"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label for="ptxEndereco">Endereço</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxEndereco"/>
                    </div>
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="ptxBairro">Bairro</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxBairro"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxTelefone">Telefone</label>
                        <input type="text" class="form-control cpoCookie phone" id="ptxTelefone" maxlength="15"/>
                    </div>                     
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label for="ptxEmail">Email</label>
                        <input type="email" class="form-control cpoCookie input-lg" id="ptxEmail"/>
                    </div>                            
 
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxAtividade">Atividade</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxAtividade"/>
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
                            <option <?php echo ($ordenacao == 'em.empresa') ? 'selected' : '';?> value='em.empresa'>Empresa</option>
                            <option <?php echo ($ordenacao == 'em.atividade, em.empresa') ? 'selected' : '';?> value='em.atividade, em.empresa'>Atividade</option>
                            <option <?php echo ($ordenacao == 'em.bairro, em.empresa') ? 'selected' : '';?> value='em.bairro, em.empresa'>Bairro</option>
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

<script src="../credenciamento/crFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#txEmpresa").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND em.idAeroporto = "+$("#hdAeroporto").val() : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxEmpresa":
                            filtro += " AND em.empresa LIKE '%"+$("#ptxEmpresa").val()+"%'";
                            descricaoFiltro += " <br>Empresa : "+$("#ptxEmpresa").val();
                            break;
                        case "ptxEndereco":
                            filtro += " AND em.endereco LIKE '%"+$("#ptxEndereco").val()+"%'";
                            descricaoFiltro += " <br>Endereço : "+$("#ptxEndereco").val();
                            break;    
                        case "ptxBairro":
                            filtro += " AND em.bairro LIKE '%"+$("#ptxBairro").val()+"%'";
                            descricaoFiltro += " <br>Bairro : "+$("#ptxBairro").val();
                            break; 
                        case "ptxEmail":
                            filtro += " AND em.email LIKE '%"+$("#ptxEmail").val()+"%'";
                            descricaoFiltro += " <br>Email : "+$("#ptxEmail").val();
                            break; 
                        case "ptxTelefone":
                            filtro += " AND em.telefone LIKE '%"+$("#ptxTelefone").val()+"%'";
                            descricaoFiltro += " <br>Telefone : "+$("#ptxTelefone").val();
                            break; 
                        case "ptxAtividade":
                            filtro += " AND em.atividade LIKE '%"+$("#ptxAtividade").val()+"%'";
                            descricaoFiltro += " <br>Atividade : "+$("#ptxAtividade").val();
                            break; 
                        case "pslSituacao":
                            filtro += " AND em.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_crCE_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_crCE_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_crCE_descricao', descricaoFiltro);
                        
            await crCarregarEmpresas('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txEmpresa").focus();
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
            $("#txEmpresa").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_crCE_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_crCE_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_crCE_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxEmpresa").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao', '','','Consultar');
        });
        
        // Adequações para o cadastro  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND em.idAeroporto = "+$("#hdAeroporto").val() : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }
        await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await crCarregarEmpresas('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txEmpresa").focus();
        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

    });

    var behavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        }, options = { onKeyPress: function (val, e, field, options) {
                    field.mask(behavior.apply({}, arguments), options);}};
    $('.phone').mask(behavior, options);
</script>