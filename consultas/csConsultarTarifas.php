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
$ordenacao = carregarCookie($siglaAeroporto.'_csCT_ordenacao','ae.icao,tr.grupo,tr.inicioPMD');            
metaTagsBootstrap('');
$titulo = "Tarifas";
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
                        <label for="pslGrupo">Grupo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslGrupo">
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
                        <select class="form-select cpoCookie selCookie input-lg" id="pslOrdenacao">
                            <option <?php echo ($ordenacao == 'ae.icao,tr.grupo,tr.inicioPMD') ? 'selected' : '';?> value='ae.icao,tr.grupo,tr.inicioPMD'>Aeroporto</option>
                            <option <?php echo ($ordenacao == 'tr.grupo,tr.inicioPMD,ae.icao') ? 'selected' : '';?> value='tr.grupo,tr.inicioPMD,ae.icao'>Grupo</option>
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
        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND tr.idAeroporto in ("+$("#hdAeroporto").val()+",0)" : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pslGrupo":
                            filtro += " AND tr.grupo = '"+$("#pslGrupo").val()+"'";
                            descricaoFiltro += " <br>Grupo : "+$("#pslGrupo :selected").text();
                        break; 
                        case "pslSituacao":
                            filtro += " AND tr.situacao = '"+$("#pslSituacao").val()+"'";
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

            await criarCookie($('#hdSiglaAeroporto').val()+'_csCT_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCT_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCT_descricao', descricaoFiltro);
            
            await adCarregarTarifas('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
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
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_csCT_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_csCT_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_csCT_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#pslAeroporto").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){         
            //await adCarregarSelectTodosAeroportosClientes('#pslAeroporto','','','Consultar');
            await suCarregarSelectTodos('TodosGrupos','#pslGrupo','','','Consultar');
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
        });

        // Adequações para o cadastro  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND tr.idAeroporto in ("+$("#hdAeroporto").val()+",0)" : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }        
        await adCarregarTarifas('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
    });
</script>