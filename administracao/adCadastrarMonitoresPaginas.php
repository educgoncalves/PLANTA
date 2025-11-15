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
    $id = carregarPosts('id');           
    $idMonitor = carregarPosts('idMonitor');
    $pagina = carregarPosts('pagina');
    $acao = carregarPosts('acao');
    $segundos = carregarPosts('segundos');
    $resolucao = carregarPosts('resolucao');
    $situacao = carregarPosts('situacao');  

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    // if (!verificaRequest()) { goto formulario; }
     
} else  {
    $id = carregarGets('id');           
    $idMonitor = carregarGets('idMonitor');
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
	// Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['pagina','acao','segundos','resolucao','situacao']);
    if (!$erros) {
        // Preparando chamada da API apiManterMonitoresPaginas
        $dados = $_POST;
        $post = ['token'=>$token,'funcao'=>($id != "" ? "Alterar" : "Incluir"),'dados'=>$dados];
        $retorno = executaAPIs('apiManterMonitoresPaginas.php', $post);
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
    $dados = ['tabela'=>'MonitoresPaginas','filtro'=>" AND mp.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $idMonitor = $dados['idMonitor'];
            $pagina = $dados['pagina'];
            $acao = $dados['acao'];
            $segundos = $dados['segundos'];
            $resolucao = $dados['resolucao'];
            $situacao = $dados['situacao'];  
        }
        $limparCampos = false;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    // Preparando chamada da API apiManterMonitoresPaginas
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Excluir",'dados'=>$dados];
    $retorno = executaAPIs('apiManterMonitoresPaginas.php', $post);
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
    $pagina = null;
    $acao = null;
    $segundos = 0;
    $resolucao = null;
    $situacao = 'ATV';
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adMTP_ordenacao','identificacao, mp.id');
metaTagsBootstrap('');
$titulo = "Páginas para o monitor";
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
            <?php barraFuncoesCadastro($titulo,array("X","X","X","X","X","X","","","","X","","","X")); ?>   
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" name="usuario" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" name="aeroporto" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" name="siglaAeroporto" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" id="hdEvento" name="evento" <?="value=\"{$evento}\"";?>/>
                
                <input type="hidden" name="id" id="hdId" class="cpoLimpar" <?="value=\"{$id}\"";?>/>
                <input type="hidden" name="idMonitor" id="hdIdMonitor" <?="value=\"{$idMonitor}\"";?>/>

                <input type="hidden" class="cpoLimpar" id="hdResolucao" <?="value=\"{$resolucao}\"";?>/>   
                <input type="hidden" class="cpoLimpar" id="hdAcao" <?="value=\"{$acao}\"";?>/>   
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>   

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row"> 
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="txPagina">Página</label>
                                <input type="text" class="form-control cpoLimpar caixaAlta input-lg" id="txPagina" name="pagina"
                                    <?=(!isNullOrEmpty($pagina)) ? "value=\"{$pagina}\"" : "";?>/>
                            </div>
                        </div>     
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="slAcao">Ação</label>
                                <select class="form-select cpoLimpar input-lg" id="slAcao" name="acao">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="txSegundos">Refresh (seg)</label>
                                <input type="text" class="form-control cpoObrigatorio numLimpar input-lg" id="txSegundos" name="segundos" maxlength="3"
                                    <?php echo (!isNullOrEmpty($segundos)) ? "value=\"{$segundos}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="slResolucao">Resolução</label>
                                <select class="form-select cpoLimpar input-lg" id="slResolucao" name="resolucao">
                                </select>
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
                    <div class="col-md-4">
                        <label for="ptxPagina">Página</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxPagina"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="pslAcao">Ação</label>
                    <select class="form-select cpoCookie selCookie input-lg" id="pslAcao">
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="pslResolucao">Resolução</label>
                    <select class="form-select cpoCookie selCookie input-lg" id="pslResolucao">
                    </select>
                </div>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <label for="ptxSegundos">Refresh (seg)</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxSegundos"/>
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
                            <option <?=($ordenacao == 'pagina') ? 'selected' : '';?> value='pagina'>Página</option>
                            <option <?=($ordenacao == 'mt.localizacao, pagina') ? 'selected' : '';?> value='mt.localizacao,pagina'>Localização</option>
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
            $("#txPagina").focus();
        });

        $("#retornarFormulario").click(function(){ window.location.href = "../administracao/adCadastrarMonitores.php"});

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND mt.idAeroporto = "+$("#hdAeroporto").val()+" AND mt.id = "+$("#hdIdMonitor").val(): "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');
            
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxPagina":
                            filtro +=  " AND mp.pagina LIKE '%"+$("#ptxPagina").val()+"%'";
                            descricaoFiltro += " <br>Página : "+$("#ptxPagina").val();
                        break;
                        case "pslAcao":
                            filtro += " AND mp.acao = '"+$("#pslAcao").val()+"'";
                            descricaoFiltro += " <br>Ação : "+$("#pslAcao :selected").text();
                        break; 
                        case "ptxSegundos":
                            filtro +=  " AND mp.segundos = "+$("#ptxSegundos").val();
                            descricaoFiltro += " <br>Refresh: "+$("#ptxSegundos").val();
                        break;
                        case "pslResolucao":
                            filtro += " AND mp.resolucao = '"+$("#pslResolucao").val()+"'";
                            descricaoFiltro += " <br>Resolução : "+$("#pslResolucao :selected").text();
                        break; 
                        case "pslSituacao":
                            filtro += " AND mt.situacao = '"+$("#pslSituacao").val()+"'";
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
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_adMTP_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adMTP_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adMTP_descricao', descricaoFiltro);

            await adCarregarMonitoresPaginas('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txPagina").focus();
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
            $("#txPagina").focus();
        });
        
        $("#retornarFormulario").click(function(){ window.location.href = "../administracao/adCadastrarMonitores.php?idMonitor="+$("#hdIdMonitor").val(); });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adMTP_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adMTP_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adMTP_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxPagina").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('MonitoresResolucoes','#pslResolucao','','','Cadastrar');
            await suCarregarSelectTodas('MonitoresAcoes','#pslAcao','','','Cadastrar');
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consulta');
        });

        // Adequações para o cadastro
        await suCarregarSelectTodas('MonitoresResolucoes','#slResolucao',$('#hdResolucao').val(),'','Cadastrar');
        await suCarregarSelectTodas('MonitoresAcoes','#slAcao',$('#hdAcao').val(),'','Cadastrar');
        await suCarregarSelectTodas('TodosSituacao','#slSituacao',$('#hdSituacao').val(),'','Cadastrar');
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND mt.idAeroporto = "+$("#hdAeroporto").val()+" AND mt.id = "+$("#hdIdMonitor").val(): "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');
        }
        await adCarregarMonitoresPaginas('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txSegundos").mask('999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txPagina").focus();
    });
</script>