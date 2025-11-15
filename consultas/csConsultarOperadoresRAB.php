<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../cadastros/cdFuncoes.php");
require_once("../modais/mdModais.php");
verificarExecucao();

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']);    

// Recuperando as informações do Aeroporto
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$funcao = '';
$operadorRAB = '';

// Verificar se foi enviando dados via POST ou inicializa as variáveis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
} else  {
    $id = carregarGets('id'); 
    $operadorRAB = carregarGets('operadorRAB');
}

// Visualizar Matrículas ou Cobrança
$funcao = (isset($_REQUEST["evento"]) && 
            ($_REQUEST["evento"] == "matriculas" || $_REQUEST["evento"] == "cobranca") && $id != "" ? $_REQUEST["evento"] : "");

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_csCOR_ordenacao','op.operador,op.cpfCnpj');
metaTagsBootstrap('');
$titulo = "Operadores Aéreos - RAB";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <form action="#" method="POST"  class="form-group" autocomplete="off"> 
            <?php barraFuncoesCadastro($titulo,array("","X","","X","X","","","","","","","","X")); ?>           
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdOperadorRAB" <?="value=\"{$operadorRAB}\"";?>/>
                <input type="hidden" id="hdFuncao" name="funcao" <?="value=\"{$funcao}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <?php destacarTarefaRAB(); ?>                  
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

<?php modalVisualizar(); ?>

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
                        <label for="ptxCpfCnpj">CPF/CNPJ</label>
                        <input type="text" class="form-control cpoCookie caixaAlta cpoLimpar input-lg" id="ptxCpfCnpj" maxlength="14"/>  
                    </div>                    
                    <div class="col-md-6">
                        <label for="ptxOperador">Nome curto</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxOperador"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label for="ptxNome">Nome completo</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNome"/>
                    </div>
                <div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label for="ptxIcao">ICAO</label>
                        <input type="text" class="form-control cpoCookie caixaAlta cpoLimpar input-lg" id="ptxIcao"/>
                    </div>    
                    <div class="col-md-4">
                        <label for="ptxIata">IATA</label>
                        <input type="text" class="form-control cpoCookie caixaAlta cpoLimpar input-lg" id="ptxIata"/>
                    </div>    
                    <div class="col-md-4">
                        <label for="pslGrupo">Grupo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslGrupo">
                        </select> 
                    </div>                                          

                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxMatriz">Matriz</label>
                        <input type="text" class="form-control cpoCookie caixaAlta cpoLimpar input-lg" id="ptxMatriz"/>
                    </div>
                    <div class="col-md-6">
                        <label for="pslFonte">Fonte</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslFonte" name="fonte">
                        </select> 
                    </div> 
                </div>
                <div class="row mt-2">                      
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
                            <option <?php echo ($ordenacao == 'op.operador,op.cpfCnpj') ? 'selected' : '';?> value='op.operador,op.cpfCnpj'>Nome curto</option>
                            <option <?php echo ($ordenacao == 'op.nome,op.operador,op.cpfCnpj') ? 'selected' : '';?> value='op.nome,op.operador,op.cpfCnpj'>Nome completo</option>
                            <option <?php echo ($ordenacao == 'op.cpfCnpj,op.operador') ? 'selected' : '';?> value='op.cpfCnpj,op.operador'>CPF/CNPJ</option>
                        </select> 
                    </div>
                </div>
                <br>
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
        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
        
        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxOperador":
                            filtro += " AND op.operador LIKE '%"+$("#ptxOperador").val()+"%'";
                            descricaoFiltro += " <br>Nome curto : "+$("#ptxOperador").val();
                        break;
                        case "ptxNome":
                            filtro += " AND op.nome LIKE '%"+$("#ptxNome").val()+"%'";
                            descricaoFiltro += " <br>Nome completo : "+$("#ptxNome").val();
                        break;
                        case "ptxIata":
                            filtro += " AND op.iata LIKE '%"+$("#ptxIata").val()+"%'";
                            descricaoFiltro += " <br>IATA : "+$("#ptxIata").val();
                        break;
                        case "ptxIcao":
                            filtro += " AND op.icao LIKE '%"+$("#ptxIcao").val()+"%'";
                            descricaoFiltro += " <br>ICAO : "+$("#ptxIcao").val();
                        break;
                        case "pslGrupo":
                            filtro += " AND op.grupo = '"+$("#pslGrupo").val()+"'";
                            descricaoFiltro += " <br>Grupo : "+$("#pslGrupo :selected").text();
                        break;
                        case "ptxEmail":
                            filtro += " AND op.email LIKE '%"+$("#ptxEmail").val()+"%'";
                            descricaoFiltro += " <br>Email : "+$("#ptxEmail").val();
                        break;
                        case "ptxMatriz":
                            filtro += " AND CONCAT(mt.icao,' - ',mt.operador) LIKE '%"+$("#ptxMatriz").val()+"%'";
                            descricaoFiltro += " <br>Matriz : "+$("#ptxMatriz").val();
                        break;
                        case "ptxCpfCnpj":
                            filtro += " AND op.cpfCnpj LIKE '%"+$("#ptxCpfCnpj").val()+"%'";
                            descricaoFiltro += " <br>CPF/CNPJ : "+$("#ptxCpfCnpj").val();
                        break;
                        case "pslFonte":
                            filtro += " AND CONCAT(op.fonte,' - ',dm3.descricao) = '"+$("#pslFonte").val()+"'";
                            descricaoFiltro += " <br>Fonte : "+$("#pslFonte :selected").text();
                        break;                         
                        case "pslSituacao":
                            filtro += " AND op.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_csCOR_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCOR_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCOR_descricao', descricaoFiltro);

            await cdCarregarOperadoresRAB('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
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

        // Visualizar informações complementares
        switch ($('#hdFuncao').val()) {
            case "matriculas":
                await cdVisualizarMatriculas(" AND mt.idOperador = "+$('#hdId').val());
                $('#botaoVisualizar').trigger('click');
            break;
            case "cobranca":
                await cdVisualizarOperadoresCobranca(" AND opc.id = "+$('#hdId').val(),$('#hdOperadorRAB').val());
                $('#botaoVisualizar').trigger('click');
            break;
        }
                
        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_csCOR_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_csCOR_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_csCOR_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxCpfCnpj").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodos('TodosGrupo','#pslGrupo','','','Consultar');
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodas('FonteOperadores','#pslFonte','','', 'Consultar'); 
        });
        $("#ptxIata").mask('YY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
        $("#ptxIcao").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
       
        // Adequações para o cadastro     
        await cdCarregarOperadoresRAB('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
    });
</script>