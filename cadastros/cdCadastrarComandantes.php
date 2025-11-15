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
    $codigoAnac = carregarPosts('codigoAnac');
    $nome = carregarPosts('nome');
    $telefone = carregarPosts('telefone');
    $email = carregarPosts('email');
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
    $erros = camposPreenchidos(['codigoAnac','nome']);
    if (!$erros) {
        // Preparando chamada da API apiManterComandantes
        $dados = $_POST;
        $post = ['token'=>$token,'funcao'=>($id != "" ? "Alterar" : "Incluir"),'dados'=>$dados];
        $retorno = executaAPIs('apiManterComandantes.php', $post);
        if ($retorno['status'] == 'OK') {
            montarMensagem("success",array($retorno['msg']));
            $id = null;
            $limparCampos = true;
        } else {
            montarMensagem("danger",array($retorno['msg']));
        }
    } else {
        montarMensagem("danger", $erros);
    } 
}

// Recuperando as informações
if ($evento == "recuperar" && $id != "") {
    // Preparando chamada da API apiConsultas
    $dados = ['tabela'=>'Comandantes','filtro'=>" AND co.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $codigoAnac = $dados['codigoAnac'];
            $nome = $dados['nome'];
            $telefone = $dados['telefone'];
            $email = $dados['email'];
            $situacao = $dados['situacao'];
            $txTodosSituacao = $dados['descSituacao'];
        }
        $limparCampos = false;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    // Preparando chamada da API apiManterComandantes
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Excluir",'dados'=>$dados];
    $retorno = executaAPIs('apiManterComandantes.php', $post);
    if ($retorno['status'] == 'OK') {
        montarMensagem("success",array($retorno['msg']));
        $id = null;
        $limparCampos = true;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $codigoAnac = null;
    $nome = null;
    $telefone = null;
    $email = null;
    $situacao = null;
    $txTodosSituacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_cdCC_ordenacao','co.codigoAnac');
metaTagsBootstrap('');
$titulo = "Comandantes";
?>
<head>
    <title><?=$_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <form action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;"> 
            <?php barraFuncoesCadastro($titulo); ?>        
	    	<div class="form-group">
                <!-- Campos hidden -->
                <input type="hidden" name="usuario" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" name="siglaAeroporto" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" name="id" id="hdId" class="cpoLimpar" <?="value=\"{$id}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row"> 
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="txCodigoAnac">Código ANAC</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txCodigoAnac" name="codigoAnac" maxlength="6"
                                    <?=(!isNullOrEmpty($codigoAnac)) ? "value=\"{$codigoAnac}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">                             
                            <div class="col-md-8">
                                <label for="txNome">Nome</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txNome" name="nome" maxlength="150"
                                    <?=(!isNullOrEmpty($nome)) ? "value=\"{$nome}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="txTelefone">Telefone</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txTelefone" name="telefone"
                                    <?=(!isNullOrEmpty($telefone)) ? "value=\"{$telefone}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">                              
                            <div class="col-md-8">
                                <label for="txEmail">Email</label>
                                <input type="email" class="form-control cpoLimpar input-lg" id="txEmail" name="email" maxlength="50"
                                    <?php echo (!isNullOrEmpty($email)) ? "value=\"{$email}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="idTodosSituacao">Situação</label>
                                <input type="text" class="form-select cpoLimpar input-lg" id="txTodosSituacao" placeholder="Selecionar" name="txTodosSituacao"
                                    <?=(!isNullOrEmpty($txTodosSituacao)) ? "value=\"{$txTodosSituacao}\"" : "";?>
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
<!-- Modal PESQUISA -->
<!-- *************************************************** -->
<div class="modal fade" id="pesquisarCadastro" tabindex="-1" aria-labelledby="sobreLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sobreLabel">Pesquisar <?=$titulo ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label for="ptxCodigoAnac">Código ANAC</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxCodigoAnac"/>
                    </div>
                </div>
                <div class="row mt-2">  
                    <div class="col-md-12">
                        <label for="ptxNome">Nome</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNome"/>
                    </div>
                </div>
                <div class="row mt-2">                              
                    <div class="col-md-4">
                        <label for="ptxTelefone">Telefone</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxTelefone"/>
                    </div>
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
                            <option <?=($ordenacao == 'co.codigoAnac') ? 'selected' : '';?> value='co.codigoAnac'>Código ANAC</option>
                            <option <?=($ordenacao == 'co.nome,co.codigoAnac') ? 'selected' : '';?> value='co.nome,co.codigoAnac'>Nome</option>
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
            $("#txCodigoAnac").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxCodigoAnac":
                            filtro += " AND co.codigoAnac LIKE '%"+$("#ptxCodigoAnac").val()+"%'";
                            descricaoFiltro += " <br>Código ANAC : "+$("#ptxCodigoAnac").val();
                        break;
                        case "ptxNome":
                            filtro += " AND co.nome LIKE '%"+$("#ptxNome").val()+"%'";
                            descricaoFiltro += " <br>Nome : "+$("#ptxNome").val();
                        break;
                        case "ptxTelefone":
                            filtro += " AND co.telefone LIKE '%"+$("#ptxTelefone").val()+"%'";
                            descricaoFiltro += " <br>Telefone : "+$("#ptxTelefone").val();
                        break;
                        case "ptxEmail":
                            filtro += " AND co.email LIKE '%"+$("#ptxEmail").val()+"%'";
                            descricaoFiltro += " <br>Email : "+$("#ptxEmail").val();
                        break; 
                        case "pslSituacao":
                            filtro += " AND co.situacao = '"+$("#pslSituacao").val()+"'";
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
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCC_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCC_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCC_descricao', descricaoFiltro);

            await cdCarregarComandantes('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txCodigoAnac").focus();
        };

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
            $("#txCodigoAnac").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCC_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_cdCC_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCC_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxIcao").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
        });
        $("#ptxTelefone").mask('+YY YY YYYYY-YYYY', {'translation': {Y: {pattern: /[0-9]/},}});
        $("#ptxCodigoAnac").mask('YYYYYY', {'translation': {Y: {pattern: /[0-9]/}}});

        // Adequações para o cadastro
        await cdCarregarComandantes('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
        $("#txTelefone").mask('+YY YY YYYYY-YYYY', {'translation': {Y: {pattern: /[0-9]/},}});
        $("#txCodigoAnac").mask('YYYYYY', {'translation': {Y: {pattern: /[0-9]/}}});
        $("#txCodigoAnac").focus();
    });
</script>