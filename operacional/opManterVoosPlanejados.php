<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../operacional/opFuncoes.php");
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
    $operador = carregarPosts('operador');
    $empresa = carregarPosts('empresa');
    $numeroVoo = carregarPosts('numeroVoo');
    $equipamento = carregarPosts('equipamento');
    $segunda = carregarPosts('segunda');
    $terca = carregarPosts('terca');
    $quarta = carregarPosts('quarta');
    $quinta = carregarPosts('quinta');
    $sexta = carregarPosts('sexta');
    $sabado = carregarPosts('sabado');
    $domingo = carregarPosts('domingo');
    $assentos = carregarPosts('assentos');
    $siros = carregarPosts('siros');
    $situacaoSiros = carregarPosts('situacaoSiros');
    $dataRegistro = carregarPosts('dataRegistro');
    $inicioOperacao = carregarPosts('inicioOperacao');
    $fimOperacao = carregarPosts('fimOperacao');
    $naturezaOperacao = carregarPosts('naturezaOperacao');
    $numeroEtapa = carregarPosts('numeroEtapa');
    $icaoOrigem = carregarPosts('icaoOrigem');
    $aeroportoOrigem = carregarPosts('aeroportoOrigem');
    $icaoDestino = carregarPosts('icaoDestino');
    $aeroportoDestino = carregarPosts('aeroportoDestino');
    $horarioPartida = carregarPosts('horarioPartida');
    $horarioChegada = carregarPosts('horarioChegada');
    $servico = carregarPosts('servico');
    $objetoTransporte = carregarPosts('objetoTransporte');
    $codeshare = carregarPosts('codeshare');
    $situacao = carregarPosts('situacao');
    $fonte = carregarPosts('fonte');
    $origem = carregarPosts('origem');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
         
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
    // Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['operador','empresa','numeroVoo']); 
    if (!$erros) {
        try {
            $conexao = conexao();
            // if ($id != "") {
            //     $comando = "UPDATE gear_voos_planejados"
            // } else {
            //     $comando = "INSERT INTO gear_voos_planejados
            // }
            $sql = $conexao->prepare($comando);               
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_voos_planejados", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
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
        $comando = "SELECT * FROM gear_voos_planejados WHERE id = ".$id;
        $sql = $conexao->prepare($comando);     
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $operador = $dados['operador'];
                $empresa = $dados['empresa'];
                $numeroVoo = $dados['numeroVoo'];
                $equipamento = $dados['equipamento'];
                $segunda = $dados['segunda'];
                $terca = $dados['terca'];
                $quarta = $dados['quarta'];
                $quinta = $dados['quinta'];
                $sexta = $dados['sexta'];
                $sabado = $dados['sabado'];
                $domingo = $dados['domingo'];
                $assentos = $dados['assentos'];
                $siros = $dados['siros'];
                $situacaoSiros = $dados['situacaoSiros'];
                $dataRegistro = $dados['dataRegistro'];
                $inicioOperacao = $dados['inicioOperacao'];
                $fimOperacao = $dados['fimOperacao'];
                $naturezaOperacao = $dados['naturezaOperacao'];
                $numeroEtapa = $dados['numeroEtapa'];
                $icaoOrigem = $dados['icaoOrigem'];
                $aeroportoOrigem = $dados['aeroportoOrigem'];
                $icaoDestino = $dados['icaoDestino'];
                $aeroportoDestino = $dados['aeroportoDestino'];
                $horarioPartida = $dados['horarioPartida'];
                $horarioChegada = $dados['horarioChegada'];
                $servico = $dados['servico'];
                $objetoTransporte = $dados['objetoTransporte'];
                $codeshare = $dados['codeshare'];
                $situacao = $dados['situacao'];
                $fonte = $dados['fonte'];
                $origem = $dados['origem'];
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
        $comando = "DELETE FROM gear_voos_planejados WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_voos_planejados", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $id, $comando);            
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
    $operador = null;
    $empresa = null;
    $numeroVoo = null;
    $equipamento = null;
    $segunda = null;
    $terca = null;
    $quarta = null;
    $quinta = null;
    $sexta = null;
    $sabado = null;
    $domingo = null;
    $assentos = null;
    $siros = null;
    $situacaoSiros = null;
    $dataRegistro = null;
    $inicioOperacao = null;
    $fimOperacao = null;
    $naturezaOperacao = null;
    $numeroEtapa = null;
    $icaoOrigem = null;
    $aeroportoOrigem = null;
    $icaoDestino = null;
    $aeroportoDestino = null;
    $horarioPartida = null;
    $horarioChegada = null;
    $servico = null;
    $objetoTransporte = null;
    $codeshare = null;
    $situacao = null;
    $fonte = null;
    $origem = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_opMVP_ordenacao','vp.operador,vp.numeroVoo,vp.inicioOperacao,vp.numeroEtapa');
metaTagsBootstrap('');
$titulo = "Voos Planejados";
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
            <?php barraFuncoesCadastro($titulo,array("","X","","X","X","","","","","","","","X")); ?>           
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\""?>/>

                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                <input type="hidden" id="hdOrdenacao" <?="value=\"{$ordenacao}\"";?>/>

                <!-- <input type="hidden" class="cpoLimpar" id="hdOperador" name="hdOperador" <?="value=\"{$operador}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSituacaoSiros" name="hdSituacaoSiros" <?="value=\"{$situacaoSiros}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdNaturezaOperacao" name="hdNaturezaOperacao" <?="value=\"{$naturezaOperacao}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdServico" name="hdServico" <?="value=\"{$servico}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdObjetoTransporte" name="hdObjetoTransporte" <?="value=\"{$objetoTransporte}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdFonte" name="hdFonte" <?="value=\"{$fonte}\"";?>/> -->

                <input type="hidden" class="cpoLimpar" id="hdIcaoOrigem" name="hdIcaoOrigem" <?="value=\"{$icaoOrigem}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdIcaoDestino" name="hdIcaoDestino" <?="value=\"{$icaoDestino}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <!-- <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-8">
                                <label for="slOperador">Operador</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slOperador" name="operador">
                                </select>
                            </div>                            
                        </div>
                        <div class="row mt-2" >
                            <div class="col-md-6">
                                <label for="dtInicioOperacao">Período de Operação</label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtInicioOperacao" size="10" name="inicioOperacao" 
                                    <?php echo (!isNullOrEmpty($inicioOperacao)) ? "value=\"{$inicioOperacao}\"" : "";?>/>
                            </div>
                            <div class="col-md-6">
                                <label for="dtFimOperacao"></label>
                                <input type="date" class="form-control cpoCookie cpoLimpar input-lg" id="dtFimOperacao" size="10" name="fimOperacao"
                                    <?php echo (!isNullOrEmpty($fimOperacao)) ? "value=\"{$fimOperacao}\"" : "";?>/>
                            </div>   
                        </div>   
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="slSituacaoSiros">Situação</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slSituacaoSiros" name="situacaoSiros">
                                </select>
                            </div>                             
                            <div class="col-md-6">
                                <label for="slNaturezaOperacao">Natureza</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slNaturezaOperacao" name="naturezaOperacao">
                                </select>
                            </div>
                        </div>   
                        <div class="row mt-2">                    
                            <div class="col-md-6">
                                <label for="slObjetoTransporte">Objeto de Transporte</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slObjetoTransporte" name="objetoTransporte">
                                </select>
                            </div>  
                            <div class="col-md-6">
                                <label for="slFonte">Fonte</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slFonte" name="fonte">
                                </select> 
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="slServico">Tipo de Serviço</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slServico" name="servico">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">                    
                            <div class="col-md-12">
                                <label for="slIcaoOrigem">Origem</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slIcaoOrigem" name="icaoOrigem">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">                    
                            <div class="col-md-12">
                                <label for="slIcaoDestino">Destino</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slIcaoDestino" name="icaoDestino">
                                </select>
                            </div>
                        </div>                        
                    </div>
                </div> -->
                <?php destacarVoosPlanejados(); ?>  
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
                        <label for="pslOperador">Operador</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslOperador">
                        </select>
                    </div>   
                    <div class="col-md-6">
                        <label for="ptxNumero">Número</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNumero">
                    </div>                          
                </div>
                <div class="row mt-2" >
                    <div class="col-md-6">
                        <label for="pdtInicioOperacao">Período de Operação</label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtInicioOperacao" size="10"/>
                    </div>
                    <div class="col-md-6">
                        <label for="pdtFimOperacao"></label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtFimOperacao" size="10"/>
                    </div>   
                </div>   
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="pslSituacaoSiros">Situação</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSituacaoSiros">
                        </select>
                    </div>                             
                    <div class="col-md-6">
                        <label for="pslNaturezaOperacao">Natureza</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslNaturezaOperacao">
                        </select>
                    </div>
                </div>   
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="pslObjetoTransporte">Objeto de Transporte</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslObjetoTransporte">
                        </select>
                    </div>  
                    <div class="col-md-6">
                        <label for="pslFonte">Fonte</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslFonte">
                        </select> 
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label for="pslServico">Serviço</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslServico">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="ptxIcaoOrigem">Origem</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxIcaoOrigem">
                    </div>
                    <div class="col-md-6">
                        <label for="ptxIcaoDestino">Destino</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxIcaoDestino">
                    </div>
                </div>
                <br>
                <div class="row mt-2">                    
                    <div class="col-md-8">
                        <label for="pslOrdenacao">Ordenação da lista</label>
                        <select class="form-select selCookie input-lg" id="pslOrdenacao">
                            <option <?php echo ($ordenacao == 'vp.operador,vp.numeroVoo,vp.inicioOperacao,vp.numeroEtapa') ? 'selected' : '';?> 
                                                        value='vp.operador,vp.numeroVoo,vp.inicioOperacao,vp.numeroEtapa'>Voo</option>
                            <option <?php echo ($ordenacao == 'horarioOperacao,vp.inicioOperacao,vp.operador,vp.numeroVoo,vp.numeroEtapa') ? 'selected' : '';?> 
                                                        value='horarioOperacao,vp.inicioOperacao,vp.operador,vp.numeroVoo,vp.numeroEtapa'>Planejamento</option>                                                                
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

<script src="../operacional/opFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#slOperador").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            var busca = $("#hdSiglaAeroporto").val();

            // Monta filtro fixo da indentificação do aeroporto
            filtro = " AND vp.idAeroporto = "+$("#hdAeroporto").val();
            descricaoFiltro = ' <br>Aeroporto : '+$("#hdNomeAeroporto").val();
                     
            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pslOperador":
                            filtro += " AND vp.operador = '"+$("#pslOperador").val()+"'";
                            descricaoFiltro += ' <br>Operador : '+$("#pslOperador :selected").text();
                        break; 
                        case "ptxNumero":
                            filtro += " AND vp.numeroVoo = '"+$("#ptxNumero").val()+"'";
                            descricaoFiltro += " <br>Número : "+$("#ptxNumero").val();
                        break;
                        case "pdtInicioOperacao":
                            filtro += " AND NOT (DATE_FORMAT(vp.inicioOperacao,'%Y-%m-%d')  > '"+mudarDataAMD($("#pdtFimOperacao").val())+"'"+
                                        " OR DATE_FORMAT(vp.fimOperacao,'%Y-%m-%d') < '"+mudarDataAMD($("#pdtInicioOperacao").val())+"')"
                            descricaoFiltro += ' <br>Período de Operação : '+mudarDataDMA($("#pdtInicioOperacao").val())+' a '+
                                                                            mudarDataDMA($("#pdtFimOperacao").val());
                        break;                            
                        case "pslSituacaoSiros":
                            filtro += " AND vp.situacaoSiros = '"+$("#pslSituacaoSiros").val()+"'";
                            descricaoFiltro += ' <br>Situação : '+$("#pslSituacaoSiros :selected").text();
                        break;
                        case "pslNaturezaOperacao":
                            filtro += " AND vp.naturezaOperacao = '"+$("#pslNaturezaOperacao").val()+"'";
                            descricaoFiltro += ' <br>Natureza da Operação : '+$("#pslNaturezaOperacao :selected").text();
                        break;  
                        case "pslServico":
                            filtro += " AND vp.servico = '"+$("#pslServico").val()+"'";
                            descricaoFiltro += ' <br>Serviço : '+$("#pslServico :selected").text();
                        break;                        
                        case "pslObjetoTransporte":                            
                            filtro += " AND vp.objetoTransporte = '"+$("#pslObjetoTransporte").val()+"'";
                            descricaoFiltro += ' <br>Objeto de Transporte : '+$("#pslObjetoTransporte :selected").text();
                        break;
                        case "pslFonte":
                            filtro += " AND CONCAT(vp.fonte,' - ',IFNULL(dm4.descricao, vp.origem)) = '"+$("#pslFonte").val()+"'";
                            descricaoFiltro += " <br>Fonte : "+$("#pslFonte :selected").text();
                        break; 
                        break;    
                        case "ptxIcaoOrigem":
                            filtro += " AND vp.icaoOrigem = '"+$("#ptxIcaoOrigem").val()+"'";
                            descricaoFiltro += ' <br>Origem : '+$("#ptxIcaoOrigem").val();
                        break;   
                        case "ptxIcaoDestino":
                            filtro += " AND vp.icaoDestino = '"+$("#ptxIcaoDestino").val()+"'";
                            descricaoFiltro += ' <br>Destino : '+$("#ptxIcaoDestino").val();
                        break;                           
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });

            // Montagem da ordem
            var ordem = $("#pslOrdenacao").val();

            await criarCookie($('#hdSiglaAeroporto').val()+'_opMVP_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opMVP_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opMVP_descricao', descricaoFiltro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opMVP_busca', busca);
            
            await opCarregarVoosPlanejados('Consultar', filtro, ordem, descricaoFiltro, busca, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slOperador").focus();
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
            $("#slOperador").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_opMVP_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_opMVP_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_opMVP_descricao');
        var pesquisaBusca = await valorCookie($('#hdSiglaAeroporto').val()+'_opMVP_busca');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#pslOperador").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            //await suCarregarSelectTodos('DestinoIcao','#pslIcaoDestino','','', 'Consulta');
            //await suCarregarSelectTodos('OrigemIcao','#pslIcaoOrigem','','', 'Consulta');
            await suCarregarSelectTodos('OperadorANAC','#pslOperador','','', 'Consulta');
            await suCarregarSelectTodas('SituacaoSiros','#pslSituacaoSiros','','', 'Consulta');
            await suCarregarSelectTodas('NaturezaOperacao','#pslNaturezaOperacao','','', 'Consulta');
            await suCarregarSelectTodos('ServicoAnac','#pslServico','','', 'Consulta');
            await suCarregarSelectTodos('ObjetoTransporte','#pslObjetoTransporte','','', 'Consulta');
            await suCarregarSelectTodas('FonteVoos','#pslFonte','','', 'Consulta');
        });            

        // Adequações para o cadastro          
        await opCarregarVoosPlanejados('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, pesquisaBusca, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#slOperador").focus();
    });
</script>