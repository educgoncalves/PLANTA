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
    $usuario = carregarPosts('usuario'); 
    $nome = carregarPosts('nome'); 
    $celular = carregarPosts('celular'); 
    $email = carregarPosts('email'); 
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
    $erros = camposPreenchidos(['usuario','celular','nome','email']); //,'situacao']);
    if (!$erros) {
        try {
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE planta_usuarios SET usuario = '".$usuario."', nome = '".$nome."', email = '".$email."', situacao = '".
                            $situacao."', celular = '".$celular."', cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO planta_usuarios (usuario, senha, celular, nome, email, situacao, cadastro) VALUES ('".
                            $usuario."', sha1('". $usuario."'), '".$celular."', '".$nome."', '".$email."', '".
                            $situacao."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando);               
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("planta_usuarios", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaSite'], 
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

// Reset da senha
if ($evento == "reset" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "UPDATE planta_usuarios SET senha = sha1(usuario), cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
        $sql = $conexao->prepare($comando);               
        if ($sql->execute()) {
            if ($sql->rowCount() > 0) {
                gravaDLog("planta_usuarios", "Reset", $_SESSION['plantaSite'], $_SESSION['plantaUsuario'], $id, $comando);            
                montarMensagem("success",array("Senha resetada com sucesso!"));
                $id = null;
                $limparCampos = true;
            } else {
                throw new PDOException("Não foi possível resetar a senha!");
            }
        } else {
            throw new PDOException("Não foi possível resetar este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Recuperando as informações
if ($evento == "recuperar" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "SELECT * FROM planta_usuarios WHERE id = ".$id;
        $sql = $conexao->prepare($comando);               
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $usuario = $dados['usuario'];
                $celular = $dados['celular']; 
                $nome = $dados['nome'];
                $email = $dados['email'];
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
        $comando = "DELETE FROM planta_usuarios WHERE id = ".$id;
        $sql = $conexao->prepare($comando);               
        if ($sql->execute()){
            gravaDLog("planta_usuarios", "Exclusão", $_SESSION['plantaSite'], $_SESSION['plantaUsuario'], $id, $comando);            
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
    $usuario = null;
    $celular = null; 
    $nome = null;
    $email = null;
    $situacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adCU_ordenacao','us.usuario');     
metaTagsBootstrap('');
$titulo = "Usuários";
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
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">     
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="txUsuario">Usuário</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txUsuario" name="usuario"
                                    <?php echo (!isNullOrEmpty($usuario)) ? "value=\"{$usuario}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">                            
                            <div class="col-md-6">
                                <label for="txNome">Nome</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txNome" name="nome" 
                                    <?php echo (!isNullOrEmpty($nome)) ? "value=\"{$nome}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="txCelular">Celular</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txCelular" name="celular" maxlength="13"
                                    <?php echo (!isNullOrEmpty($celular)) ? "value=\"{$celular}\"" : "";?>/>
                            </div><small class="text-primary">Celular completo com código do pais e DDD, digitar somente números</small>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="txEmail">Email</label>
                                <input type="email" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txEmail" name="email"
                                    <?php echo (!isNullOrEmpty($email)) ? "value=\"{$email}\"" : "";?>/>
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
                    <div class="col-md-8">
                        <label for="txUsuario">Usuário</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxUsuario"/>
                    </div>
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-8">
                        <label for="ptxNome">Nome</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNome"/>
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
                        <label for="ptxEmail">Email</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEmail"/>
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
                            <option <?php echo ($ordenacao == 'us.usuario') ? 'selected' : '';?> value='us.usuario'>Usuário</option>
                            <option <?php echo ($ordenacao == 'us.nome') ? 'selected' : '';?> value='us.nome'>Nome</option>
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
            $("#txUsuario").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxUsuario":
                            filtro += " AND us.usuario = '"+$("#ptxUsuario").val()+"'";
                            descricaoFiltro += " <br>Usuário : "+$("#ptxUsuario").val();
                            break;
                        case "ptxNome":
                            filtro += " AND us.nome LIKE '%"+$("#ptxNome").val()+"%'";
                            descricaoFiltro += " <br>Nome : "+$("#ptxNome").val();
                            break;     
                        case "ptxCelular":
                            filtro += " AND us.celular LIKE '%"+$("#ptxCelular").val()+"%'";
                            descricaoFiltro += " <br>Celular : "+$("#ptxCelular").val();
                            break;                        
                        case "ptxEmail":
                            filtro += " AND us.email LIKE '%"+$("#ptxEmail").val()+"%'";
                            descricaoFiltro += " <br>Email : "+$("#ptxEmail").val();
                        break; 
                        case "pslSituacao":
                            filtro += " AND us.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_adCU_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCU_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCU_descricao', descricaoFiltro);
                                    
            await adCarregarUsuarios('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txUsuario").focus();
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
            $("#txUsuario").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adCU_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adCU_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adCU_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxUsuario").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){         
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
        });
        $("#ptxCelular").mask('Y', {'translation': {Y: {pattern: /[0-9]/,recursive: true},}});
        
        // Adequações para o cadastro  
        await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await adCarregarUsuarios('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txUsuario").focus();
        $("#txCelular").mask('Y', {'translation': {Y: {pattern: /[0-9]/,recursive: true},}});
    });
</script>