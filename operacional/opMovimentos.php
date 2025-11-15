<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../operacional/opFuncoes.php");
require_once("../modais/mdModais.php");
verificarExecucao();

// Modal e procedimentos para cada tipo de movimentação
require_once("../operacional/opMovimentosChegadasModal.php");
require_once("../operacional/opMovimentosPartidasModal.php"); 
require_once("../operacional/opMovimentosStatusModal.php");

// Modal para a inclusão rápida de informações
require_once("../modais/mdComandante.php");

//var_dump($_GET);
//var_dump($_POST);
//var_dump($_SESSION);

// Pegando o número da página a exibir
$_page = carregarGets('page', 0);
$_paginacao = carregarGets('paginacao', 'NAO');
$_limite = carregarGets('limite', 100);  

// Recuperando as informações do Aeroporto
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];
$usuario = $_SESSION['plantaUsuario'];
$refreshPagina = $_SESSION['plantaRefreshPagina'];

// Resolvendo o tipo e objetivo do formulário
// Para solucionar os GETS que não tenham esses dois parametros
$tipo = carregarGets('tipo', (isset($_SESSION[$siglaAeroporto.'_opPMV_tipo']) ? $_SESSION[$siglaAeroporto.'_opPMV_tipo'] : ''));
$objetivo = carregarGets('objetivo', (isset($_SESSION[$siglaAeroporto.'_opPMV_objetivo']) ? $_SESSION[$siglaAeroporto.'_opPMV_objetivo'] : ''));
$_SESSION[$siglaAeroporto.'_opPMV_tipo'] = $tipo;
$_SESSION[$siglaAeroporto.'_opPMV_objetivo'] = $objetivo;
//var_dump($_SESSION);

// Recebendo com o metodo GET, caso não esteja preenchido pega pelo POST
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = "show"; //($evento != "recuperar" ? "hide" : "show");
$funcao = carregarGets('funcao',carregarPosts('funcao'));
$movimento = carregarGets('movimento',carregarPosts('movimento'));
$idStatus = carregarGets('idStatus',carregarPosts('idStatus'));
$idChegada = carregarGets('idChegada',carregarPosts('idChegada'));
$idPartida = carregarGets('idPartida',carregarPosts('idPartida'));
$idMovimento = carregarGets('idMovimento',carregarPosts('idMovimento'));
$idUltimo = carregarGets('idUltimo',carregarPosts('idUltimo'));

// Montando o vetor de parâmetros
$parametros = array('aeroporto'=>$aeroporto, 
                    'siglaAeroporto'=>$siglaAeroporto,
                    'nomeAeroporto'=>$nomeAeroporto,
                    'utcAeroporto'=>$utcAeroporto,
                    'usuario'=>$usuario,
                    'tipo'=>$tipo,
                    'objetivo'=>$objetivo,
                    'evento'=>$evento,
                    'funcao'=>$funcao,
                    'movimento'=>$movimento,
                    'idStatus'=>$idStatus,
                    'idChegada'=>$idChegada,
                    'idPartida'=>$idPartida,
                    'idMovimento'=>$idMovimento,
                    'idUltimo'=>$idUltimo,
                    'status'=>null,
                    'mensagem'=>null,
                    'complemento'=>null,
                    'page'=>$_page,
                    'paginacao'=>$_paginacao,
                    'limite'=>$_limite);
//var_dump("Inicio"); var_dump($parametros);  

