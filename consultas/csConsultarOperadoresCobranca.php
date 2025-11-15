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
$operadorCOB = '';

// Verificar se foi enviando dados via POST ou inicializa as variáveis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
} else  {
    $id = carregarGets('id'); 
    $operadorCOB = carregarGets('operadorCOB');
}

// Visualizar Matrículas ou Operadores RAB
$funcao = (isset($_REQUEST["evento"]) && 
            ($_REQUEST["evento"] == "matriculas" || $_REQUEST["evento"] == "operadores") && $id != "" ? $_REQUEST["evento"] : "");

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_csCOC_ordenacao','opc.operador,opc.cpfCnpj');
metaTagsBootstrap('');
$titulo = "Operadores Aéreos - Cobrança";
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
                <input type="hidden" class="cpoLimpar" id="hdOperadorCOB" <?="value=\"{$operadorCOB}\"";?>/>
                <input type="hidden" id="hdFuncao" name="funcao" <?="value=\"{$funcao}\"";?>/>
                
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
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxCpfCnpj" maxlength="14"/>  
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
                    <div class="col-md-6">
                        <label for="ptxEndereco">Endereço</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEndereco"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxComplemento">Complemento</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxComplemento"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxBairro">Bairro</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxBairro"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxMunicipio">Município</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxMunicipio"/>
                    </div>
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="ptxCidade">Cidade</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxCidade"/>
                    </div>
                    <div class="col-md-2">
                        <label for="ptxEstado">Estado</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxEstado"/>
                    </div>
                    <div class="col-md-4">
                        <label for="ptxCep">CEP</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="txCep"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxContato">Contato</label>
                        <input type="text" class="form-control cpoCookie cpoLimpar input-lg" id="ptxContato"/>
                    </div>
                    <div class="col-md-6">
                        <label for="ptxEmail">Email</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEmail"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxTelefone">Telefone</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxTelefone"/>  
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
                            <option <?php echo ($ordenacao == 'opc.operador,opc.cpfCnpj') ? 'selected' : '';?> value='opc.operador,opc.cpfCnpj'>Nome curto</option>
                            <option <?php echo ($ordenacao == 'opc.nome,opc.operador,opc.cpfCnpj') ? 'selected' : '';?> value='opc.nome,opc.operador,opc.cpfCnpj'>Nome completo</option>
                            <option <?php echo ($ordenacao == 'opc.cpfCnpj,opc.operador') ? 'selected' : '';?> value='opc.cpfCnpj,opc.operador'>CPF/CNPJ</option>
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
                        case "ptxCpfCnpj":
                            filtro += " AND opc.cpfCnpj LIKE '%"+$("#ptxCpfCnpj").val()+"%'";
                            descricaoFiltro += " <br>CPF/CNPJ : "+$("#ptxCpfCnpj").val();
                        break;
                        case "ptxOperador":
                            filtro += " AND opc.operador LIKE '%"+$("#ptxOperador").val()+"%'";
                            descricaoFiltro += " <br>Nome curto : "+$("#ptxOperador").val();
                        break;
                        case "ptxNome":
                            filtro += " AND opc.nome LIKE '%"+$("#ptxNome").val()+"%'";
                            descricaoFiltro += " <br>Nome completo : "+$("#ptxNome").val();
                        break;
                        case "ptxEndereco":
                            filtro += " AND opc.endereco LIKE '%"+$("#ptxEndereco").val()+"%'";
                            descricaoFiltro += " <br>Endereço : "+$("#ptxEndereco").val();
                        break;
                        case "ptxComplemento":
                            filtro += " AND opc.complemento LIKE '%"+$("#ptxComplemento").val()+"%'";
                            descricaoFiltro += " <br>Complemento : "+$("#ptxComplemento").val();
                        break;
                        case "ptxBairro":
                            filtro += " AND opc.bairro LIKE '%"+$("#ptxBairro").val()+"%'";
                            descricaoFiltro += " <br>Bairro : "+$("#ptxBairro").val();
                        break;
                        case "ptxMunicipio":
                            filtro += " AND opc.municipio LIKE '%"+$("#ptxMunicipio").val()+"%'";
                            descricaoFiltro += " <br>Município : "+$("#ptxMunicipio").val();
                        break;
                        case "ptxCidade":
                            filtro += " AND opc.cidade LIKE '%"+$("#ptxCidade").val()+"%'";
                            descricaoFiltro += " <br>Cidade : "+$("#ptxCidade").val();
                        break;
                        case "ptxEstado":
                            filtro += " AND opc.estado LIKE '%"+$("#ptxEstado").val()+"%'";
                            descricaoFiltro += " <br>Estado : "+$("#ptxEstado").val();
                        break;
                        case "ptxCep":
                            filtro += " AND opc.cep LIKE '%"+$("#ptxCep").val()+"%'";
                            descricaoFiltro += " <br>CEP : "+$("#ptxCep").val();
                        break;
                        case "ptxContato":
                            filtro += " AND opc.contato LIKE '%"+$("#ptxContato").val()+"%'";
                            descricaoFiltro += " <br>Contato : "+$("#ptxContato").val();
                        break;
                        case "ptxEmail":
                            filtro += " AND opc.email LIKE '%"+$("#ptxEmail").val()+"%'";
                            descricaoFiltro += " <br>Email : "+$("#ptxEmail").val();
                        break;
                        case "ptxTelefone":
                            filtro += " AND opc.telefone LIKE '%"+$("#ptxTelefone").val()+"%'";
                            descricaoFiltro += " <br>Telefone : "+$("#ptxTelefone").val();
                        break;
                        case "pslSituacao":
                            filtro += " AND opc.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_csCOC_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCOC_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCOC_descricao', descricaoFiltro);

            await cdCarregarOperadoresCobranca('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
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
            case "operadores":
                await cdVisualizarOperadoresRAB(" AND op.idCobranca = "+$('#hdId').val(),$('#hdOperadorCOB').val());
                $('#botaoVisualizar').trigger('click');
            break;
        }
        
        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_csCOC_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_csCOC_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_csCOC_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxCpfCnpj").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
        });
       
        // Adequações para o cadastro     
        await cdCarregarOperadoresCobranca('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
    });
</script>