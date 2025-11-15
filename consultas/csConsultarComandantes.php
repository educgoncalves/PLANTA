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
$usuario = $_SESSION['plantaUsuario'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_csCC_ordenacao','co.codigoAnac');
metaTagsBootstrap('');
$titulo = "Comandantes";
?>
<head>
    <title><?=$_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <form action="#" method="POST"  class="form-group" autocomplete="off"> 
            <?php barraFuncoesCadastro($titulo,array("","X","","X","X","","","","","","","","X")); ?>           
	    	<div class="form-group">
                <!-- Campos hidden -->
                <input type="hidden" name="usuario" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" name="siglaAeroporto" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>

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
                <h5 class="modal-title" id="sobreLabel">Pesquisar <?=$titulo ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label for="ptxCodigoAnac">Código ANAC</label>
                        <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxCodigoAnac"/>
                    </div>
                </div>
                <div class="row mt-2">  
                    <div class="col-md-12">
                        <label for="ptxNome">Nome</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNome"/>
                    </div>
                </div>
                <div class="row mt-2">                              
                    <div class="col-md-4">
                        <label for="ptxTelefone">Telefone</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxTelefone"/>
                    </div>
                    <div class="col-md-8">
                        <label for="ptxEmail">Email</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxEmail"/>
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
                            <option <?=($ordenacao == 'co.codigoAnac') ? 'selected' : '';?> value='co.codigoAnac'>Código ANAC</option>
                            <option <?=($ordenacao == 'co.nome,co.codigoAnac') ? 'selected' : '';?> value='co.nome,co.codigoAnac'>Nome</option>
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
                        case "ptxCodigoAnac":
                            filtro += " AND co.codigoAnac LIKE '%"+$("#ptxCodigoAnac").val()+"%'";
                            descricaoFiltro += " <br>Código ANAC : "+$("#ptxCodigoAnac").val();
                        break;
                        case "ptxNome":
                            filtro += " AND co.nome LIKE '%"+$("#ptxNome").val()+"%'";
                            descricaoFiltro += " <br>Nome : "+$("#ptxNome").val();
                        break;
                        case "ptxTelefone":
                            filtro += " AND co.telefone LIKE '%"+$("#ptxTelefone").val()+"%'";
                            descricaoFiltro += " <br>Telefone : "+$("#ptxTelefone").val();
                        break;
                        case "ptxEmail":
                            filtro += " AND co.email LIKE '%"+$("#ptxEmail").val()+"%'";
                            descricaoFiltro += " <br>Email : "+$("#ptxEmail").val();
                        break; 
                        case "pslSituacao":
                            filtro += " AND co.situacao = '"+$("#pslSituacao").val()+"'";
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
            
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCC_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCC_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_csCC_descricao', descricaoFiltro);

            await cdCarregarComandantes('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
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
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_csCC_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_csCC_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_csCC_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#ptxIcao").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
        });
        $("#ptxTelefone").mask('+YY YY YYYYY-YYYY', {'translation': {Y: {pattern: /[0-9]/},}});
        $("#ptxCodigoAnac").mask('YYYYYY', {'translation': {Y: {pattern: /[0-9]/}}});

        // Adequações para o cadastro
        await cdCarregarComandantes('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
    });
</script>