// Modal
// Preparando a chamada do modal pelo javascript de acordo com a função escolhida
if ($parametros['funcao'] != null) {
    switch ($parametros['tipo']) {
        case 'Chegada':
            switch ($parametros['funcao']) {
                case "Desconectar":
                    desconectarVoos($parametros);
                break;
                default:
                    if ($parametros['movimento'] != "Previsão") {
                        // Pega último ID do movimento de Chegada
                        $parametros = pegarIdUltimoMovimentoChegada($parametros);
                        if ($parametros['status'] == 'danger') {
                            montarMensagem($parametros['status'],array($parametros['mensagem']),$parametros['complemento']);
                        }                 
                        // var_dump("CHG"); var_dump($parametros);                  
                        // Só prossegue se montar o último ID do movimento de Chegada
                        if ($parametros['idUltimo'] != '') {
                            $chegada = prepararChegada($parametros);
                            if ($chegada['resultado'] == 'danger') {
                                montarMensagem($chegada['resultado'],array($chegada['mensagem']),$chegada['complemento']);
                                $parametros['funcao'] = null; 
                            }
                        }
                    }
                break;
            }
        break;
        case 'Partida':
            switch ($parametros['funcao']) {
                case "Desconectar":
                    desconectarVoos($parametros);
                break;
                default:
                    if ($parametros['movimento'] != "Previsão") {
                        // Pega último ID do movimento de Partida
                        $parametros = pegarIdUltimoMovimentoPartida($parametros);
                        if ($parametros['status'] == 'danger') {
                            montarMensagem($parametros['status'],array($parametros['mensagem']),$parametros['complemento']);
                        }                 
                        // var_dump("PRT"); var_dump($parametros); 
                        // Só prossegue se montar o último ID do movimento de Partida
                        if ($parametros['idUltimo'] != '') {
                            $partida = prepararPartida($parametros);
                            if ($partida['resultado'] == 'danger') {
                                montarMensagem($partida['resultado'],array($partida['mensagem']),$partida['complemento']);
                                $parametros['funcao'] = null; 
                            }
                        }
                    }
                break;
            }
        break;
        case 'Status':
            switch ($parametros['funcao']) {
                case "Desconectar":
                    desconectarStatus($parametros);
                break;
                default:
                    if (($parametros['funcao'] == "Alteração") || 
                        ($parametros['funcao'] == "Conectar") ||
                        ($parametros['funcao'] == "Inclusão" && 
                            $parametros['movimento'] != "Pouso" && $parametros['movimento'] != "Previsão")) { 
                        // Pega último ID do movimento de Status
                        $parametros = pegarIdUltimoMovimentoStatus($parametros);
                        if ($parametros['status'] == 'danger') {
                            montarMensagem($parametros['status'],array($parametros['mensagem']),$parametros['complemento']);
                        }                 
                        // Só prossegue se montar o último ID do movimento de Partida
                        if ($parametros['idUltimo'] != '') {
                            $status = prepararStatus($parametros);
                            if ($status['resultado'] == 'danger') {
                                montarMensagem($status['resultado'],array($status['mensagem']),$status['complemento']);
                                $parametros['funcao'] = null; 
                            }
                        }
                    }
                break;
            }
        break;
        case 'StatusComplementos':
            $statusComplementos = prepararStatusComplementos($parametros);
            if ($statusComplementos['resultado'] == 'danger') {
                montarMensagem($statusComplementos['resultado'],
                    array($statusComplementos['mensagem']),$statusComplementos['complemento']);
                $parametros['funcao'] = null; 
            }
        break;
    }
    //var_dump("Função"); var_dump($parametros);
}

// Salvando as informações digitadas de Chegadas, Partidas e Status
//
if ($evento == "salvar") {
    switch ($tipo) {
        case 'Chegada':
            $chegada = pegarDigitacaoChegada($parametros);
            $parametros = salvarChegada($parametros, $chegada);
            montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento'],
                ($parametros['status'] != 'success' ? $tipo : ''));
        break;
        case 'Partida':
            $partida = pegarDigitacaoPartida($parametros);
            $parametros = salvarPartida($parametros, $partida);
            montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento'],
                ($parametros['status'] != 'success' ? $tipo : ''));
        break;
        case 'Status':
            $status = pegarDigitacaoStatus($parametros);
            // Correção da classe, natureza e serviço
            $status = corrigirCamposStatus('origem', $status);
            if ($status['resultado'] === 'warning') {
                montarMensagem($status['resultado'], $status['mensagem'], $status['complemento'], $tipo);
            } else {
                $parametros = salvarStatus($parametros, $status);
                montarMensagem($parametros['status'], $parametros['mensagem'], $parametros['complemento'],
                                ($parametros['status'] != 'success' ? $tipo : ''));
            }
        break;
        case 'StatusComplementos':
            $statusComplementos = pegarDigitacaoStatusComplementos();
            $parametros = salvarStatusComplementos($parametros, $statusComplementos);
            montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento'],
                ($parametros['status'] != 'success' ? $tipo : ''));
        break;
    }
    //var_dump("Salvar"); var_dump($parametros); 
}

