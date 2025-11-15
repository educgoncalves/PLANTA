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

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $grupo = carregarPosts('grupo');
    $inicioPMD = carregarPosts('inicioPMD');
    $finalPMD = carregarPosts('finalPMD');
    $domTPO = carregarPosts('domTPO');
    $domTPM = carregarPosts('domTPM');
    $domTPE = carregarPosts('domTPE');
    $intTPO = carregarPosts('intTPO');
    $intTPM = carregarPosts('intTPM');
    $intTPE = carregarPosts('intTPE');
    $domTPOF = carregarPosts('domTPOF');
    $domTPMF = carregarPosts('domTPMF');
    $domTPEF = carregarPosts('domTPEF');
    $intTPOF = carregarPosts('intTPOF');
    $intTPMF = carregarPosts('intTPMF');
    $intTPEF = carregarPosts('intTPEF');
    $situacao = carregarPosts('situacao','ATV');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
         
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Formatação dos campos decimais
// $formatter = new NumberFormatter('pt_BR',  NumberFormatter::DECIMAL);
// $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
// $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
// $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);
// $formatter->setAttribute(NumberFormatter::DECIMAL_ALWAYS_SHOWN, 2);

// Salvando as informações
if ($evento == "salvar") {
	// Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['grupo']); 
    if (!$erros) {
        try {
            $conexao = conexao();
            $domTPO = mudarDecimalMysql($domTPO);
            $domTPM = mudarDecimalMysql($domTPM);
            $domTPE = mudarDecimalMysql($domTPE);
            $intTPO = mudarDecimalMysql($intTPO);
            $intTPM = mudarDecimalMysql($intTPM);
            $intTPE = mudarDecimalMysql($intTPE);
            $domTPOF = mudarDecimalMysql($domTPOF);
            $domTPMF = mudarDecimalMysql($domTPMF);
            $domTPEF = mudarDecimalMysql($domTPEF);
            $intTPOF = mudarDecimalMysql($intTPOF);
            $intTPMF = mudarDecimalMysql($intTPMF);
            $intTPEF = mudarDecimalMysql($intTPEF);
            $inicioPMD = (!isNullOrEmpty($inicioPMD) ? $inicioPMD : 0);
            $finalPMD = (!isNullOrEmpty($finalPMD) ? $finalPMD : 0);
            if ($id != "") {
                $comando = "UPDATE gear_tarifas SET idAeroporto = ".$aeroporto.", grupo = '".$grupo."', inicioPMD = ".$inicioPMD.", finalPMD = ".$finalPMD.
                            ", domTPO = ".$domTPO.", domTPM = ".$domTPM.", domTPE = ".$domTPE.", intTPO = ".$intTPO.", intTPM = ".$intTPM.", intTPE = ".$intTPE.
                            ", domTPOF = ".$domTPOF.", domTPMF = ".$domTPMF.", domTPEF = ".$domTPEF.", intTPOF = ".$intTPOF.", intTPMF = ".$intTPMF.", intTPEF = ".$intTPEF.
                            ", situacao = '".$situacao."', cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO gear_tarifas (idAeroporto, grupo, inicioPMD, finalPMD, domTPO, domTPM, domTPE, intTPO, intTPM, intTPE, ".
                            "domTPOF, domTPMF, domTPEF, intTPOF, intTPMF, intTPEF, situacao, cadastro) VALUES (".
                            $aeroporto.", '".$grupo."', ".$inicioPMD.", ".$finalPMD.", ".
                            $domTPO.", ".$domTPM.", ".$domTPE.", ".$intTPO.", ".$intTPM.", ".$intTPE.", ".
                            $domTPOF.", ".$domTPMF.", ".$domTPEF.", ".$intTPOF.", ".$intTPMF.", ".$intTPEF.", '"
                            .$situacao."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando);               
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_tarifas", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
                                $_SESSION['plantaUsuario'], ($id != "" ? $id  : $conexao->lastInsertId()), $comando);
                    montarMensagem("success",array("Registro ".($id != "" ? "alterado" : "incluído")." com sucesso!"));
                    $id = null;
                    $limparCampos = true;
                } else {
                    throw new PDOException("Não foi possível efetivar esta ".($id != "" ? "alteração" : "inclusão")."!");
                }
            } else {
                throw new PDOException("Não foi possível ".($id != "" ? "alterar" : "incluir")." este registro!");
            } 
        } catch (PDOException $e) {
            montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
        }
    } else {
        montarMensagem("danger", $erros);
    } 
}
    
