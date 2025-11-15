<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
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
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$propaganda = carregarGets('propaganda',carregarPosts('propaganda'));
$uploadPropaganda = array('evento'=>$evento, 'propaganda'=>$propaganda, 'arquivo'=>'../arquivos/propagandas/'.$propaganda);
$aeroportoDestino = carregarGets('aeroportoDestino',carregarPosts('aeroportoDestino'));
$copiarAeroporto = array('titulo'=>'Copiar propagandas do aeroporto '.$siglaAeroporto,
                        'aviso'=>'O cadastro de propagandas do aeroporto selecionado será sobreposto com as informações
                        deste aeroporto.<br><br>Favor verificar ao final do processo!');

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $empresa = carregarPosts('empresa');
    $propaganda = carregarPosts('propaganda');
    $dtInicio = carregarPosts('dtInicio');
    $dtFinal = carregarPosts('dtFinal');
    $dhExibicao = carregarPosts('dhExibicao');
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
    $erros = camposPreenchidos(['empresa','propaganda','dtInicio','dtFinal']);
    if (!$erros){
        // Preparando chamada da API apiManterPropagandas
        $dados = $_POST;
        $post = ['token'=>$token,'funcao'=>($id != "" ? "Alterar" : "Incluir"),'dados'=>$dados];
        $retorno = executaAPIs('apiManterPropagandas.php', $post);
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
    $dados = ['tabela'=>'Propagandas','filtro'=>" AND pg.id = ".$id,'ordem'=>'','busca'=>''];
    $post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
    $retorno = executaAPIs('apiConsultas.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $empresa = $dados['empresa'];
            $propaganda = $dados['propaganda'];
            $dtInicio = $dados['dtInicio'];
            $dtFinal = $dados['dtFinal'];
            $situacao = $dados['situacao'];
        }
        $limparCampos = false;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }    
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    // Preparando chamada da API apiManterPropagandas
    $dados = ["id"=>$id,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>"Excluir",'dados'=>$dados];
    $retorno = executaAPIs('apiManterPropagandas.php', $post);
    if ($retorno['status'] == 'OK') {
        montarMensagem("success",array($retorno['msg']));
        $id = null;
        $limparCampos = true;
    } else {
        montarMensagem("danger",array($retorno['msg']));
    }
}

// Exibindo/Upload do arquivo de propaganda
if ($evento == "uploadPropaganda") {
    $propagandaAtual = carregarPosts('propagandaAtual');
    if(($_FILES['propagandaNova']['error'] == 0) && !empty($propagandaAtual)) {
        $upload = new UploadImagem();
        $upload->width = 500;
        $upload->height = 500;
        $msg = $upload->salvar("../arquivos/propagandas/", $_FILES['propagandaNova'], $propagandaAtual);
        montarMensagem($msg['tipo'],array($msg['mensagem']));
    } else {
        montarMensagem("danger",array('Upload não pode ser executado!'));
    }
}

// Copiando informações para outro aeroporto
if ($evento == "executarCopiarAeroporto" && $aeroportoDestino != "") {
    $limparCampos = true;
    try {
        $conexao = conexao();
        // Abrindo a transação
		$conexao->beginTransaction();
        $comando = "DELETE FROM gear_propagandas WHERE idAeroporto = ".$aeroportoDestino;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            $comando = "INSERT INTO gear_propagandas (idAeroporto, empresa, propaganda, dtInicio, dtFinal, situacao, cadastro)
                            SELECT ".$aeroportoDestino.", empresa, propaganda, dtInicio, dtFinal, situacao, UTC_TIMESTAMP()
                            FROM gear_propagandas WHERE idAeroporto = ".$aeroporto;
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()){
                gravaDLog("gear_propagandas", "Copiar", $siglaAeroporto, $usuario, $aeroportoDestino, $comando);  
                montarMensagem("success",array("Propagandas copiados com sucesso!")); 
                $conexao->commit();
            } else {
                throw new PDOException("Não foi possível copiar as propagandas para o aeroporto destino!");
            } 
        } else {
            throw new PDOException("Não foi possível excluir as propagandas do aeroporto destino!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
        if ($conexao->inTransaction()) {$conexao->rollBack();}
    }
}