// Salvando as informações de Conectar Chegadas, Partidas e Status
//
if ($evento == "conectar") {
    switch ($tipo) {
        case 'Chegada':
            $parametros['idPartida'] = carregarPosts('conectarVoos');
            $parametros = salvarConectarChegada($parametros);
            montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento']);
        break;
        case 'Partida':
            $parametros['idChegada'] = carregarPosts('conectarVoos');
            $parametros = salvarConectarPartida($parametros);
            //var_dump("Conectar Status"); var_dump($parametros); 
            montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento']);
        break;
        case 'Status':
            $parametros['id'.$parametros['movimento']] = carregarPosts('conectarVoos');
            $parametros = salvarConectarStatus($parametros);
            montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento']);
        break;
    }
    //var_dump("Conectar"); var_dump($parametros);  
}

// Excluir o último movimento
if ($evento == "excluir" && $idUltimo != "") {
    switch ($tipo) {
        case 'Chegada':
            $parametros = excluirMovimentoChegada($parametros);
            montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento']);
        break;
        case 'Partida':
            $parametros = excluirMovimentoPartida($parametros);
            montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento']);
        break;
        case 'Status':
            $parametros = excluirMovimentoStatus($parametros);
            montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento']);
        break;
    }
    //var_dump("Excluir"); var_dump($parametros);
}

// Ponto para exibição do formulário
// Inicializando o vetor caso não exista para manter a digitação e não apresentar erro    
formulario:  
//var_dump("Inicio Formulario"); var_dump($parametros);         
$status = (!isset($status) ? limparStatus($parametros) : $status);
$statusComplementos = (!isset($statusComplementos) ? limparStatusComplementos() : $statusComplementos);
$chegada = (!isset($chegada) ? limparChegada($parametros) : $chegada);
$partida = (!isset($partida) ? limparPartida($parametros) : $partida);    
metaTagsBootstrap('');
$titulo = "Movimentação de Voos e Aeronaves";
?>

