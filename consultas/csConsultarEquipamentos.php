<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']);  

// Recuperando as informações do Aeroporto

$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_csCE_ordenacao','eq.equipamento,eq.modelo,eq.fabricante');
metaTagsBootstrap('');
$titulo = "Equipamentos";
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

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <?php destacarTarefaEquipamentosANAC(); ?>  
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
                    <div class="col-md-4">
                        <label for="ptxEquipamento">ICAO</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxEquipamento"/>
                    </div>
                    <div class="col-md-4">
                        <label for="ptxModelo">Modelo</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxModelo"/>
                    </div>
                    <div class="col-md-4">
                        <label for="ptxFabricante">Fabricante</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxFabricante"/>
                    </div> 
                </div>
                <div class="row mt-2">                            
                    <div class="col-md-4">
                        <label for="ptxIataEquipamento">IATA</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxIataEquipamento"/>
                    </div> 
                    <div class="col-md-4">
                        <label for="ptxIcaoCategoria">Categoria</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxIcaoCategoria"/>
                    </div>  
                    <div class="col-md-4">
                        <label for="ptxEnvergadura">Envergadura</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEnvergadura" maxlength="6"/>
                    </div>  
                </div>
                <div class="row mt-2">                                              
                    <div class="col-md-4">
                        <label for="ptxComprimento">Comprimento</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxComprimento" maxlength="6"/>
                    </div> 
                    <div class="col-md-4">
                        <label for="ptxAssentos">Assentos</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxAssentos" maxlength="3"/>
                    </div>    
                    <div class="col-md-4">
                        <label for="pslAsa">Asa</label>
                        <div class="input-group">
                            <select class="form-select cpoCookie selCookie input-lg" id="pslAsa">
                            </select>
                        </div> 
                    </div>                                                       
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-4">
                        <label for="pslTipoMotor">Tipo Motor</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslTipoMotor">
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="pslFonte">Fonte</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslFonte" name="fonte">
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
                            <option <?php echo ($ordenacao == 'eq.equipamento,eq.modelo,eq.fabricante') ? 'selected' : '';?> 
                                        value='eq.equipamento,eq.modelo,eq.fabricante'>Equipamento</option>
                            <option <?php echo ($ordenacao == 'eq.modelo,eq.fabricante,eq.equipamento') ? 'selected' : '';?> 
                                        value='eq.modelo,eq.fabricante,eq.equipamento'>Modelo</option>
                            <option <?php echo ($ordenacao == 'eq.fabricante,eq.equipamento,eq.modelo') ? 'selected' : '';?> 
                                        value='eq.fabricante,eq.equipamento,eq.modelo'>Fabricante</option>
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
        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxEquipamento":
                            filtro += " AND eq.equipamento LIKE '%"+$("#ptxEquipamento").val()+"%'";
                            descricaoFiltro += " <br>Equipamento : "+$("#ptxEquipamento").val();
                            break;
                        case "ptxIataEquipamento":
                            filtro += " AND eq.iataEquipamento = '"+$("#ptxIataEquipamento").val()+"'";
                            descricaoFiltro += " <br>IATA Equipamento : "+$("#ptxIataEquipamento").val();
                            break;
                        case "ptxModelo":
                            filtro += " AND eq.modelo LIKE '%"+$("#ptxModelo").val()+"%'";
                            descricaoFiltro += " <br>Modelo : "+$("#ptxModelo").val();
                            break;
                        case "ptxIcaoCategoria":
                            filtro += " AND eq.icaoCategoria = '"+$("#ptxIcaoCategoria").val()+"'";
                            descricaoFiltro += " <br>Categoria : "+$("#ptxIcaoCategoria").val();
                            break;
                        case "pslTipoMotor":
                            filtro += " AND eq.tipoMotor = '"+$("#pslTipoMotor").val()+"'";
                            descricaoFiltro += " <br>Tipo Motor : "+$("#pslTipoMotor :selected").text();
                            break;
                        case "pslAsa":
                            filtro += " AND eq.asa = '"+$("#pslAsa").val()+"'";
                            descricaoFiltro += " <br>Asa : "+$("#pslAsa :selected").text();
                            break;
                        case "ptxEnvergadura":
                            filtro += " AND eq.envergadura = "+mudarDecimalMysql($("#ptxEnvergadura").val());
                            descricaoFiltro += " <br>Envergadura : "+$("#ptxEnvergadura").val();
                            break;
                        case "ptxComprimento":
                            filtro += " AND eq.comprimento = "+mudarDecimalMysql($("#ptxComprimento").val());
                            descricaoFiltro += " <br>Comprimento : "+$("#ptxComprimento").val();
                            break;                    
                        case "txAssentos":
                            filtro += " AND eq.assentos = "+$("#ptxAssentos").val();
                            descricaoFiltro += " <br>Equipamento : "+$("#ptxAssentos").val();
                            break;                    
                        case "ptxFabricante":
                            filtro += " AND eq.fabricante LIKE '%"+$("#ptxFabricante").val()+"%'";
                            descricaoFiltro += " <br>Fabricante : "+$("#ptxFabricante").val();
                            break;     
                        case "pslFonte":
                            filtro += " AND CONCAT(eq.fonte,' - ',dm4.descricao) = '"+$("#pslFonte").val()+"'";
                            descricaoFiltro += " <br>Fonte : "+$("#pslFonte :selected").text();
                        break;                                                
                        case "pslSituacao":
                            filtro += " AND eq.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_csCE_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCE_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCE_descricao', descricaoFiltro);

            await cdCarregarEquipamentos('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
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
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_csCE_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_csCE_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_csCE_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxEquipamento").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodos('EquipamentosTipoMotor','#pslTipoMotor','','','Consultar');
            await suCarregarSelectTodas('EquipamentosAsa','#pslAsa','','','Consultar');
            await suCarregarSelectTodas('FonteEquipamentos','#pslFonte','','', 'Consulta');
        });
        $("#ptxEquipamento").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
        $("#ptxIataEquipamento").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
        $("#ptxIcaoCategoria").mask('YY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});
        $("#ptxEnvergadura").mask('##0,00', {reverse: true});
        $("#ptxComprimento").mask('##0,00', {reverse: true});
        $("#ptxAssentos").mask('##0', {reverse: true});

        // Adequações para o cadastro
        await cdCarregarEquipamentos('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
    });
</script>