// Recuperando as informações
if ($evento == "recuperar" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "SELECT * FROM gear_tarifas WHERE id = ".$id;
        $sql = $conexao->prepare($comando);     
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $grupo = $dados['grupo'];
                $inicioPMD = $dados['inicioPMD'];
                $finalPMD = $dados['finalPMD'];
                $domTPO = $dados['domTPO'];
                $domTPM = $dados['domTPM'];
                $domTPE = $dados['domTPE'];
                $intTPO = $dados['intTPO'];
                $intTPM = $dados['intTPM'];
                $intTPE = $dados['intTPE'];
                $domTPOF = $dados['domTPOF'];
                $domTPMF = $dados['domTPMF'];
                $domTPEF = $dados['domTPEF'];
                $intTPOF = $dados['intTPOF'];
                $intTPMF = $dados['intTPMF'];
                $intTPEF = $dados['intTPEF'];
                $situacao = $dados['situacao'];
            }
            $limparCampos = false;
        } else {
            throw new PDOException("Não foi possível recuperar este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "DELETE FROM gear_tarifas WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_tarifas", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $id, $comando);            
            montarMensagem("success",array("Registro excluído com sucesso!"));
            $id = null;
            $limparCampos = true;
        } else {
            throw new PDOException("Não foi possível excluir este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $grupo = null;
    $inicioPMD = null;
    $finalPMD = null;
    $domTPO = null;
    $domTPM = null;
    $domTPE = null;
    $intTPO = null;
    $intTPM = null;
    $intTPE = null;
    $domTPOF = null;
    $domTPMF = null;
    $domTPEF = null;
    $intTPOF = null;
    $intTPMF = null;
    $intTPEF = null;
    $situacao = 'ATV';
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adCT_ordenacao','ae.icao,tr.grupo,tr.inicioPMD');            
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
        <form action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;">
            <?php barraFuncoesCadastro($titulo); ?>           
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\""?>/>
                <input type="hidden" class="cpoLimpar" id="hdGrupo" <?="value=\"{$grupo}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>  

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <label for="slGrupo">Grupo</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slGrupo" name="grupo">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="txInicioPMD">Faixa de PMD (TON)</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txInicioPMD" name="inicioPMD"
                                    <?php echo (!isNullOrEmpty($inicioPMD)) ? "value=\"{$inicioPMD}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txFinalPMD">até</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="txFinalPMD" name="finalPMD"
                                    <?php echo (!isNullOrEmpty($finalPMD)) ? "value=\"{$finalPMD}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="slSituacao">Situação</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slSituacao" name="situacao">
                                </select> 
                            </div>                            
                        </div>
                        <div class="row mt-4"><div class="col-md-12 text-center"><strong>Valores Fixos</strong></div></div>       
                        <div class="row mt"> 
                            <div class="col-md-6 text-center"><strong>____________________ Doméstico __________________________</strong></div>        
                            <div class="col-md-6 text-center"><strong>____________________ Internacional ______________________</strong></div>
                        </div>  
                        <div class="row"> 
                            <div class="col-md-2">
                                <label for="txDomTPOF">TPO (Pouso)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txDomTPOF" name="domTPOF" size=10
                                    <?php echo (!isNullOrEmpty($domTPOF)) ? "value=\"{$domTPOF}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txDomTPMF">TPM (Manobra)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txDomTPMF" name="domTPMF" size=10
                                    <?php echo (!isNullOrEmpty($domTPMF)) ? "value=\"{$domTPMF}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txDomTPEF">TPE (Estadia)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txDomTPEF" name="domTPEF" size=10
                                    <?php echo (!isNullOrEmpty($domTPEF)) ? "value=\"{$domTPEF}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txIntTPOF">TPO (Pouso)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txIntTPOF" name="intTPOF" size=10
                                    <?php echo (!isNullOrEmpty($intTPOF)) ? "value=\"{$intTPOF}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txIntTPMF">TPM (Manobra)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txIntTPMF" name="intTPMF" size=10
                                    <?php echo (!isNullOrEmpty($intTPMF)) ? "value=\"{$intTPMF}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txIntTPEF">TPE (Estadia)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txIntTPEF" name="intTPEF" size=10
                                    <?php echo (!isNullOrEmpty($intTPEF)) ? "value=\"{$intTPEF}\"" : "";?>>
                            </div>
                        </div> 
                        <div class="row mt-4"><div class="col-md-12 text-center"><strong>Valores Variáveis</strong></div></div>  
                        <div class="row mt">
                            <div class="col-md-6 text-center"><strong>____________________ Doméstico __________________________</strong></div>        
                            <div class="col-md-6 text-center"><strong>____________________ Internacional ______________________</strong></div>                        </div>  
                        <div class="row"> 
                            <div class="col-md-2">
                                <label for="txDomTPO">TPO (Pouso)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txDomTPO" name="domTPO" size=10
                                    <?php echo (!isNullOrEmpty($domTPO)) ? "value=\"{$domTPO}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txDomTPM">TPM (Manobra)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txDomTPM" name="domTPM" size=10
                                    <?php echo (!isNullOrEmpty($domTPM)) ? "value=\"{$domTPM}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txDomTPE">TPE (Estadia)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txDomTPE" name="domTPE" size=10
                                    <?php echo (!isNullOrEmpty($domTPE)) ? "value=\"{$domTPE}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txIntTPO">TPO (Pouso)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txIntTPO" name="intTPO" size=10
                                    <?php echo (!isNullOrEmpty($intTPO)) ? "value=\"{$intTPO}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txIntTPM">TPM (Manobra)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txIntTPM" name="intTPM" size=10
                                    <?php echo (!isNullOrEmpty($intTPM)) ? "value=\"{$intTPM}\"" : "";?>>
                            </div>
                            <div class="col-md-2">
                                <label for="txIntTPE">TPE (Estadia)</label>
                                <input type="text" class="form-control cpoLimpar cpoObrigatorio input-lg" id="txIntTPE" name="intTPE" size=10
                                    <?php echo (!isNullOrEmpty($intTPE)) ? "value=\"{$intTPE}\"" : "";?>>
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
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#pslAeroporto").focus();
        });

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

            await criarCookie($('#hdSiglaAeroporto').val()+'_adCT_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCT_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCT_descricao', descricaoFiltro);
            
            await adCarregarTarifas('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#pslAeroporto").focus();
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
            $("#txUsuario").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adCT_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adCT_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adCT_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#pslAeroporto").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){         
            await suCarregarSelectTodos('TodosGrupos','#pslGrupo','','','Consultar');
            await suCarregarSelectTodas('TodosSituacao','#pslSituacao','','','Consultar');
        });
 
        // Adequações para o cadastro  
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND tr.idAeroporto in ("+$("#hdAeroporto").val()+",0)" : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }       
        await suCarregarSelectTodos('TodosGrupos','#slGrupo', $('#hdGrupo').val(),'','Cadastrar');
        await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
        await adCarregarTarifas('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txUsuario").focus();
        $("#txInicioPMD").mask('#.##0', {reverse: true});
        $("#txFinalPMD").mask('#.##0', {reverse: true});
        $("#txDomTPO").mask('###.##0,00', {reverse: true});
        $("#txDomTPM").mask('###.##0,00', {reverse: true});
        $("#txDomTPE").mask('###.##0,00', {reverse: true});
        $("#txIntTPO").mask('###.##0,00', {reverse: true});
        $("#txIntTPM").mask('###.##0,00', {reverse: true});
        $("#txIntTPE").mask('###.##0,00', {reverse: true});
    });
</script>