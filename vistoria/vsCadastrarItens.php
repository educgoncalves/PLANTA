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
$usuarioGrupo = $_SESSION['plantaGrupo'];
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
    $tipo = carregarPosts('tipo');
    $numero = carregarPosts('numero');
    $item = carregarPosts('item');
    $situacao = carregarPosts('situacao');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest('')) { goto formulario; }
    
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
	// Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['tipo','numero','item','situacao']);
    if (!$erros) {
        // Preparando chamada da API apiManterVistoriaItens
        $dados = $_POST;
        $post = ['token'=>$token,'funcao'=>($id != "" ? "Alterar" : "Incluir"),'dados'=>$dados];
        $retorno = executaAPIs('apiManterVistoriaItens.php', $post);
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
    $dados = ['tabela'=>'VistoriaItens','filtro'=>" AND vi.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $tipo = $dados['tipo'];
            $numero = $dados['numero'];
            $item = $dados['item'];
            $situacao = $dados['situacao'];
        }
        $limparCampos = false;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    // Preparando chamada da API apiManterVistoriaItens
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Excluir",'dados'=>$dados];
    $retorno = executaAPIs('apiManterVistoriaItens.php', $post);
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
    // Preparando chamada da API apiManterVistoriaItens
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto,"aeroporto"=>$aeroporto];
    $post = ['token'=>$token,'funcao'=>"Copiar",'dados'=>$dados];
    $retorno = executaAPIs('apiManterVistoriaItens.php', $post);
    if ($retorno['status'] == 'OK') {
        montarMensagem("success",array($retorno['msg']));
        $id = null;
        $limparCampos = true;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Padronizando item
if ($evento == "padronizar" && $id != "") {
    // Preparando chamada da API apiManterVistoriaItens
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Padronizar",'dados'=>$dados];
    $retorno = executaAPIs('apiManterVistoriaItens.php', $post);
    if ($retorno['status'] == 'OK') {
        montarMensagem("success",array($retorno['msg']));
        $id = null;
        $limparCampos = true;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Despadronizando item
if ($evento == "despadronizar" && $id != "") {
    // Preparando chamada da API apiManterAeroportos
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto,"aeroporto"=>$aeroporto];
    $post = ['token'=>$token,'funcao'=>"Despadronizar",'dados'=>$dados];
    $retorno = executaAPIs('apiManterVistoriaItens.php', $post);
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
    $tipo = null;
    $numero = null;
    $item = null;
    $situacao = 'ATV';
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_vsCI_ordenacao','vi.tipo,vi.numero');
metaTagsBootstrap('');
$titulo = "Itens de Vistoria";
?>
<head>
    <title><?=$_SESSION['plantaSistema']." - ".$titulo?></title>
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
                <input type="hidden" name="usuario" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" name="usuarioGrupo" id="hdUsuarioGrupo" <?="value=\"{$usuarioGrupo}\"";?>/>
                <input type="hidden" name="aeroporto" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" name="siglaAeroporto" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                <input type="hidden" name="id" id="hdId" class="cpoLimpar" <?="value=\"{$id}\"";?>/>

                <input type="hidden" class="cpoLimpar" id="hdTipo" <?="value=\"{$tipo}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>   

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row"> 
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-8 form-group">
                                <label for="slTipo">Tipo</label>
                                <select class="form-select cpoLimpar input-lg" id="slTipo" name="tipo">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">                             
                            <div class="col-md-2">
                                <label for="txNumero">Número</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txNumero" name="numero"
                                    <?=(!isNullOrEmpty($numero)) ? "value=\"{$numero}\"" : "";?>/>
                            </div>
                             <div class="col-md-8">
                                <label for="txItem">Item</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txItem" name="item"
                                    <?=(!isNullOrEmpty($item)) ? "value=\"{$item}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="slSituacao">Situação</label>
                                <select class="form-select cpoLimpar cpoObrigatorio input-lg" id="slSituacao" name="situacao">
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
                    <div class="col-md-12">
                        <label for="pslTipo">Tipo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslTipo">
                        </select>                         
                    </div>
                </div>
                <div class="row mt-2">                      
                    <div class="col-md-6">
                        <label for="ptxNumero">Número</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxNumero"/>
                    </div>
                </div>
                <div class="row mt-2">  
                    <div class="col-md-12">
                        <label for="ptxItem">Item</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxItem"/>
                    </div>
                </div>
                <div class="row mt-2">                              
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
                            <option <?=($ordenacao == 'vi.tipo,vi.numero') ? 'selected' : '';?> value='vi.tipo,vi.numero'>Tipo</option>
                            <option <?=($ordenacao == 'vi.numero,vi.tipo') ? 'selected' : '';?> value='vi.numero,vi.tipo'>Número</option>
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

<script src="../vistoria/vsFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#txTipo").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND (vi.idAeroporto = 0 OR vi.idAeroporto = "+$("#hdAeroporto").val()+")" : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pslTipo":
                            filtro += " AND vi.tipo = '"+$("#pslTipo").val()+"'";
                            descricaoFiltro += " <br>Tipo : "+$("#pslTipo :selected").text();
                        break;
                        case "ptxNumero":
                            filtro +=  " AND vi.numero = '"+$("#ptxNumero").val()+"'";
                            descricaoFiltro += " <br>Número : "+$("#ptxNumero").val();
                        break;
                        case "ptxItem":
                            filtro += " AND vi.item LIKE '%"+$("#ptxItem").val()+"%'";
                            descricaoFiltro += " <br>Item : "+$("#ptxItem").val();
                        break;
                        case "pslSituacao":
                            filtro += " AND vi.situacao = '"+$("#pslSituacao").val()+"'";
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
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsCI_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsCI_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsCI_descricao', descricaoFiltro);

            await vsCarregarVistoriaItens('Cadastrar'+$('#hdUsuarioGrupo').val(), filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txTipo").focus();
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
            $("#txTipo").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_vsCI_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_vsCI_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_vsCI_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxTipo").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodos('VistoriaItensTipos','#pslTipo','','','Consulta');
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consulta');
        });
        $("#ptxNumero").mask('YY.YY', {'translation': {Y: {pattern: /[0-9]/},}});

        // Adequações para o cadastro
        await suCarregarSelectTodos('VistoriaItensTipos','#slTipo',$('#hdTipo').val(),'','Cadastrar');
        await suCarregarSelectTodas('TodosSituacao','#slSituacao',$('#hdSituacao').val(),'','Cadastrar');
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND (vi.idAeroporto = 0 OR vi.idAeroporto = "+$("#hdAeroporto").val()+")": "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }
        await vsCarregarVistoriaItens('Cadastrar'+$('#hdUsuarioGrupo').val(), pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
        $("#txNumero").mask('YY.YY', {'translation': {Y: {pattern: /[0-9]/},}});
        $("#txTipo").focus();
    });
</script>