// Limpeza dos campos 
if ($limparCampos == true) {
    $empresa = null;
    $propaganda = null;
    $dtInicio = null;
    $dtFinal = null;
    $situacao = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adCP_ordenacao','pg.empresa,pg.dtInicio,pg.propaganda');    
metaTagsBootstrap('');
$titulo = "Propagandas";
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
                <input type="hidden" name="id" id="hdId" class="cpoLimpar" <?="value=\"{$id}\"";?>/>
                <input type="hidden" name="usuario" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" name="siglaAeroporto" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" name="aeroporto" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                
                <input type="hidden" id="hdEvento" name="evento" <?="value=\"{$evento}\"";?>/>

                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="d-flex justify-content-end pt-4">
                    <a href='?evento=copiarAeroporto' class="btn btn-outline-primary" role="button">Copiar informações para outro Aeroporto</a>
                </div>
                <div class="row pt-2">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">               
                        <div class="row mt-2">  
                            <div class="col-md-5">
                                <label for="txEmpresa">Empresa</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txEmpresa" name="empresa"
                                    <?php echo (!isNullOrEmpty($empresa)) ? "value=\"{$empresa}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">  
                            <div class="col-md-3">
                                <label for="txPropaganda">Propaganda</label>
                                <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txPropaganda" name="propaganda"
                                    <?php echo (!isNullOrEmpty($propaganda)) ? "value=\"{$propaganda}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtDtInicio">Período para Exibição</label>
                                <input type="date" class="form-control cpoObrigatorio cpoLimpar input-lg" id="dtDtInicio" size="21" name="dtInicio"
                                    <?php echo (!isNullOrEmpty($dtInicio)) ? "value=\"{$dtInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtDtFinal"></label>
                                <input type="date" class="form-control cpoObrigatorio cpoLimpar input-lg" id="dtDtFinal" size="21" name="dtFinal"
                                    <?php echo (!isNullOrEmpty($dtFinal)) ? "value=\"{$dtFinal}\"" : "";?>/>
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
<!-- Modal PROPAGANDA -->
<!-- *************************************************** -->
<?php modalPropaganda($uploadPropaganda);?>

<!-- *************************************************** -->
<!-- Modal COPIAR PARA AEROPORTO -->
<!-- *************************************************** -->
<?php modalCopiarAeroporto($copiarAeroporto);?>

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
                        <label for="ptxEmpresa">Empresa</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxEmpresa"/>
                    </div>
                </div>
                <div class="row mt-2">  
                    <div class="col-md-8">
                        <label for="ptxPropaganda">Propaganda</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxPropaganda"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label for="pdtDtInicio">Período de Exibição</label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtDtInicio" size="21"/>
                    </div>
                    <div class="col-md-4">
                        <label for="pdtDtFinal"></label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtDtFinal" size="21"/>
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
                            <option <?php echo ($ordenacao == 'pg.empresa,pg.dtInicio,pg.propaganda') ? 'selected' : '';?> value='pg.empresa,pg.dtInicio,pg.propaganda'>Empresa</option>
                            <option <?php echo ($ordenacao == 'pg.propaganda,pg.dtInicio,pg.empresa') ? 'selected' : '';?> value='pg.propaganda,pg.dtInicio,pg.empresa'>Propaganda</option>
                            <option <?php echo ($ordenacao == 'pg.dtInicio,pg.empresa,pg.propaganda') ? 'selected' : '';?> value='pg.dtInicio,pg.empresa,pg.propaganda'>Exibição</option>
                            <option <?php echo ($ordenacao == 'pg.situacao,pg.dtInicio,pg.empresa') ? 'selected' : '';?> value='pg.situacao,pg.dtInicio,pg.empresa'>Situacão</option>
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
            $("#txEmpresa").focus();
        });

        // Chamando modal para eventos
        if ($('#hdEvento').val() == "propaganda") { $('#botaoPropaganda').trigger('click'); }
        if ($('#hdEvento').val() == "copiarAeroporto") { $('#botaoCopiarAeroporto').trigger('click'); }
        await suCarregarSelectTodos('AeroportosClientes','#pslAeroportoDestino',''," AND ae.id <> "+$("#hdAeroporto").val(),'Cadastrar');

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND pg.idAeroporto = "+$("#hdAeroporto").val() : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxEmpresa":
                            filtro += " AND pg.empresa LIKE '%"+$("#ptxEmpresa").val()+"%'";
                            descricaoFiltro += " <br>Empresa : "+$("#ptxEmpresa").val();
                            break;
                        case "ptxPropaganda":
                            filtro += " AND pg.propaganda LIKE '%"+$("#ptxPropaganda").val()+"%'";
                            descricaoFiltro += " <br>Propaganda : "+$("#ptxPropaganda").val();
                            break;                            
                        case "pdtDtInicio":
                            filtro += " AND NOT (DATE_FORMAT(pg.dtInicio,'%Y-%m-%d')  > '"+mudarDataAMD($("#pdtDtFinal").val())+"'"+
                                        " OR DATE_FORMAT(pg.dtFinal,'%Y-%m-%d') < '"+mudarDataAMD($("#pdtDtInicio").val())+"')"
                            descricaoFiltro += ' <br>Período para Exibição : '+mudarDataDMA($("#pdtDtInicio").val())+' a '+
                                                                            mudarDataDMA($("#pdtDtFinal").val());
                        break; 
                        case "pslSituacao":
                            filtro += " AND pg.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_adCP_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCP_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCP_descricao', descricaoFiltro);
                                    
            await adCarregarPropagandas('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
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
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adCP_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adCP_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adCP_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxEmpresa").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('PropagandasSituacao','#pslSituacao','','','Consultar');
        });
        
        // Adequações para o cadastro    
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND pg.idAeroporto = "+$("#hdAeroporto").val() : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }              
        await suCarregarSelectTodas('PropagandasSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await adCarregarPropagandas('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txEmpresa").focus();
    });
</script>