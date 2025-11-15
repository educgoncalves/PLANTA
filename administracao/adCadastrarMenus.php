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
    $sistema = carregarPosts('sistema');
    $tipo = carregarPosts('tipo');
    $formulario = carregarPosts('formulario');
    $modulo = carregarPosts('modulo');
    $descricao = carregarPosts('descricao');
    $href = carregarPosts('href');
    $target = carregarPosts('target');
    $iconeSVG = carregarPosts('iconeSVG');
    $ordem = carregarPosts('ordem');
    $atalho = carregarPosts('atalho');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
     
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
    // Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['sistema','tipo','formulario','modulo','descricao']);
    if (!$erros) {
        // Preparando chamada da API apiManterMenus
        $dados = $_POST; 
        $post = ['token'=>$token,'funcao'=>($id != "" ? "Alterar" : "Incluir"),'dados'=>$dados];
        $retorno = executaAPIs('apiManterMenus.php', $post);
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
    $dados = ['tabela'=>'MenusFormulario','filtro'=>" AND me.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $sistema = $dados['sistema'];
            $tipo = $dados['tipo'];
            $formulario = $dados['formulario'];
            $modulo = $dados['modulo'];
            $descricao = $dados['descricao'];
            $href = $dados['href'];
            $target = $dados['target'];
            $iconeSVG = $dados['iconeSVG'];
            $ordem = $dados['ordem'];
            $atalho = $dados['atalho'];
        }
        $limparCampos = false;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    // Preparando chamada da API apiManterMenus
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Excluir",'dados'=>$dados];
    $retorno = executaAPIs('apiManterMenus.php', $post);
    if ($retorno['status'] == 'OK') {
        montarMensagem("success",array($retorno['msg']));
        $id = null;
        $limparCampos = true;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Copiando as informações
if ($evento == "copiar" && $id != "") {
    // Preparando chamada da API apiManterAeroportos
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Copiar",'dados'=>$dados];
    $retorno = executaAPIs('apiManterMenus.php', $post);
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
    $sistema = null;
    $tipo = null;
    $formulario = null;
    $modulo = null;
    $descricao = null;
    $href = null;
    $target = null;
    $iconeSVG = null;
    $ordem = null;
    $atalho = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adCMN_ordenacao','me.sistema, me.ordem, me.formulario, me.descricao');
metaTagsBootstrap('');
$titulo = "Menus";
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

                <input type="hidden" class="cpoLimpar" id="hdTipo" <?="value=\"{$tipo}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdAtalho" <?="value=\"{$atalho}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row"> 
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="txSistema">Sistema</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txSistema" name="sistema"
                                    <?=(!isNullOrEmpty($sistema)) ? "value=\"{$sistema}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="slTipo">Tipo</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slTipo" name="tipo">
                                </select>                                 
                            </div>
                            <div class="col-md-4">
                                <label for="txModulo">Módulo</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txModulo" name="modulo" maxlength="50"
                                    <?=(!isNullOrEmpty($modulo)) ? "value=\"{$modulo}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txFormulario">Formulário</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txFormulario" name="formulario"
                                    <?=(!isNullOrEmpty($formulario)) ? "value=\"{$formulario}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2"> 
                            <div class="col-md-5">
                                <label for="txDescricao">Descrição</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txDescricao" name="descricao" maxlength="50"
                                    <?=(!isNullOrEmpty($descricao)) ? "value=\"{$descricao}\"" : "";?>/>
                            </div>
                            <div class="col-md-7">
                                <label for="txHref">Href</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txHref" name="href" maxlength="150"
                                    <?=(!isNullOrEmpty($href)) ? "value=\"{$href}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">                             
                            <div class="col-md-2">
                                <label for="txTarget">Target</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txTarget" name="target" maxlength="50"
                                    <?=(!isNullOrEmpty($target)) ? "value=\"{$target}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txIconeSVG">SVG</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txIconeSVG" name="iconeSVG" maxlength="50"
                                    <?=(!isNullOrEmpty($iconeSVG)) ? "value=\"{$iconeSVG}\"" : "";?>/>
                            </div>
                            <div class="col-md-1">
                                <label for="txOrdem">Ordenação</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txOrdem" name="ordem" maxlength="3"
                                    <?php echo (!isNullOrEmpty($ordem)) ? "value=\"{$ordem}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="slAtalho">Atalho</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slAtalho" name="atalho">
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
                    <div class="col-md-3">
                        <label for="pslSistema">Sistema</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSistema">
                        </select> 
                    </div>
                    <div class="col-md-6">
                        <label for="pslTipo">Tipo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslTipo">
                        </select> 
                    </div>
                    <div class="col-md-3">
                        <label for="ptxFormulario">Formulário</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxFormulario"/>
                    </div>
                </div>
                <div class="row mt-2">  
                    <div class="col-md-6">
                        <label for="pslModulo">Módulo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslModulo">
                        </select> 
                    </div>
                    <div class="col-md-6">
                        <label for="ptxDescricao">Descrição</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxDescricao"/>
                    </div>
                </div>
                <div class="row mt-2">                      
                    <div class="col-md-6">
                        <label for="ptxHref">Href</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxHref"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxTarget">Target</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxTarget"/>
                    </div>
                </div>
                <div class="row mt-2">  
                    <div class="col-md-6">
                        <label for="ptxIconeSVG">SVG</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxIconeSVG" />
                    </div>                    
                    <div class="col-md-6">
                        <label for="pslAtalho">Atalho</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslAtalho">
                        </select> 
                    </div>
                </div>
                <br>
                <div class="row mt-2">                     
                    <div class="col-md-8">
                        <label for="pslOrdenacao">Ordenação da lista</label>
                        <select class="form-select selCookie input-lg" id="pslOrdenacao">
                            <option <?=($ordenacao == 'me.sistema, me.ordem, me.formulario, me.descricao') ? 'selected' : '';?> 
                                                value='me.sistema, me.ordem, me.formulario, me.descricao'>Sistema</option>
                            <option <?=($ordenacao == 'me.modulo, me.sistema, me.ordem, me.formulario, me.descricao') ? 'selected' : '';?> 
                                                value='me.modulo, me.sistema, me.ordem, me.formulario, me.descricao'>Módulo</option>
                            <option <?=($ordenacao == 'me.descricao, me.sistema, me.ordem, me.formulario') ? 'selected' : '';?> 
                                                value='me.descricao, me.sistema, me.ordem, me.formulario'>Descrição</option>
                            <option <?=($ordenacao == 'me.formulario, me.descricao') ? 'selected' : '';?> 
                                                value='me.formulario, me.descricao'>Formulário</option>
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
<script src="../pesquisas/pqPesquisa.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#txSistema").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pslSistema":
                            filtro += " AND me.sistema = '"+$("#pslSistema").val()+"'";
                            descricaoFiltro += " <br>Sistema : "+$("#pslSistema :selected").text();
                        break;
                        case "pslTipo":
                            filtro += " AND me.tipo = '"+$("#pslTipo").val()+"'";
                            descricaoFiltro += " <br>Tipo : "+$("#pslTipo :selected").text();
                        break;
                        case "ptxFormulario":
                            filtro +=  " AND me.formulario LIKE '%"+$("#ptxFormulario").val()+"%'";
                            descricaoFiltro += " <br>Formulário : "+$("#ptxFormulario").val();
                        break;
                        case "pslModulo":
                            filtro += " AND me.modulo = '"+$("#pslModulo").val()+"'";
                            descricaoFiltro += " <br>Módulo : "+$("#pslModulo :selected").text();
                        break;
                        case "ptxDescricao":
                            filtro += " AND me.descricao LIKE '%"+$("#ptxDescricao").val()+"%'";
                            descricaoFiltro += " <br>Descrição : "+$("#ptxDescricao").val();
                        break;
                        case "ptxHref":
                            filtro += " AND me.href LIKE '%"+$("#ptxHref").val()+"%'";
                            descricaoFiltro += " <br>Href : "+$("#ptxHref").val();
                        break;
                        case "ptxTarget":
                            filtro += " AND me.target LIKE '%"+$("#ptxTarget").val()+"%'";
                            descricaoFiltro += " <br>Target : "+$("#ptxTarget").val();
                        break;    
                        case "ptxIconeSVG":
                            filtro += " AND me.iconeSVG LIKE '%"+$("#ptxIconeSVG").val()+"%'";
                            descricaoFiltro += " <br>SVG: "+$("#ptxIconeSVG").val();
                        break;
                        case "pslAtalho":
                            filtro += " AND me.atalho = '"+$("#pslAtalho").val()+"'";
                            descricaoFiltro += " <br>Atalho : "+$("#pslAtalho :selected").text();
                        break;                      
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            }); 

            // Montagem da ordem
            var ordem = $("#pslOrdenacao").val();
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCMN_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCMN_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCMN_descricao', descricaoFiltro);

            await adCarregarMenus('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txSistema").focus();
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
            $("#txSistema").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adCMN_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adCMN_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adCMN_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxSistema").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){
            await suCarregarSelectTodos('Atalhos','#pslAtalho', '','','Consultar');
            await suCarregarSelectTodos('TipoMenu','#pslTipo', '','','Consultar');
            await suCarregarSelectTodos('Modulos','#pslModulo', '','','Consultar');
            await suCarregarSelectTodos('Sistemas','#pslSistema', '','','Consultar');
        });
        $("#ptxFormulario").mask('YYYY', {'translation': {Y: {pattern: /[0-9]/},}});
        $("#ptxSistema").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z]/}}});

        // Adequações para o cadastro
        await suCarregarSelectTodos('Atalhos','#slAtalho', $('#hdAtalho').val(),'','Cadastrar');
        await suCarregarSelectTodos('TipoMenu','#slTipo', $('#hdTipo').val(),'','Cadastrar');
        await adCarregarMenus('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));

        $("#txFormulario").mask('YYYY', {'translation': {Y: {pattern: /[0-9]/},}});
        $("#txSistema").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z]/}}});
        $("#txOrdem").mask('000', {reverse: true});
        $("#txSistema").focus();
    });
</script>