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
    $codigo = carregarPosts('codigo');
    $descricao = carregarPosts('descricao');
    $tmpTolerancia = carregarPosts('tmpTolerancia');
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
    $erros = camposPreenchidos(['codigo','descricao','tmpTolerancia']);
    if (!$erros) {
        // Preparando chamada da API apiManterTarefas
        $dados = $_POST;
        $post = ['token'=>$token,'funcao'=>($id != "" ? "Alterar" : "Incluir"),'dados'=>$dados];
        $retorno = executaAPIs('apiManterTarefas.php', $post);
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
    $dados = ['tabela'=>'Tarefas','filtro'=>" AND tr.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $codigo = $dados['codigo'];
            $descricao = $dados['descricao'];
            $tmpTolerancia = $dados['tmpTolerancia'];
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
    // Preparando chamada da API apiManterTarefas
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Excluir",'dados'=>$dados];
    $retorno = executaAPIs('apiManterTarefas.php', $post);
    if ($retorno['status'] == 'OK') {
        montarMensagem("success",array($retorno['msg']));
        $id = null;
        $limparCampos = true;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Limpeza dos campos 
if ($limparCampos == true) {
    $codigo = null;
    $descricao = null;
    $email = null;
    $tmpTolerancia = null;
    $situacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_svCT_ordenacao','tr.situacao,tr.dhExecucao,tr.codigo');
metaTagsBootstrap('');
$titulo = "Tarefas";
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

                <input type="hidden" class="cpoLimpar" id="hdEmail" <?="value=\"{$email}\"";?>/>   
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>   

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row"> 
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="txCodigo">Código</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txCodigo" name="codigo"
                                    <?=(!isNullOrEmpty($codigo)) ? "value=\"{$codigo}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">                             
                            <div class="col-md-4">
                                <label for="txDescricao">Descrição</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txDescricao" name="descricao" maxlength="50"
                                    <?=(!isNullOrEmpty($descricao)) ? "value=\"{$descricao}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txTmpTolerancia">Tolerância (min)</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txTmpTolerancia" name="tmpTolerancia" maxlength="4"
                                    <?php echo (!isNullOrEmpty($tmpTolerancia)) ? "value=\"{$tmpTolerancia}\"" : "";?>/>
                            </div>  
                            <div class="col-md-2">
                                <label for="slEmail">Enviar Email</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slEmail" name="email">
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
                <h5 class="modal-title" id="sobreLabel">Pesquisar <?=$titulo ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label for="ptxCodigo">Código</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxCodigo"/>
                    </div>
                </div>
                <div class="row mt-2">  
                    <div class="col-md-12">
                        <label for="ptxDescricao">Descrição</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxDescricao"/>
                    </div>
                </div>
                <div class="row mt-2">   
                    <div class="col-md-4">
                        <label for="pslEmail">Enviar Email</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslEmail">
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
                            <option <?=($ordenacao == 'tr.codigo') ? 'selected' : '';?> value='tr.codigo'>Código</option>
                            <option <?=($ordenacao == 'tr.descricao') ? 'selected' : '';?> value='tr.descricao'>Descrição</option>
                            <option <?=($ordenacao == 'tr.situacao,tr.dhExecucao,tr.codigo') ? 'selected' : '';?> value='tr.situacao,tr.dhExecucao,tr.codigo'>Execução</option>
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

<script src="../tarefas/trFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<!-- <script src="../pesquisas/pqPesquisa.js"></script> -->
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#txCodigo").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxCodigo":
                            filtro += " AND tr.codigo LIKE '%"+$("#ptxCodigo").val()+"%'";
                            descricaoFiltro += " <br>Código : "+$("#ptxCodigo").val();
                        break;
                        case "ptxDescricao":
                            filtro += " AND tr.descricao LIKE '%"+$("#ptxDescricao").val()+"%'";
                            descricaoFiltro += " <br>Descricao : "+$("#ptxDescricao").val();
                        break;
                        case "pslEmail":
                            filtro += " AND tr.email = '"+$("#pslEmail").val()+"'";
                            descricaoFiltro += " <br>Enviar Email : "+$("#pslEmail :selected").text();
                        break;
                        case "pslSituacao":
                            filtro += " AND tr.situacao = '"+$("#pslSituacao").val()+"'";
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
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_svCT_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_svCT_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_svCT_descricao', descricaoFiltro);

            await trCarregarTarefas('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txCodigo").focus();
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
            $("#txCodigo").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_svCT_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_svCT_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_svCT_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxIcao").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodos('TodosSimNao','#pslEmail', '', '','Cadastrar');
            await tsuCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
        });
        $("#ptxCodigo").mask('AAAA', {'translation': {Y: {pattern: /[A-Z][a-z]/}}});

        // Adequações para o cadastro
        await suCarregarSelectTodos('TodosSimNao','#slEmail', $('#hdEmail').val(), '','Cadastrar');
        await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await trCarregarTarefas('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txCodigo").mask('AAAA', {'translation': {Y: {pattern: /[A-Z][a-z]/}}});
        $("#txCodigo").focus();
    });
</script>