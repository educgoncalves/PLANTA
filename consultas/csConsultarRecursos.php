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
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_csCR_ordenacao','re.tipo,re.recurso');     
metaTagsBootstrap('');
$titulo = "Recursos";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>    
    <div class="container alert alert-padrao" >
        <form action="#" method="POST"  class="form-group" autocomplete="off"> 
            <?php barraFuncoesCadastro($titulo,array("","X","","X","X","","","","","","","","X")); ?>           
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
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
                    <div class="col-md-6">
                        <label for="pslTipo">Tipo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslTipo">
                        </select>
                    </div>     
                </div>
                <div class="row mt-2">                                       
                    <div class="col-md-6">
                        <label for="ptxRecurso">Identificação</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxRecurso"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxDescricao">Descrição</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxDescricao"/>
                    </div>
                </div>
                <div class="row mt-2">                      
                    <div class="col-md-6">
                        <label for="pslUtilizacao">Utilização</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslUtilizacao">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="pslNatureza">Natureza</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslNatureza">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">                       
                    <div class="col-md-6">
                        <label for="pslClasse">Classe</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslClasse">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="pslSentido">Sentido</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSentido">
                        </select>
                    </div>
                </div>
                <div class="row mt-2"> 
                    <div class="col-md-6">
                        <label for="ptxCapacidade">Capacidade</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxCapacidade" maxlength="3"/>
                    </div>  
                    <div class="col-md-6">
                        <label for="pslUnidade">Unidade</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslUnidade">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">     
                    <div class="col-md-4">
                        <label for="ptxEnvergadura">Envergadura</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEnvergadura" maxlength="6"/>
                    </div>                                          
                    <div class="col-md-4">
                        <label for="ptxComprimento">Comprimento</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxComprimento" maxlength="6"/>
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
                            <option <?php echo ($ordenacao == 're.tipo,re.recurso') ? 'selected' : '';?> value='re.tipo,re.recurso'>Tipo</option>
                            <option <?php echo ($ordenacao == 're.recurso,re.tipo') ? 'selected' : '';?> value='re.recurso,re.tipo'>Identificação</option>
                            <option <?php echo ($ordenacao == 're.natureza,re.tipo,re.recurso') ? 'selected' : '';?> value='re.natureza,re.tipo,re.recurso'>Natureza</option>
                            <option <?php echo ($ordenacao == 're.situacao,re.tipo,re.recurso') ? 'selected' : '';?> value='re.situacao,re.tipo,re.recurso'>Situacão</option>
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
        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND re.idAeroporto = "+$("#hdAeroporto").val() : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxRecurso":
                            filtro += " AND re.recurso LIKE '%"+$("#ptxRecurso").val()+"%'";
                            descricaoFiltro += " <br>Identificação : "+$("#ptxRecurso").val();
                        break;
                        case "ptxDescricao":
                            filtro += " AND re.descricao LIKE '%"+$("#ptxDescricao").val()+"%'";
                            descricaoFiltro += " <br>Descrição : "+$("#ptxDescricao").val();
                        break;                            
                        case "pslTipo":
                            filtro += " AND re.tipo = '"+$("#pslTipo").val()+"'";
                            descricaoFiltro += " <br>Tipo : "+$("#pslTipo :selected").text();
                        break;
                        case "pslUtilizacao":
                            filtro += " AND re.utilizacao = '"+$("#pslUtilizacao").val()+"'";
                            descricaoFiltro += " <br>Utilização : "+$("#pslUtilizacao :selected").text();
                        break;
                        case "pslNatureza":
                            filtro += " AND re.natureza = '"+$("#pslNatureza").val()+"'";
                            descricaoFiltro += " <br>Natureza : "+$("#pslNatureza").val();
                        break;
                        case "pslClasse":
                            filtro += " AND re.classe = '"+$("#pslClasse").val()+"'";
                            descricaoFiltro += " <br>Classe : "+$("#pslClasse :selected").text();
                        break;
                        case "pslSentido":
                            filtro += " AND re.sentido = '"+$("#pslSentido").val()+"'";
                            descricaoFiltro += " <br>Sentido : "+$("#pslSentido :selected").text();
                        break;                               
                        case "ptxCapacidade":
                            filtro += " AND re.capacidade = "+$("#ptxCapacidade").val();
                            descricaoFiltro += " <br>Capacidade : "+$("#ptxCapacidade").val();
                        break;
                        case "pslUnidade":
                            filtro += " AND re.unidade = '"+$("#pslUnidade").val()+"'";
                            descricaoFiltro += " <br>Unidade : "+$("#pslUnidade :selected").text();
                        break;
                        case "ptxEnvergadura":
                            filtro += " AND re.envergadura = "+mudarDecimalMysql($("#ptxEnvergadura").val());
                            descricaoFiltro += " <br>Envergadura : "+$("#ptxEnvergadura").val();
                            break;
                        case "ptxComprimento":
                            filtro += " AND re.comprimento = "+mudarDecimalMysql($("#ptxComprimento").val());
                            descricaoFiltro += " <br>Comprimento : "+$("#ptxComprimento").val();
                            break; 
                        case "pslSituacao":
                            filtro += " AND re.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_csCR_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCR_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCR_descricao', descricaoFiltro);
                        
            await adCarregarRecursos('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        }

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="'+$("#hdSiglaAeroporto").val()+'">';
            form += '<input type="hidden" name="titulo" value="' + $('#divTitulo').text() + '">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divImpressao').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="L">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_csCR_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_csCR_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_csCR_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxRecurso").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('RecursosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodos('RecursosSentido','#pslSentido','','','Consultar');
            await suCarregarSelectTodas('RecursosUnidade','#pslUnidade','','','Consultar');
            await suCarregarSelectTodas('RecursosClasse','#pslClasse','','','Consultar');
            await suCarregarSelectTodas('RecursosNatureza','#pslNatureza','','','Consultar');
            await suCarregarSelectTodos('RecursosUtilizacao','#pslUtilizacao','','','Consultar');
            await suCarregarSelectTodos('RecursosTipo','#pslTipo','','','Consultar');
        });
        $("#ptxCapacidade").mask('##0', {reverse: true});
        $("#ptxEnvergadura").mask('##0,00', {reverse: true});
        $("#ptxComprimento").mask('##0,00', {reverse: true});

        // Adequações para o cadastro  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND re.idAeroporto = "+$("#hdAeroporto").val() : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }
        await adCarregarRecursos('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
    });
</script>