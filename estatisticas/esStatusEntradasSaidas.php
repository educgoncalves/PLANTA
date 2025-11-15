<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../faturamento/faFuncoes.php");
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
$usuario = $_SESSION['plantaUsuario'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Carregar os posts
$periodoInicio = carregarPosts('periodoInicio'); 
$periodoFinal = carregarPosts('periodoFinal'); 
$grupo = carregarPosts('grupo'); 
$movimento = carregarPosts('movimento'); 
$posicao = carregarPosts('posicao'); 
$utilizacao = carregarPosts('utilizacao'); 
$equipamento = carregarPosts('equipamento'); 
$operador = carregarPosts('operador'); 
$classe = carregarPosts('classe'); 
$natureza = carregarPosts('natureza'); 
$servico = carregarPosts('servico'); 
$grupamentos = carregarPosts('grupamentos'); 

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_esSES_ordenacao','st.id, sm.id');
$grupamento = carregarCookie($siglaAeroporto.'_esSES_grupamento','G-01');
metaTagsBootstrap('');
$titulo = "Estatíticas de Entradas e Saídas";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao">
        <?php barraFuncoesCadastro($titulo, array("","","X","","X","","X","","","X","","X","X")); ?>   
        <form action="#" method="POST" class="form-group" autocomplete="off">
            <div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" id="hdGrupo" <?="value=\"{$grupo}\"";?>/>
                <input type="hidden" id="hdMOvimento" <?="value=\"{$movimento}\"";?>/>
                <input type="hidden" id="hdPosicao" <?="value=\"{$posicao}\"";?>/>
                <input type="hidden" id="hdUtilizacao" <?="value=\"{$utilizacao}\"";?>/>
                <input type="hidden" id="hdClasse" <?="value=\"{$classe}\"";?>/>
                <input type="hidden" id="hdNatureza" <?="value=\"{$natureza}\"";?>/>
                <input type="hidden" id="hdServico" <?="value=\"{$servico}\"";?>/>
                <input type="hidden" id="hdOrdenacao" <?="value=\"{$ordenacao}\"";?>/>
                <input type="hidden" id="hdGrupamento" <?="value=\"{$grupamento}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row pt-2">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">  
                        <div class="row mt-2" >
                            <label for="pdtStsPeriodoInicio">Período</label>
                            <div class="col-md-2">
                                <input type="date" class="form-control cpoCookie input-lg" id="pdtStsPeriodoInicio" size="10" name="periodoInicio" 
                                    <?php echo (!isNullOrEmpty($periodoInicio)) ? "value=\"{$periodoInicio}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control cpoCookie input-lg" id="pdtStsPeriodoFinal" size="10" name="periodoFinal"
                                    <?php echo (!isNullOrEmpty($periodoFinal)) ? "value=\"{$periodoFinal}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="pslStsGrupo">Grupo</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="pslStsGrupo" name="grupo">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="pslStsMovimento">Movimento</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="pslStsMovimento" name="movimento">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="pslStsPosicao">Posição</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="pslStsPosicao" name="posicao">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="pslStsUtilizacao">Utilização</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="pslStsUtilizacao" name="utilizacao">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="ptxStsEquipamento">Equipamento</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxStsEquipamento" name="equipamento"
                                    <?php echo (!isNullOrEmpty($equipamento)) ? "value=\"{$equipamento}\"" : "";?>/>
                            </div>                                                         
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="ptxStsOperador">Operador</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="ptxStsOperador" name="operador"
                                    <?php echo (!isNullOrEmpty($operador)) ? "value=\"{$operador}\"" : "";?>/>
                            </div>
                            <div class="col-md-2">
                                <label for="pslStsClasse">Classe</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="pslStsClasse" name="classe">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="pslStsNatureza">Natureza</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="pslStsNatureza" name="natureza">
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="pslStsServico">Tipo de Serviço</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="pslStsServico" name="servico">
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="pslStsGrupamento">Grupamento</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="pslStsGrupamento" name="grupamento">
                                    <option <?php echo ($grupamento == "G-01") ? 'selected' : '';?> value="G-01">Por ano e mês</option>
                                    <option <?php echo ($grupamento == "G-02") ? 'selected' : '';?> value="G-02">Por ano e semana</option>
                                    <option <?php echo ($grupamento == "G-03") ? 'selected' : '';?> value="G-03">Por mês e dia</option>
                                    <option <?php echo ($grupamento == "G-04") ? 'selected' : '';?> value="G-04">Por dia da semana</option>
                                    <option <?php echo ($grupamento == "G-05") ? 'selected' : '';?> value="G-05">Por data e hora do movimento</option>
                                    <option <?php echo ($grupamento == "G-06") ? 'selected' : '';?> value="G-06">Por grupo de voo</option>
                                    <option <?php echo ($grupamento == "G-07") ? 'selected' : '';?> value="G-07">Por operador aéreo</option>
                                    <option <?php echo ($grupamento == "G-08") ? 'selected' : '';?> value="G-08">Por equipamento</option>
                                    <option <?php echo ($grupamento == "G-09") ? 'selected' : '';?> value="G-09">Por classe do voo</option>
                                    <option <?php echo ($grupamento == "G-10") ? 'selected' : '';?> value="G-10">Por natureza do voo</option>
                                    <option <?php echo ($grupamento == "G-11") ? 'selected' : '';?> value="G-11">Por serviço aéreo</option>
                                    <option <?php echo ($grupamento == "G-12") ? 'selected' : '';?> value="G-12">Por posicao do voo</option>
                                    <option <?php echo ($grupamento == "G-13") ? 'selected' : '';?> value="G-13">Por utilização da posição</option>
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
        <div class="row" >
            <div class="col-md-6">
                <div class="table-responsive" id="divTabela"></div>
            </div>
            <div class="col-md-6">
                <div class="grafico">
                    <canvas id="cnvGrafico"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->   

<script src="../ativos/chart_443/chart.umd.min.js"></script>
<script src="../ativos/chart_443/chartjs-plugin-datalabels.min.js"></script>
<script src="../estatisticas/esFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparPesquisa();
            $("#pdtStsPeriodoInicio").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase());});

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
                filtro += " AND st.idAeroporto = "+$("#hdAeroporto").val()+" AND (sm.movimento = 'ENT' OR sm.movimento = 'SAI')";
                descricaoFiltro += 'Aeroporto : '+$("#hdNomeAeroporto").val().trim();                       

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pdtStsPeriodoInicio":
                            filtro += " AND (DATE_FORMAT(sm.dhMovimento,'%Y-%m-%d')  >= '"+mudarDataAMD($("#pdtStsPeriodoInicio").val())+"'"+
                                        " AND DATE_FORMAT(sm.dhMovimento,'%Y-%m-%d') <= '"+mudarDataAMD($("#pdtStsPeriodoFinal").val())+"')"
                            descricaoFiltro += ' <br>Período : '+
                                        mudarDataDMA($("#pdtStsPeriodoInicio").val())+' a '+
                                        mudarDataDMA($("#pdtStsPeriodoFinal").val());
                        break;
                        case "pslStsGrupo":
                            filtro += " AND op.grupo = '"+$("#pslStsGrupo").val()+"'";
                            descricaoFiltro += ' <br>Grupo : '+$("#pslStsGrupo :selected").text();
                        break;
                        case "pslStsMovimento":
                            filtro += " AND sm.movimento = '"+$("#pslStsMovimento").val()+"'";
                            descricaoFiltro += ' <br>Movimento : '+$("#pslStsMovimento :selected").text();
                        break;
                        case "pslStsPosicao":
                            filtro += " AND re.id = "+$("#pslStsPosicao").val();
                            descricaoFiltro += ' <br>Posição : '+$("#pslStsPosicao :selected").text();
                        break;
                        case "pslStsUtilizacao":
                            filtro += " AND re.utilizacao = '"+$("#pslStsUtilizacao").val()+"'";
                            descricaoFiltro += ' <br>Utilização : '+$("#pslStsUtilizacao :selected").text();
                        break;
                        case "ptxStsEquipamento":
                            filtro += " AND CONCAT(eq.equipamento,' - ',eq.modelo) LIKE '%"+$("#ptxStsEquipamento").val()+"%'";;
                            descricaoFiltro += ' <br>Equipamento : '+$("#ptxStsEquipamento").val();
                        break;                         
                        case "ptxStsOperador":
                            filtro += " AND op.operador LIKE '%"+$("#ptxStsOperador").val()+"%'";
                            descricaoFiltro += " <br>Operador : "+$("#ptxStsOperador").val();
                        break;
                        case "pslStsNatureza":
                            filtro += " AND st.natureza = '"+$("#pslStsNatureza").val()+"'";
                            descricaoFiltro += ' <br>Natureza : '+$("#pslStsNatureza :selected").text();
                        break;
                        case "pslStsClasse":
                            filtro += " AND st.classe = '"+$("#pslStsClasse").val()+"'";
                            descricaoFiltro += ' <br>Classe : '+$("#pslStsClasse :selected").text();
                        break;
                        case "pslStsServico":
                            filtro += " AND st.servico = '"+$("#pslStsServico").val()+"'";
                            descricaoFiltro += ' <br>Tipo de Serviço : '+$("#pslStsServico :selected").text();
                        break;
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });

            // Montagem da ordem
            var ordem = $("#hdOrdenacao").val();

            // Montagem da ordem
            var grupamento = $("#pslStsGrupamento").val();

            await criarCookie($('#hdSiglaAeroporto').val()+'_esSES_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_esSES_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_esSES_descricao', descricaoFiltro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_esSES_grupamento', grupamento);

            await esStatusEntradasSaidas(filtro, ordem, grupamento, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#pdtStsPeriodoInicio").focus();
        };

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="'+$("#hdSiglaAeroporto").val()+'">';
            form += '<input type="hidden" name="titulo" value="' + $('#divTitulo').text() + '">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divTabela').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="P">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
            $("#pdtStsPeriodoInicio").focus();
        });

        $("#exportarCSV").click(function(){
            exportarTabelaParaCSV('divTabela', $("#hdSiglaAeroporto").val(), $('#divTitulo').text());
            $("#pdtStsPeriodoInicio").focus();
        });

        // Adequações para a pesquisa
        var pesquisaGrupamento = await valorCookie($('#hdSiglaAeroporto').val()+'_esSES_grupamento');
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_esSES_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_esSES_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_esSES_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#pdtStsPeriodoInicio").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });

        await suCarregarSelectTodos('TodosGrupo','#pslStsGrupo', $("#hdGrupo").val(), '', 'Consulta');
        await suCarregarSelectTodos('Movimentos','#pslStsMovimento', $("#hdMovimento").val(), 
            " AND mo.idAeroporto = "+$("#hdAeroporto").val()+
            " AND mo.operacao = 'STA' AND (mo.movimento = 'ENT' OR mo.movimento = 'SAI')", 'Consulta');        
        await suCarregarSelectTodos('Recursos','#pslStsPosicao', $("#hdPosicao").val(),           
            " AND re.idAeroporto = "+$("#hdAeroporto").val()+" AND re.tipo = 'POS'", 'Consulta');
        await suCarregarSelectTodas('RecursosUtilizacao','#pslStsUtilizacao', $("#hdUtilizacao").val(), '', 'Consulta');            
        await suCarregarSelectTodas('Classe','#pslStsClasse', $("#hdClasse").val(), '', 'Consulta');
        await suCarregarSelectTodas('Natureza','#pslStsNatureza', $("#hdNatureza").val(), '', 'Consulta');
        await suCarregarSelectTodos('Servico','#pslStsServico', $("#hdServico").val(), '', 'Consulta');

        // Adequações para o formulario  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaGrupamento = $("#hdGrupamento").val();
            pesquisaOrdem = $("#hdOrdenacao").val();
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND st.idAeroporto = "+$("#hdAeroporto").val()+
                                " AND (sm.movimento = 'ENT' OR sm.movimento = 'SAI')" : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? 'Aeroporto : '+$("#hdNomeAeroporto").val().trim() : ''); 

            await criarCookie($('#hdSiglaAeroporto').val()+'_esSES_ordenacao', pesquisaOrdem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_esSES_filtro', pesquisaFiltro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_esSES_descricao', pesquisaDescricao);
            await criarCookie($('#hdSiglaAeroporto').val()+'_esSES_grupamento', pesquisaGrupamento);
        }  
        await esStatusEntradasSaidas(pesquisaFiltro, pesquisaOrdem, pesquisaGrupamento, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#pdtStsPeriodoInicio").focus();
    });
</script>