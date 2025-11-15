<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../tarefas/trFuncoes.php");
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
    $iata = carregarPosts('iata');
    $icao = carregarPosts('icao');
    $nome = carregarPosts('nome');
    $localidade = carregarPosts('localidade');
    $pais = carregarPosts('pais');
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
    $erros = camposPreenchidos(['icao','nome','localidade','pais','situacao']);
    if (!$erros) {
        // Preparando chamada da API apiManterAeroportos
        $dados = $_POST;
        $post = ['token'=>$token,'funcao'=>($id != "" ? "Alterar" : "Incluir"),'dados'=>$dados];
        $retorno = executaAPIs('apiManterAeroportos.php', $post);
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
    $dados = ['tabela'=>'Aeroportos','filtro'=>" AND ae.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $iata = $dados['iata'];
            $icao = $dados['icao'];
            $nome = $dados['nome'];
            $localidade = $dados['localidade'];
            $pais = $dados['pais'];
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
    // Preparando chamada da API apiManterAeroportos
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Excluir",'dados'=>$dados];
    $retorno = executaAPIs('apiManterAeroportos.php', $post);
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
    $iata = null;
    $icao = null;
    $nome = null;
    $localidade = null;
    $pais = null;
    $situacao = null;
    $txTodosSituacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_cdCA_ordenacao','ae.icao');
metaTagsBootstrap('');
$titulo = "Aeroportos";
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
                                <label for="txIcao">ICAO</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txIcao" name="icao" maxlength="4"
                                    <?=(!isNullOrEmpty($icao)) ? "value=\"{$icao}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txIata">IATA</label>
                                <input type="text" class="form-control cpoLimpar caixaAlta input-lg" id="txIata" name="iata" maxlength="3"
                                    <?=(!isNullOrEmpty($iata)) ? "value=\"{$iata}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">  
                            <div class="col-md-8">
                                <label for="txNome">Nome</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txNome" name="nome" maxlength="250"
                                    <?=(!isNullOrEmpty($nome)) ? "value=\"{$nome}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">  
                            <div class="col-md-3">
                                <label for="txLocalidade">Localidade</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txLocalidade" name="localidade" maxlength="250"
                                    <?=(!isNullOrEmpty($localidade)) ? "value=\"{$localidade}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="txPais">País</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txPais" name="pais" maxlength="250"
                                    <?=(!isNullOrEmpty($pais)) ? "value=\"{$pais}\"" : "";?>/>
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
                <?php destacarTarefaAeroportosANAC(); ?>  
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
                    <div class="col-md-6">
                        <label for="ptxIcao">ICAO</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxIcao"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxIata">IATA</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxIata"/>
                    </div>
                </div>
                <div class="row mt-2">  
                    <div class="col-md-12">
                        <label for="ptxNome">Nome</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNome"/>
                    </div>
                </div>
                <div class="row mt-2">                              
                    <div class="col-md-6">
                        <label for="ptxLocalidade">Localidade</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxLocalidade"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxPais">País</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxPais"/>
                    </div>
                </div>
                <div class="row mt-2">                      
                    <div class="col-md-6">
                        <label for="pslFonte">Fonte</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslFonte">
                        </select> 
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
                            <option <?=($ordenacao == 'ae.icao') ? 'selected' : '';?> value='ae.icao'>ICAO</option>
                            <option <?=($ordenacao == 'ae.iata') ? 'selected' : '';?> value='ae.iata'>IATA</option>
                            <option <?=($ordenacao == 'ae.nome,ae.icao') ? 'selected' : '';?> value='ae.nome,ae.icao'>Nome</option>
                            <option <?=($ordenacao == 'ae.localidade,ae.icao') ? 'selected' : '';?> value='ae.localidade,ae.icao'>Localidade</option>
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
            $("#txIcao").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
        
        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxIcao":
                            filtro += " AND ae.icao LIKE '%"+$("#ptxIcao").val()+"%'";
                            descricaoFiltro += " <br>ICAO : "+$("#ptxIcao").val();
                        break;
                        case "ptxIata":
                            filtro +=  " AND ae.iata LIKE '%"+$("#ptxIata").val()+"%'";
                            descricaoFiltro += " <br>IATA : "+$("#ptxIata").val();
                        break;
                        case "ptxNome":
                            filtro += " AND ae.nome LIKE '%"+$("#ptxNome").val()+"%'";
                            descricaoFiltro += " <br>Nome : "+$("#ptxNome").val();
                        break;
                        case "ptxLocalidade":
                            filtro += " AND ae.localidade LIKE '%"+$("#ptxLocalidade").val()+"%'";
                            descricaoFiltro += " <br>Localidade : "+$("#ptxLocalidade").val();
                        break;
                        case "ptxPais":
                            filtro += " AND ae.pais LIKE '%"+$("#ptxPais").val()+"%'";
                            descricaoFiltro += " <br>País : "+$("#ptxPais").val();
                        break;
                        case "pslFonte":
                            filtro += " AND CONCAT(ae.fonte,' - ',dm2.descricao) = '"+$("#pslFonte").val()+"'";
                            descricaoFiltro += " <br>Fonte : "+$("#pslFonte :selected").text();
                        break;  
                        case "pslSituacao":
                            filtro += " AND ae.situacao = '"+$("#pslSituacao").val()+"'";
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
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCA_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCA_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_cdCA_descricao', descricaoFiltro);

            await cdCarregarAeroportos('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txIcao").focus();
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
            $("#txIcao").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCA_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_cdCA_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_cdCA_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxIcao").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodas('FonteAeroportos','#pslFonte','','','Consultar');
        });
        $("#ptxIata").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
        $("#ptxIcao").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});

        // Adequações para o cadastro
        // await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await cdCarregarAeroportos('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txIata").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
        $("#txIcao").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
        $("#txIcao").focus();
    });
</script>