<link rel="stylesheet" href="../ativos/css/painelOperacional.css">
<!-- <link href="/file-css.css?1254" rel="stylesheet" type="text/css" /> -->
<!-- <script type="text/javascript" src="/file-javascript.js?1254"></script> -->
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo ?></title>
</head>
<body>
<div id="container">      
    <div class="py-2 px-4" >
        <form>
            <!-- Campos hidden -->
            <!--***************************************************************** -->
            <input type="hidden" id="hdAeroporto" <?="value=\"{$parametros['aeroporto']}\"";?>/>
            <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$parametros['siglaAeroporto']}\"";?>/>
            <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$parametros['nomeAeroporto']}\"";?>/>
            <input type="hidden" id="hdUsuario" <?="value=\"{$parametros['usuario']}\"";?>/>
            <input type="hidden" id="hdRefreshPagina" <?="value=\"{$refreshPagina}\"";?>/>

            <input type="hidden" id="hdTipo" name="tipo" <?="value=\"{$parametros['tipo']}\"";?>/>
            <input type="hidden" id="hdObjetivo" name="objetivo" <?="value=\"{$parametros['objetivo']}\"";?>/>
            <input type="hidden" id="hdFuncao" name="funcao" <?="value=\"{$parametros['funcao']}\"";?>/>
            <input type="hidden" id="hdEvento" name="evento" <?="value=\"{$parametros['evento']}\"";?>/>
            <input type="hidden" id="hdMovimento" name="movimento" <?="value=\"{$parametros['movimento']}\"";?>/>
            <input type="hidden" id="hdIdStatus" name="idStatus" <?="value=\"{$parametros['idStatus']}\"";?>/>
            <input type="hidden" id="hdIdChegada" name="idChegada" <?="value=\"{$parametros['idChegada']}\"";?>/>
            <input type="hidden" id="hdIdPartida" name="idPartida" <?="value=\"{$parametros['idPartida']}\"";?>/>
            <input type="hidden" id="hdIdMovimento" name="idMovimento" <?="value=\"{$parametros['idMovimento']}\"";?>/>
            <input type="hidden" id="hdIdUltimo" name="idUltimo" <?="value=\"{$parametros['idUltimo']}\"";?>/> 

            <input type="hidden" id="hdPagina" <?="value=\"{$parametros['page']}\"";?>/>
            <input type="hidden" id="hdPaginacao" <?="value=\"{$parametros['paginacao']}\"";?>/>
            <input type="hidden" id="hdLimite" <?="value=\"{$parametros['limite']}\"";?>/> 
            <!--***************************************************************** -->
        </form>
        <div class="row">
            <div class="d-flex justify-content-between" >
                <div id="divLogo"><img class="rounded-pill" src="../ativos/img/logo_medio.png"/></div>
                <div id="divTitulo" class="titulo text-nowrap"><?php echo $nomeAeroporto;?></div>
            </div> 
            <div class="d-flex justify-content-end" >
                <div id="barra-container"><div id="barra-progresso"></div></div>
            </div> 
        </div>
        <?php exibirMensagem(); ?>
        <div class="row">
            <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                <?php barraFuncoesChegada("Chegadas", $parametros, false); ?> 
                <div id="painelChegadas" style="width:100%; height:193px; overflow-y:scroll;">
                    <div id="divChegadas"></div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                <?php barraFuncoesPartida("Partidas", $parametros, false); ?> 
                <div id="painelPartidas" style="width:100%; height:193px; overflow-y:scroll;">
                    <div id="divPartidas"></div>                    
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                <?php barraFuncoesStatus("Status", $parametros, false); ?>
                <div id="painelStatus" style="width:100%; height:193px; overflow-y:scroll;">
                    <div id="divStatus"></div>                    
                </div>
            </div>
        </div>
    </div>
</div>  

<!-- *************************************************** -->
<!-- Modal Geral -->
<!-- *************************************************** -->
<?php modalVisualizar(); ?>
<?php modalConectar($parametros); ?>

<!-- *************************************************** -->
<!-- Modal Chegadas -->
<!-- *************************************************** -->
<?php modalChegada($parametros,$chegada); ?>
<?php pesquisarChegada($parametros); ?>

<!-- *************************************************** -->
<!-- Modal Partidas -->
<!-- *************************************************** --> 
<?php modalPartida($parametros,$partida); ?>
<?php pesquisarPartida($parametros); ?>

<!-- *************************************************** -->
<!-- Modal Status -->
<!-- *************************************************** -->
<?php modalStatus($parametros,$status); ?>
<?php modalStatusComplementos($parametros,$statusComplementos); ?>
<?php pesquisarStatus($parametros); ?>

<!-- *************************************************** -->
<!-- Modal Cadastros Rápidos -->
<!-- *************************************************** -->
<?php mdComando('editarStatusComplementos') ?>
<!-- *************************************************** -->

