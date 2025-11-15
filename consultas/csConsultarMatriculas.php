<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

// Controle de paginação
$_page = isset($_GET['page']) ? $_GET['page'] : 1;
$_paginacao = isset($_GET['paginacao']) ? $_GET['paginacao'] : 'NAO'; 
$_limite = isset($_GET['limite']) ? $_GET['limite'] : $_SESSION['plantaRegPorPagina'];  

// Recuperando as informações do Aeroporto

$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_csCM_ordenacao','mt.matricula');
metaTagsBootstrap('');
$titulo = "Matrículas";
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
                        <label for="ptxMatricula">Matrícula</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxMatricula"/>
                    </div>
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="ptxOperador">Operador Aéreo</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxOperador"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="ptxEquipamento">Equipamento</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxEquipamento"/>
                    </div>                            
                    <div class="col-md-6">
                        <label for="ptxAssentos">Assentos</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxAssentos" maxlength="3"/>
                    </div>  
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="ptxPmd">PMD</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxPmd" maxlength="7"/>
                    </div>  
                    <div class="col-md-6">
                        <label for="pslCategoria">Categoria</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslCategoria">
                        </select> 
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
                            <option <?php echo ($ordenacao == 'mt.matricula') ? 'selected' : '';?> value='mt.matricula'>Matrícula</option>
                            <option <?php echo ($ordenacao == 'op.nome,mt.matricula') ? 'selected' : '';?> value='op.nome,mt.matricula'>Operador</option>
                            <option <?php echo ($ordenacao == 'eq.modelo,mt.matricula') ? 'selected' : '';?> value='eq.modelo,mt.matricula'>Modelo</option>
                            <option <?php echo ($ordenacao == 'eq.fabricante,mt.matricula') ? 'selected' : '';?> value='eq.fabricante,mt.matricula'>Fabricante</option>
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
        
        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "ptxMatricula":
                            filtro += " AND mt.matricula LIKE '%"+$("#ptxMatricula").val()+"%'";
                            descricaoFiltro += " <br>Matrícula : "+$("#ptxMatricula").val();
                        break;
                        case "ptxOperador":
                            filtro +=  " AND CONCAT(op.icao, ' - ', op.operador) LIKE '%"+$("#ptxOperador").val()+"%'";
                            descricaoFiltro += " <br>Operador : "+$("#ptxOperador").val();
                        break;
                        case "ptxEquipamento":
                            filtro += " AND CONCAT(eq.equipamento,eq.modelo,eq.fabricante) LIKE '%"+$("#ptxEquipamento").val()+"%'";
                            descricaoFiltro += " <br>Equipamento : "+$("#ptxEquipamento").val();
                        break;
                        case "ptxAssentos":
                            filtro += " AND mt.assentos = "+$("#ptxAssentos").val();
                            descricaoFiltro += " <br>Assentos : "+$("#ptxAssentos").val();
                        break;
                        case "ptxPmd":
                            filtro += " AND mt.pmd = "+$("#ptxPmd").val();
                            descricaoFiltro += " <br>PMD : "+$("#ptxPmd").val();
                        break;
                        case "pslCategoria":
                            filtro += " AND mt.categoria = '"+$("#pslCategoria").val()+"'";
                            descricaoFiltro += " <br>Categoria : "+$("#pslCategoria :selected").text();
                        break;   
                        case "pslFonte":
                            filtro += " AND CONCAT(mt.fonte,' - ',dm3.descricao) = '"+$("#pslFonte").val()+"'";
                            descricaoFiltro += " <br>Fonte : "+$("#pslFonte :selected").text();
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_csCM_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCM_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCM_descricao', descricaoFiltro);

            await cdCarregarMatriculas('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
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
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_csCM_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_csCM_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_csCM_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxMatricula").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('MatriculasCategoria','#pslCategoria','','','Consultar');
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
            await suCarregarSelectTodas('FonteMatriculas','#pslFonte','','', 'Consultar');        
        });
        $("#ptxAssentos").mask('##0', {reverse: true});
        $("#ptxPmd").mask('######0', {reverse: true});

        // Adequações para o cadastro        
        await cdCarregarMatriculas('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
    });
</script>