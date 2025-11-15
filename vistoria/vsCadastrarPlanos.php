<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../vistoria/vsFuncoes.php");
require_once("../suporte/suClassUpload.php");
require_once("../modais/mdModais.php");
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
$mapa = carregarGets('mapa',carregarPosts('mapa'));
$uploadMapa = array('evento'=>$evento, 'mapa'=>$mapa, 'arquivo'=>'../arquivos/mapas/'.$mapa);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id');                // Plano
    $numero = carregarPosts('numero');
    $finalidade = carregarPosts('finalidade');
    $inicio = carregarPosts('inicio');
    $finalidade = carregarPosts('finalidade');
    $frequencia = carregarPosts('frequencia');
    $quantidade = carregarPosts('quantidade');
    $periodo = carregarPosts('periodo');
    $mapa = carregarPosts('mapa');
    $situacao = carregarPosts('situacao');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
     
} else  {
    $id = carregarGets('id');                // Plano
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
	// Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['inicio','finalidade','frequencia','quantidade','situacao']);
    if (!$erros) {
        // Preparando chamada da API apiManterVistoriaItens
        $dados = $_POST;
        $post = ['token'=>$token,'funcao'=>($id != "" ? "Alterar" : "Incluir"),'dados'=>$dados];
        $retorno = executaAPIs('apiManterVistoriaPlanos.php', $post);
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
    $dados = ['tabela'=>'VistoriaPlanos','filtro'=>" AND vp.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $numero = $dados['numero'];
            $finalidade = $dados['finalidade'];
            $inicio = $dados['dtInicio'];
            $frequencia = $dados['frequencia'];
            $quantidade = $dados['quantidade'];
            $periodo = $dados['periodo'];
            $mapa = $dados['mapa'];
            $situacao = $dados['situacao'];
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
    $retorno = executaAPIs('apiManterVistoriaPlanos.php', $post);
    if ($retorno['status'] == 'OK') {
        montarMensagem("success",array($retorno['msg']));
        $id = null;
        $limparCampos = true;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Gerando agendamentos
if ($evento == "agendar" && $id != "") {
    // Preparando chamada da API apiConsultas
    $dados = ['tabela'=>'VistoriaPlanos','filtro'=>" AND vp.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $dados['siglaAeroporto'] = $siglaAeroporto;
            $dados['usuario'] = $usuario;
            $retorno = gerarAgendamentosPlano($dados);
        }
        if ($retorno['status'] == 'OK') {
            montarMensagem("success",array($retorno['msg']));
        } else {
            montarMensagem("danger",array($retorno['msg']));   
        }
        $id = null;
        $limparCampos = true;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Exibindo/Upload do arquivo de mapa
if ($evento == "uploadMapa") {
    $mapaAtual = carregarPosts('mapaAtual');
    if(($_FILES['mapaNovo']['error'] == 0) && !empty($mapaAtual)) {
        $upload = new UploadImagem();
        $upload->width = 500;
        $upload->height = 500;
        $msg = $upload->salvar("../arquivos/mapas/", $_FILES['mapaNovo'], $mapaAtual);
        montarMensagem($msg['tipo'],array($msg['mensagem']));
    } else {
        montarMensagem("danger",array('Upload não pode ser executado!'));
    }
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $numero = null;
    $finalidade = null;
    // Data e hora local do aeroporto
    $inicio = dateTimeUTC($utcAeroporto)->format('Y-m-d');
    $frequencia = null;
    $quantidade = null;
    $periodo = null;
    $mapa = null;
    $situacao = 'APG';
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_vsCP_ordenacao','vp.numero,vp.inicio');
metaTagsBootstrap('');
$titulo = "Planos de Vistoria";
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

                <input type="hidden" id="hdEvento" name="evento" <?="value=\"{$evento}\"";?>/>

                <input type="hidden" class="cpoLimpar" id="hdFrequencia" <?="value=\"{$frequencia}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdQuantidade" <?="value=\"{$quantidade}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdPeriodo" <?="value=\"{$periodo}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>   

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row"> 
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="txNumero">Número</label>
                                <input type="text" class="form-control cpoLimpar caixaAlta input-lg" readonly="true" id="txNumero" name="numero"
                                    <?=(!isNullOrEmpty($numero)) ? "value=\"{$numero}\"" : "";?>/>
                            </div>
                        </div>     
                        <div class="row mt-2">
                            <div class="col-md-8">
                                <label for="txFinalidade">Finalidade</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txFinalidade" name="finalidade"
                                    <?=(!isNullOrEmpty($finalidade)) ? "value=\"{$finalidade}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="txMapa">Mapa</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txMapa" name="mapa"
                                    <?=(!isNullOrEmpty($mapa)) ? "value=\"{$mapa}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-2"> 
                                <label for="dtInicio">Início</label>
                                <input type="date" class="form-control cpoObrigatorio cpoLimpar input-lg" id="dtInicio" name="inicio" 
                                    <?php echo (!isNullOrEmpty($inicio)) ? "value=\"{$inicio}\"" : "";?>>
                            </div>    
                            <div class="col-md-2">
                                <label for="slFrequencia">Frequência</label>
                                <select class="form-select cpoLimpar cpoObrigatorio input-lg" id="slFrequencia" name="frequencia">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="slQuantidade">Quantidade</label>
                                <select class="form-select cpoLimpar cpoObrigatorio input-lg" id="slQuantidade" name="quantidade">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="slPeriodo">Período</label>
                                <select class="form-select cpoLimpar cpoObrigatorio input-lg" id="slPeriodo" name="periodo">
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
<!-- Modal MAPA -->
<!-- *************************************************** -->
<?php modalMapa($uploadMapa);?>

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
                        <label for="ptxNumero">Número</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxNumero"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <label for="ptxFinalidade">Finalidade</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxFinalidade"/>
                    </div>
                </div>
                <div class="row mt-2">  
                    <div class="col-md-4">
                        <label for="pslFrequencia">Frequência</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslFrequencia">
                        </select> 
                    </div>
                    <div class="col-md-4">
                        <label for="pslQuantidade">Quantidade</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslQuantidade">
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
                            <option <?=($ordenacao == 'vp.numero, dtInicio') ? 'selected' : '';?> value='vp.numero, dtInicio'>Número</option>
                            <option <?=($ordenacao == 'dtInicio, vp.numero') ? 'selected' : '';?> value='dtInicio, vp.numero'>Data Início</option>
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
            $("#txFinalidade").focus();
        });
        
        // Chamando modal para eventos
        if ($('#hdEvento').val() == "mapa") { $('#botaoMapa').trigger('click'); }

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND vp.idAeroporto = "+$("#hdAeroporto").val() : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxNumero":
                            filtro +=  " AND vp.numero = '"+$("#ptxNumero").val()+"'";
                            descricaoFiltro += " <br>Número : "+$("#ptxNumero").val();
                        break;
                        case "ptxFinalidade":
                            filtro += " AND vp.finalidade LIKE '%"+$("#ptxFinalidade").val()+"%'";
                            descricaoFiltro += " <br>Finalidade : "+$("#ptxFinalidade").val();
                        break;
                        case "pslFrequencia":
                            filtro += " AND vp.frequencia = '"+$("#pslFrequencia").val()+"'";
                            descricaoFiltro += " <br>Frequência : "+$("#pslFrequencia :selected").text();
                        break;
                        case "pslQuantidade":
                            filtro += " AND vp.quantidade = '"+$("#pslQuantidade").val()+"'";
                            descricaoFiltro += " <br>Quantidade : "+$("#pslQuantidade :selected").text();
                        break;
                        case "pslSituacao":
                            filtro += " AND vp.situacao = '"+$("#pslSituacao").val()+"'";
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
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsCP_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsCP_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsCP_descricao', descricaoFiltro);

            await vsCarregarVistoriaPlanos('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txFinalidade").focus();
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
            $("#txFinalidade").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_vsCP_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_vsCP_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_vsCP_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxNumero").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('VistoriaPlanosFrequencia','#pslFrequencia','','','Consulta');
            await suCarregarSelectTodas('VistoriaPlanosQuantidade','#pslQuantidade','','','Consulta');
            await suCarregarSelectTodas('VistoriaPlanosPeriodo','#pslPeriodo','','','Consulta');
            await suCarregarSelectTodas('VistoriaPlanosSituacao','#pslSituacao','','','Consulta');
        });

        // Adequações para o cadastro
        await suCarregarSelectTodas('VistoriaPlanosFrequencia','#slFrequencia',$('#hdFrequencia').val(),'','Cadastrar');
        await suCarregarSelectTodas('VistoriaPlanosQuantidade','#slQuantidade',$('#hdQuantidade').val(),'','Cadastrar');
        await suCarregarSelectTodas('VistoriaPlanosPeriodo','#slPeriodo',$('#hdPeriodo').val(),'','Cadastrar');
        await suCarregarSelectTodas('VistoriaPlanosSituacao','#slSituacao',$('#hdSituacao').val(),
            ($('#hdSituacao').val() == 'APG' ? " AND codigo = 'APG'" : ''),'Cadastrar');
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND vp.idAeroporto = "+$("#hdAeroporto").val() : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');
        }
        await vsCarregarVistoriaPlanos('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
        $("#txFinalidade").focus();

        $('#slFrequencia').change(async function(){
            var filtro = ($("#slFrequencia").val() != "D" ? " AND codigo = 1" : "")
            await suCarregarSelectTodas('VistoriaPlanosQuantidade','#slQuantidade',$('#hdQuantidade').val(),filtro,'Cadastrar');
            filtro = ($('#slQuantidade').val() > 2 ? " AND codigo <> 'L'" : " AND codigo = 'L'");
            await suCarregarSelectTodas('VistoriaPlanosPeriodo','#slPeriodo',$('#hdPeriodo').val(),filtro,'Cadastrar');
		});

        $('#slQuantidade').change(async function(){
            var filtro = ($('#slQuantidade').val() > 2 ? " AND codigo <> 'L'" : " AND codigo = 'L'");
            await suCarregarSelectTodas('VistoriaPlanosPeriodo','#slPeriodo',$('#hdPeriodo').val(),filtro,'Cadastrar');
		});
    });
</script>