</body>
</html>         
<script src="../operacional/opFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script src="../pesquisas/pqPesquisa.js"></script>
<script>
    $(async function() {
        // Limpeza dos formularios
        $("#limparFormularioChegada").click(function(){ limparClasse("LimparChegada"); });
        $("#limparFormularioPartida").click(function(){ limparClasse("LimparPartida"); });
        $("#limparFormularioStatus").click(function(){ limparClasse("LimparStatus"); });
        $("#limparFormularioStsComplementos").click(function(){ limparClasse("LimparStsComplementos"); });

        $("#limparConectar").click(function(){ limparClasse("Conectar"); });

        // Caixa Alta
        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase());});

        // Preparando e chamando o modal de acordo com o tipo
        if ($('#hdFuncao').val() != "") {
            switch($('#hdTipo').val()) {
                case 'Chegada':
                    switch($('#hdFuncao').val()) {
                        case 'Visualizar':
                            await opVisualizarChegada(" AND vo.idAeroporto = "+$('#hdAeroporto').val()+" AND vo.id = "+$('#hdIdChegada').val());
                            $('#botaoVisualizar').trigger('click'); 
                        break;
                        case 'Conectar':
                            await prepararConectarChegada($('#hdAeroporto').val(),$('#hdIdChegada').val());
                            $('#botaoConectar').trigger('click');
                        break;
                        case 'Desconectar':
                        break;
                        default:
                            await prepararModalChegada();
                            $('#botaoChegada').trigger('click');
                        break;
                    }
                break;
                case 'Partida':
                    switch($('#hdFuncao').val()) {
                        case 'Visualizar':
                            await opVisualizarPartida(" AND vo.idAeroporto = "+$('#hdAeroporto').val()+" AND vo.id = "+$('#hdIdPartida').val());
                            $('#botaoVisualizar').trigger('click');
                        break;
                        case 'Conectar':
                            await prepararConectarPartida($('#hdAeroporto').val(),$('#hdIdPartida').val());
                            $('#botaoConectar').trigger('click');
                        break;
                        case 'Desconectar':
                        break;
                        default:
                            await prepararModalPartida();
                            $('#botaoPartida').trigger('click');
                        break;
                    }                    
                break;
                case 'Status':
                    switch($('#hdFuncao').val()) {
                        case 'Visualizar':
                            await opVisualizarStatus(" AND st.idAeroporto = "+$('#hdAeroporto').val()+" AND st.id = "+$('#hdIdStatus').val());
                            $('#botaoVisualizar').trigger('click');
                            break;
                        case 'Conectar':
                            await prepararConectarStatus($('#hdAeroporto').val(),$('#hdIdStatus').val(),$('#hdMovimento').val());
                            $('#botaoConectar').trigger('click');
                        break;
                        case 'Desconectar':
                        break;
                        default:                            
                            await prepararModalStatus();
                            $('#botaoStatus').trigger('click');
                        break;
                    }  
                break;
                case 'StatusComplementos':
                    await prepararModalStatusComplementos();
                    $('#botaoStatusComplementos').trigger('click');
                break
            }
        }

    // *********************************************************************************************
    // Procedimentos para a chamada da pesquisa das Chegadas 
    // *********************************************************************************************
        var chegadaFiltro = await valorCookie($('#hdSiglaAeroporto').val()+'_opPCH_filtro');
        if (chegadaFiltro == "") {
            await criarCookie($('#hdSiglaAeroporto').val()+'_opPCH_filtro', " AND vo.idAeroporto = "+$('#hdAeroporto').val()+
                        " AND vo.situacao = 'ATV' AND vm.movimento <> 'CND'");
            await criarCookie($('#hdSiglaAeroporto').val()+'_opPCH_descricao', "");  
            await criarCookie($('#hdSiglaAeroporto').val()+'_opPCH_ordenacao', "vo.dhPrevista,vo.operador,vo.numeroVoo");     
        }
        $("#limparFiltroChegada").click(function(){ limparClasse("CookieChegada"); buscarChegada(); });
        $("#limparPesquisaChegada").click(function(){ limparClasse("CookieChegada"); });
        $("#aplicarPesquisaChegada").click(function(){ buscarChegada(); });
        $("#iniciarPesquisaChegada").click(async function(){ 
            await suCarregarSelectTodas('Classe','#pslChgClasse', '', '', 'Consulta');
            await suCarregarSelectTodas('Natureza','#pslChgNatureza', '', '', 'Consulta');
            await suCarregarSelectTodas('Servico','#pslChgServico', '', '', 'Consulta');
            await suCarregarSelectTodos('Movimentos','#pslChgMovimento', '', 
                " AND mo.idAeroporto = "+$("#hdAeroporto").val()+" AND mo.operacao = 'CHG'", 'Consulta');
        });
        $("#pesquisarChegada").click(async function(){});
        $("#buscarChegada").click(function(){ buscarChegada(); });
        $("#txChgChegada").mask('AAA9999', {'translation': { A: {pattern: /[A-Za-z]/}, 9: {pattern: /[0-9]/}}});    
    // *********************************************************************************************

    // *********************************************************************************************
    // Procedimentos para a chamada da pesquisa das Partidas
    // *********************************************************************************************
        var partidaFiltro = await valorCookie($('#hdSiglaAeroporto').val()+'_opPPR_filtro');
        if (partidaFiltro == "") {
            await criarCookie($('#hdSiglaAeroporto').val()+'_opPPR_filtro', " AND vo.idAeroporto = "+$('#hdAeroporto').val()+
                        " AND vo.situacao = 'ATV' AND vm.movimento <> 'CND'");
            await criarCookie($('#hdSiglaAeroporto').val()+'_opPPR_descricao', "");  
            await criarCookie($('#hdSiglaAeroporto').val()+'_opPPR_ordenacao', "vo.dhPrevista,vo.operador,vo.numeroVoo");     
        }
        $("#limparFiltroPartida").click(function(){ limparClasse("CookiePartida"); buscarPartida(); });
        $("#limparPesquisaPartida").click(function(){ limparClasse("CookiePartida"); });
        $("#aplicarPesquisaPartida").click(function(){ buscarPartida(); });
        $("#iniciarPesquisaPartida").click(async function(){ 
            await suCarregarSelectTodas('Classe','#pslPrtClasse', '', '', 'Consulta');
            await suCarregarSelectTodas('Natureza','#pslPrtNatureza', '', '', 'Consulta');
            await suCarregarSelectTodas('Servico','#pslPrtServico', '', '', 'Consulta');
            await suCarregarSelectTodos('Movimentos','#pslPrtMovimento', '', 
                " AND mo.idAeroporto = "+$("#hdAeroporto").val()+" AND mo.operacao = 'PRT'", 'Consulta');
        });
        $("#pesquisarPartida").click(async function(){});
        $("#buscarPartida").click(function(){ buscarPartida(); });
        $("#txPrtPartida").mask('AAA9999', {'translation': { A: {pattern: /[A-Za-z]/}, 9: {pattern: /[0-9]/}}});    
    // *********************************************************************************************

    // *********************************************************************************************
    // Procedimentos para a chamada da pesquisa dos Status 
    // *********************************************************************************************
        var statusFiltro = await valorCookie($('#hdSiglaAeroporto').val()+'_opPST_filtro');
        if (statusFiltro == "") {
            await criarCookie($('#hdSiglaAeroporto').val()+'_opPST_filtro', " AND st.idAeroporto = "+$('#hdAeroporto').val()+ 
                            " AND st.faturado = 'NAO' AND st.situacao = 'ATV' AND sm.movimento <> 'CND'");
            await criarCookie($('#hdSiglaAeroporto').val()+'_opPST_descricao', "");  
            await criarCookie($('#hdSiglaAeroporto').val()+'_opPST_ordenacao', "sm.id desc, sm.dhMovimento desc");                                               
        }
        $("#limparFiltroStatus").click(function(){ limparClasse("CookieStatus"); buscarStatus(); });
        $("#limparPesquisaStatus").click(function(){ limparClasse("CookieStatus"); });
        $("#aplicarPesquisaStatus").click(function(){ buscarStatus(); });
        $("#iniciarPesquisaStatus").click(async function(){ 
            await suCarregarSelectTodas('Classe','#pslStsClasse', '', '', 'Consulta');
            await suCarregarSelectTodas('Natureza','#pslStsNatureza', '', '', 'Consulta');
            await suCarregarSelectTodas('Servico','#pslStsServico', '', '', 'Consulta');
            await suCarregarSelectTodos('Movimentos','#pslStsMovimento', '', 
                " AND mo.idAeroporto = "+$("#hdAeroporto").val()+" AND mo.operacao = 'STA'", 'Consulta');
        });
        $("#pesquisarStatus").click(async function(){});
        $("#buscarStatus").click(function(){ buscarStatus(); });
        $("#ptxStsStatusInicial").mask('9999/99/999999', {'translation': {9: {pattern: /[0-9]/}}});   
        $("#ptxStsStatusFinal").mask('9999/99/999999', {'translation': {9: {pattern: /[0-9]/}}});  
    // *********************************************************************************************

    // Criando um intervalo para atualização automática das Grids
        const refresh = $('#hdRefreshPagina').val() * 1000;
   
        barraProgresso(refresh);
        // await ajaxChegadas($('#hdSiglaAeroporto').val());
        // await ajaxPartidas($('#hdSiglaAeroporto').val());
        // await ajaxMovimentosStatus($('#hdSiglaAeroporto').val());
        await atualizarChegadas($('#hdSiglaAeroporto').val());
        await atualizarPartidas($('#hdSiglaAeroporto').val());
        await atualizarStatus($('#hdSiglaAeroporto').val());
        var interval = setInterval(async function () {
            var date = new Date();
            barraProgresso(refresh);
            await atualizarChegadas($('#hdSiglaAeroporto').val());
            await atualizarPartidas($('#hdSiglaAeroporto').val());
            await atualizarStatus($('#hdSiglaAeroporto').val());
        }, refresh);

    // Define um temporizador para fechar a mensagem em 10 segundos
        $(document).ready(function() { setTimeout(() => { $('#mensagem .btn-close').click(); }, 10000); });
    });    

    // *********************************************************************************************
    // Procedimentos para a atualização das Grids
    // *********************************************************************************************
    async function atualizarChegadas(siglaAeroporto) {
        const ordem = valorCookie(siglaAeroporto+'_opPCH_ordenacao','');
        const filtro = " " + valorCookie(siglaAeroporto+'_opPCH_filtro','');
        const descricao = valorCookie(siglaAeroporto+'_opPC_descricao','');
        const busca = '';
        const pagina = 0;
        const limite = 0;    
        await opPainelChegadas('Cadastrar', filtro, ordem, descricao, busca, pagina, limite);
    }    

    async function atualizarPartidas(siglaAeroporto) {
        const ordem = valorCookie(siglaAeroporto+'_opPPR_ordenacao','');
        const filtro = " " + valorCookie(siglaAeroporto+'_opPPR_filtro','');
        const descricao = valorCookie(siglaAeroporto+'_opPPR_descricao','');
        const busca = '';
        const pagina = 0;
        const limite = 0;    
        await opPainelPartidas('Cadastrar', filtro, ordem, descricao, busca, pagina, limite);
    }    

    async function atualizarStatus(siglaAeroporto){
        const ordem = valorCookie(siglaAeroporto+'_opPST_ordenacao','');
        const filtro = " " + valorCookie(siglaAeroporto+'_opPST_filtro','');
        const descricao = valorCookie(siglaAeroporto+'_opPST_descricao','');
        const busca = '';
        const pagina = 0;
        const limite = 0;
        await opPainelStatus('Cadastrar', filtro, ordem, descricao, busca, pagina, limite);
    }
    // *********************************************************************************************
</script>        