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

// Montando os cookies
$empresa = carregarCookie($siglaAeroporto.'_crPC_empresa');
$nome = carregarCookie($siglaAeroporto.'_crPC_nome');
$documento = carregarCookie($siglaAeroporto.'_crPC_documento');
$area = carregarCookie($siglaAeroporto.'_crPC_area');
$periodoInicio = carregarCookie($siglaAeroporto.'_crPC_periodoInicio');
$periodoFinal = carregarCookie($siglaAeroporto.'_crPC_periodoFinal');
$situacao = carregarCookie($siglaAeroporto.'_crPC_situacao');
$ordenacao = carregarCookie($siglaAeroporto.'_crPC_ordenacao');

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Pesquisar Credenciados";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>    
    <div class="container alert alert-padrao" >
        <h4><?php echo $titulo ?></h4>
        <form action="#" method="POST"  class="form-group" autocomplete="off">
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                <input type="hidden" id="hdSituacao" class="cpoLimpar" <?="value=\"{$situacao}\"";?>/>
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div class ="col-lg-10">                
                        <div class="row mt-2">  
                            <div class="col-md-6">
                                <label for="slEmpresa">Empresa</label>
                                <select class="form-select input-lg" id="slEmpresa" name="slEmpresa" onchange="$('#buscarCredenciados').trigger('click');">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="txNome">Nome</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txNome" name="nome"
                                    <?php echo (!isNullOrEmpty($nome)) ? "value=\"{$nome}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="txDocumento">Documento</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txDocumento" name="documento"
                                    <?php echo (!isNullOrEmpty($documento)) ? "value=\"{$documento}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="slArea">Área</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slArea" name="area">
                                </select> 
                            </div>                            
                        </div>
                        <div class="row mt-2" >
                            <div class="col-md-2">
                                <label for="dtPeriodoInicio">Período de validade</label>
                                <input type="date" class="form-control cpoCookie input-lg" id="dtPeriodoInicio" size="10" name="periodoInicio" 
                                    <?php echo (!isNullOrEmpty($periodoInicio)) ? "value=\"{$periodoInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="dtPeriodoFinal"></label>
                                <input type="date" class="form-control cpoCookie input-lg" id="dtPeriodoFinal" size="10" name="periodoFinal"
                                    <?php echo (!isNullOrEmpty($periodoFinal)) ? "value=\"{$periodoFinal}\"" : "";?>/>
                            </div>   
                            <div class="col-md-3">
                                <label for="slSituacao">Situação</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slSituacao" name="situacao">
                                </select> 
                            </div>
                            <div class="col-md-3">
                                <label for="slOrdenacao">Ordenação da lista</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slOrdenacao" name="ordenacao">
                                    <option <?php echo ($ordenacao == 'em.empresa,pcr.nome') ? 'selected' : '';?> value='em.empresa,pcr.nome'>Empresa</option>
                                    <option <?php echo ($ordenacao == 'pcr.nome') ? 'selected' : '';?> value='pcr.nome'>Nome</option>
                                    <option <?php echo ($ordenacao == 'pcr.validade,em.empresa,pcr.nome') ? 'selected' : '';?> value='pcr.validade,em.empresa,pcr.nome'>Validade</option>
                                </select> 
                            </div>                            
                        </div>
                    </div>  
                    <div class ="col-lg-2">
                        <div class="row pt-2 px-2">
                            <input type="button" class="btn btn-padrao" id="limparFormulario" value="Limpar"/>
                        </div>
                        <div class="row pt-2 px-2">
                            <input type="button" class="btn btn-padrao" id="exportarPDF" value="Imprimir"/>
                        </div>
                        <div class="row pt-2 px-2">
                            <input type="button" class="btn btn-padrao" id="buscarCadastro" value="Buscar"/>
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
</body>
</html>    

<script src="../credenciamento/crFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            processarCookies('apagar',$('#hdSiglaAeroporto').val()+'_crPC');
            $("#slEmpresa").focus();
        });

        $(".caixaAlta").keyup(function(){
            $(this).val($(this).val().toUpperCase());
        });

        // Montagem dos filtros e descrição para buscar o cadastro
        $("#buscarCadastro").click(function(){
            document.getElementById("hdPagina").value = 1;
            buscarCadastro();
        });
        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND em.idAeroporto = "+$("#hdAeroporto").val() : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "slEmpresa":
                            filtro += " AND pcr.empresa = "+$("#slEmpresa").val()+"'";
                            descricaoFiltro += " <br>Empresa : "+$("#slEmpresa :selected").text();
                            break;
                        case "txNome":
                            filtro += " AND pcr.nome LIKE '%"+$("#txNome").val()+"%'";
                            descricaoFiltro += " <br>Nome : "+$("#txNome").val();
                            break;    
                        case "txDocumento":
                            filtro += " AND pcr.documento LIKE '%"+$("#txDocumento").val()+"%'";
                            descricaoFiltro += " <br>Documento : "+$("#txDocumento").val();
                            break; 
                        case "slArea":
                            filtro += " AND pcr.idArea = '"+$("#slArea").val()+"'";
                            descricaoFiltro += " <br>Área : "+$("#slArea :selected").text();
                            break;      
                        case "dtPeriodoInicio":
                            filtro += " AND (DATE_FORMAT(pcr.validade,'%Y-%m-%d')  >= '"+mudarDataAMD($("#dtPeriodoInicio").val())+"'"+
                                        " AND DATE_FORMAT(pcr.validade,'%Y-%m-%d') <= '"+mudarDataAMD($("#dtPeriodoFinal").val())+"')"; 
                            descricaoFiltro += ' <br>Período de Validade : '+mudarDataDMA($("#dtPeriodoInicio").val())+' a '+
                                                                                mudarDataDMA($("#dtPeriodoFinal").val());
                            break; 
                        case "slSituacao":
                            filtro += " AND pcr.situacao = '"+$("#slSituacao").val()+"'";
                            descricaoFiltro += " <br>Situação : "+$("#slSituacao :selected").text();
                            break;                           
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });

            // Montagem da ordem
            var ordem = $("#slOrdenacao").val();

            await processarCookies('criar',$('#hdSiglaAeroporto').val()+'_crPC');
            await crCarregarCredenciados('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slEmpresa").focus();
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
            $("#slEmpresa").focus();
        });

        await suCarregarSelectTodas('Recursos','#slArea', $('#hdArea').val(), " AND re.tipo = 'ARA' AND re.situacao = 'ATV' AND re.idAeroporto = "+$('#hdAeroporto').val(),'Consultar');
        await crCarregarSelectEmpresas('#slEmpresa', $('#hdEmpresa').val(), ' AND em.idAeroporto = '+$('#hdAeroporto').val(), 'Consultar');
        await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Consultar');
        $("#slEmpresa").focus();

        // Executa a paginação
        if ($('#hdPaginacao').val() == 'SIM') {
            buscarCadastro();
        }

        // Interceptar a tecla enter e submeter o botão principal
        //
        document.addEventListener("keypress", function(e) {
            if(e.key === 'Enter') {
                event.preventDefault();
                buscarCadastro();
            }
        });
    });
    
    var behavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        }, options = { onKeyPress: function (val, e, field, options) {
                    field.mask(behavior.apply({}, arguments), options);}};
    $('.phone').mask(behavior, options);
</script>