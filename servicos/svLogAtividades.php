<?php 
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']);  

// Recuperando as informações do Site
$utcSite = $_SESSION['plantaUTCSite'];
$siglaSite = $_SESSION['plantaSite'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = "show"; //($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Montando os cookies
$dataInicio = carregarPosts("dataInicio");
$dataFinal = carregarPosts("dataFinal");
$tabela = carregarPosts("tabela");
$operacao = carregarPosts("operacao");
$site = carregarPosts("site");
$usuario = carregarPosts("usuario");
$registro = carregarPosts("registro");
$comando = carregarPosts("comando");
$observacao = carregarPosts("observacao");

// Adicionais 
$filtro = carregarCookie($siglaSite.'_svPL_filtro','');
$descricao = carregarCookie($siglaSite.'_svPL_descricao','');
$ordenacao = carregarCookie($siglaSite.'_svPL_ordenacao','lg.cadastro desc');

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Log de Atividades";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <form action="#" method="POST"  class="form-group" autocomplete="off">
            <?php barraFuncoesCadastro($titulo,array("","","X","","X","","X","","","","","","X")); ?>  
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdSiglaSite" <?="value=\"{$siglaSite}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSite" <?="value=\"{$site}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdTabela" <?="value=\"{$tabela}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdOperacao" <?="value=\"{$operacao}\"";?>/>
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">                 
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="dtDataInicio">Data início</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtDataInicio" size="10" name="dataInicio" 
                                    <?php echo (!isNullOrEmpty($dataInicio)) ? "value=\"{$dataInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtDataFinal">Data final</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtDataFinal" size="10" name="dataFinal"
                                    <?php echo (!isNullOrEmpty($dataFinal)) ? "value=\"{$dataFinal}\"" : "";?>/>
                            </div>                    
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="slTabela">Tabela</label>
                                <select class="form-select cpoCookie selLimpar input-lg" id="slTabela" name="tabela">
                                </select>                            
                            </div>
                            <div class="col-md-2">
                                <label for="slOperacao">Operação</label>
                                <select class="form-select cpoCookie selLimpar input-lg" id="slOperacao" name="operacao">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="slSite">Site</label>
                                <select class="form-select cpoCookie selLimpar input-lg" id="slSite" name="site">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="slUsuario">Usuário</label>
                                <select class="form-select cpoCookie selLimpar input-lg" id="slUsuario" name="usuario">
                                </select>                            
                            </div>
                            <div class="col-md-2">
                                <label for="txRegistro">Registro</label>
                                <input type="text" class="form-control cpoCookie cpoLimpar input-lg" id="txRegistro" name="registro"
                                    <?php echo (!isNullOrEmpty($registro)) ? "value=\"{$registro}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-5">
                                <label for="txComando">Comando</label>
                                <input type="text" class="form-control cpoCookie cpoLimpar input-lg" id="txComando" name="comando"
                                    <?php echo (!isNullOrEmpty($comando)) ? "value=\"{$comando}\"" : "";?>/>
                            </div>
                            <div class="col-md-5">
                                <label for="txObservacao">Observação</label>
                                <input type="text" class="form-control cpoCookie cpoLimpar input-lg" id="txObservacao" name="observacao"
                                    <?php echo (!isNullOrEmpty($observacao)) ? "value=\"{$observacao}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="slOrdenacao">Ordenação da lista</label>
                                <select class="form-select cpoCookie selLimpar input-lg" id="slOrdenacao" name="ordenacao">
                                    <option <?php echo ($ordenacao == 'lg.cadastro desc') ? 'selected' : '';?> value='lg.cadastro desc'>Data</option>
                                    <option <?php echo ($ordenacao == 'lg.tabela, lg.cadastro desc') ? 'selected' : '';?> value='lg.tabela, lg.cadastro desc'>Tabela</option>
                                    <option <?php echo ($ordenacao == 'lg.site, lg.cadastro desc') ? 'selected' : '';?> value='lg.site, lg.cadastro desc'>Site</option>
                                    <option <?php echo ($ordenacao == 'lg.usuario, lg.cadastro desc') ? 'selected' : '';?> value='lg.usuario, lg.cadastro desc'>Usuário</option>
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

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->      

<script src="../servicos/svFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#dtDataInicio").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
        
        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "dtDataInicio":
                            filtro += ' AND (DATE_FORMAT(data,"%Y-%m-%d")  >= "'+mudarDataAMD($("#dtDataInicio").val())+'"'+
                                ' AND DATE_FORMAT(data,"%Y-%m-%d") <= "'+mudarDataAMD($("#dtDataFinal").val())+'")';
                            descricaoFiltro += ' <br>Período : '+$("#dtDataInicio").val()+' a '+$("#dtDataFinal").val();
                        break;
                        case "slTabela":
                            filtro += " AND lg.tabela = '"+$("#slTabela").val()+"'";
                            descricaoFiltro += ' <br>Tabela : '+$("#slTabela :selected").text();
                        break;
                        case "slOperacao":
                            filtro += " AND lg.operacao = '"+$("#slOperacao").val()+"'";
                            descricaoFiltro += ' <br>Operação : '+$("#slOperacao :selected").text();
                        break;
                        case "slSite":
                            filtro += " AND lg.site = '"+$("#slSite :selected").text().split(' - ')[0]+"'";
                            descricaoFiltro += ' <br>Site : '+$("#slSite :selected").text();
                        break;
                        case "slUsuario":
                            filtro += " AND lg.usuario = '"+$("#slUsuario :selected").text().split(' - ')[0]+"'";
                            descricaoFiltro += ' <br>Usuário : '+$("#slUsuario :selected").text();
                        break;
                        case "txRegistro":
                            filtro += " AND lg.registro = "+$("#txRegistro").val();
                            descricaoFiltro += ' <br>Registro : '+$("#txRegistro").val();
                        break;  
                        case "txComando":
                            filtro += ' AND lg.comando LIKE "%'+$("#txComando").val()+'%"';
                            descricaoFiltro += ' <br>Comando : '+$("#txComando").val();
                        break;  
                        case "txObservacao":
                            filtro += " AND lg.observacao LIKE '%"+$("#txObservacao").val()+"%'";
                            descricaoFiltro += ' <br>Observação : '+$("#txObservacao").val();
                        break;                         
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            }); 

            // Montagem da ordem
            var ordem = $("#slOrdenacao").val();
            
            await criarCookie($('#hdSiglaSite').val()+'_svPL_ordenacao', ordem);
            await criarCookie($('#hdSiglaSite').val()+'_svPL_filtro', filtro);
            await criarCookie($('#hdSiglaSite').val()+'_svPL_descricao', descricaoFiltro);

            await svCarregarLogsAtividades('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#dtDataInicio").focus();
        }

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="'+$("#hdSiglaSite").val()+'">';
            form += '<input type="hidden" name="titulo" value="' + $('#divTitulo').text() + ($('#divPagina').text() != "Paginação" ? " [incompleto]" : "") + '">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divImpressao').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="L">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
            $("#dtDataInicio").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaSite').val()+'_svPL_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaSite').val()+'_svPL_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaSite').val()+'_svPL_descricao');

        // Adequações para o formulario  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaOrdem = 'lg.cadastro desc';
            pesquisaFiltro = "";
            pesquisaDescricao = "";

            await criarCookie($('#hdSiglaSite').val()+'_svPL_ordenacao', pesquisaOrdem);
            await criarCookie($('#hdSiglaSite').val()+'_svPL_filtro', pesquisaFiltro);
            await criarCookie($('#hdSiglaSite').val()+'_svPL_descricao', pesquisaDescricao);
        }  

        await suCarregarSelectTodas('LogsOperacao','#slOperacao', $('#hdOperacao').val(),'','Consultar');
        await suCarregarSelectTodos('LogsTabela','#slTabela', $('#hdTabela').val(),'','Consultar');
        await suCarregarSelectTodos('Usuarios','#slUsuario', $('#hdUsuario').val(),'','Consultar');
        await suCarregarSelectTodos('SitesClientes','#slSite',$('#hdSite').val(),'','Consultar');

        await svCarregarLogsAtividades('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#dtDataInicio").focus();
    });
</script>