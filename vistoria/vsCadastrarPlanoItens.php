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
    $id = carregarPosts('id');           // PlanoItem
    $idPlano = carregarPosts('idPlano');
    $idItem = carregarPosts('idItem');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    // if (!verificaRequest()) { goto formulario; }
     
} else  {
    $id = carregarGets('id');            // PlanoItem
    $idPlano = carregarGets('idPlano');
    $idItem = carregarGets('idItem');
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
	// Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['idPlano','idItem']);
    if (!$erros) {
        // Preparando chamada da API apiManterVistoriaPlanosItens
        $dados = $_POST;
        $post = ['token'=>$token,'funcao'=>($id != "" ? "Alterar" : "Incluir"),'dados'=>$dados];
        $retorno = executaAPIs('apiManterVistoriaPlanosItens.php', $post);
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

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    // Preparando chamada da API apiManterVistoriaPlanosItens
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Excluir",'dados'=>$dados];
    $retorno = executaAPIs('apiManterVistoriaPlanosItens.php', $post);
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
    $idItem = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_vsCPI_ordenacao','vp.numero, vp.inicio, vi.numero');
metaTagsBootstrap('');
$titulo = "Itens para o plano";
?>
<head>
    <title><?=$_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto)?>    
    <div class="container alert alert-padrao" >
        <form action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;"> 
            <?php barraFuncoesCadastro($titulo); ?>        
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->                 
                <input type="hidden" name="usuario" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" name="aeroporto" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" name="siglaAeroporto" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" name="id" id="hdId" class="cpoLimpar" <?="value=\"{$id}\"";?>/>
                <input type="hidden" name="idPlano" id="hdIdPlano" <?="value=\"{$idPlano}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row"> 
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="slTipo">Tipo</label>
                                <select class="form-select cpoLimpar input-lg" id="slTipo" name="slTipo">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">                            
                            <div class="col-md-10">
                                <label for="slItem">Item</label>
                                <select class="form-select cpoLimpar cpoObrigatorio input-lg" id="slItem" name="idItem">
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

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->

<script src="../vistoria/vsFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#slTipo").change();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND vp.idAeroporto = "+$("#hdAeroporto").val()+" AND vp.id = "+$("#hdIdPlano").val(): "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

            // Montagem da ordem
            var ordem = 'vp.numero, vp.inicio, vi.numero';
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsCPI_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsCPI_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsCPI_descricao', descricaoFiltro);

            await vsCarregarVistoriaPlanosItens('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slTipo").focus();
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
            $("#slTipo").focus();
        });
        
        $("#retornarFormulario").click(function(){ window.location.href = "../vistoria/vsCadastrarPlanos.php?idPlano="+$("#hdIdPlano").val(); });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_vsCPI_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_vsCPI_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_vsCPI_descricao');

        // Adequações para o cadastro
        pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND vp.idAeroporto = "+$("#hdAeroporto").val()+" AND vp.id = "+$("#hdIdPlano").val(): "");
        pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

        await suCarregarSelectTodos('VistoriaItensTipos','#slTipo','','','Cadastrar');
        await suCarregarSelectTodos('VistoriaItens','#slItem','',
                " AND (vi.idAeroporto = 0 OR vi.idAeroporto = "+$("#hdAeroporto").val()+") AND vi.tipo = 'XXX'",'Cadastrar');
        await vsCarregarVistoriaPlanosItens('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#slTipo").focus();

        $('#slTipo').change(async function(){
            await suCarregarSelectTodos('VistoriaItens','#slItem','',
                " AND (vi.idAeroporto = 0 OR vi.idAeroporto = "+$("#hdAeroporto").val()+") AND vi.tipo = '"+$("#slTipo").val()+"'",'Cadastrar');
		});
    });
</script>