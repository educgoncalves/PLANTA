<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../operacional/opFuncoes.php");
require_once("../modais/mdModais.php");
verificarExecucao();

// Modal e procedimentos para cada tipo de movimentação
require_once("../operacional/opMovimentosStatusModal.php");

// Modal para a inclusão rápida de informações
require_once("../modais/mdComandante.php");
require_once("../modais/mdMatricula.php");
require_once("../modais/mdAeroportoOrigem.php");
require_once("../modais/mdAeroportoDestino.php");

//var_dump($_GET);
//var_dump($_POST);
//var_dump($_SESSION);

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
$refreshPagina = $_SESSION['plantaRefreshPagina'];

// Resolvendo o tipo e objetivo do formulário
// Para solucionar os GETS que não tenham esses dois parametros
$tipo = carregarGets('tipo', (isset($_SESSION[$siglaAeroporto.'_opMS_tipo']) ? $_SESSION[$siglaAeroporto.'_opMS_tipo'] : ''));
$objetivo = carregarGets('objetivo', (isset($_SESSION[$siglaAeroporto.'_opMS_objetivo']) ? $_SESSION[$siglaAeroporto.'_opMS_objetivo'] : ''));
$_SESSION[$siglaAeroporto.'_opMS_tipo'] = $tipo;
$_SESSION[$siglaAeroporto.'_opMS_objetivo'] = $objetivo;

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

// Controle do Request
// Caso o request seja invalido (F5) não executa nenhuma funcão, só exibe o formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hash = md5( implode($_POST) );
    if(isset($_SESSION['hash'] ) && $_SESSION['hash'] == $hash ) { 
        montarMensagem("warning",array('Tentativa de repetir a mesma operação!')); 
        $parametros['funcao'] = ''; 
        goto formulario;  
    } else { $_SESSION['hash'] = $hash; }  
}

// Modal
// Preparando a chamada do modal pelo javascript de acordo com a função escolhida
//
if ($parametros['funcao'] != null) {
    switch ($parametros['tipo']) {
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
                            $parametros['funcao'] = null; 
                        }                 
                        // var_dump("STA"); var_dump($parametros); 
                        // Só prossegue se montar o último ID do movimento do Status
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

// Salvando as informações do Status
//
if ($evento == "salvar") {
    switch ($tipo) {
        case 'Status':
            $status = pegarDigitacaoStatus($parametros);
            // Correção da classe, natureza e serviço
            if (($parametros['funcao'] == 'Inclusão' && $parametros['movimento'] == 'Pouso') ||
                ($parametros['funcao'] == 'Alteração' && $parametros['movimento'] == 'Status')) {
                $status = corrigirCamposStatus('origem', $status);
            }
            if (($parametros['funcao'] == 'Alteração' && $parametros['movimento'] == 'Status')  && 
                ($status['resultado'] === 'warning')) {
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

// Salvando as informações de Conectar Status
//
if ($evento == "conectar") {
    $parametros['id'.$parametros['movimento']] = carregarPosts('conectarVoos');
    $parametros = salvarConectarStatus($parametros);
    montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento']);
    //var_dump("Conectar"); var_dump($parametros);
}

// Excluir o último movimento
//
if ($evento == "excluir" && $idUltimo != "") {
    $parametros = excluirMovimentoStatus($parametros);
    montarMensagem($parametros['status'],$parametros['mensagem'],$parametros['complemento']);
    //var_dump("Excluir"); var_dump($parametros);
}

// Ponto para exibição do formulário
// Inicializando o vetor caso não exista para manter a digitação e não apresentar  erro   
formulario:           
$status = (!isset($status) ? limparStatus($parametros) : $status);
$statusComplementos = (!isset($statusComplementos) ? limparStatusComplementos() : $statusComplementos);
$ordenacao = carregarCookie($siglaAeroporto.'_opMS_ordenacao','sm.idStatus desc, sm.id');    
metaTagsBootstrap('');
$titulo = ($objetivo == "status" ? "Status de Aeronaves" : "Movimentação de Aeronaves");
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>    
    <div class="container alert alert-padrao" >
        <form id="frmStatus" class="form-group" action="#" method="POST" autocomplete="off">
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
            <?php barraFuncoesStatus($titulo, $parametros, true); ?> 
            <br>
            <div class="d-flex justify-content-end" >
                <div id="barra-container"><div id="barra-progresso"></div></div>
            </div> 
        </form>  
    </div>     
    <div class="row">
        <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
            <div id="painelStatus">

<div id="divStatus">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-6" id="divTitulo" style="display:none"></div>
            <div class="col-6" id="divPagina" style="display:none"></div>
        </div>
    </div>
    <div class="container table-responsive" id="divTabela"></div>
    <div class="container" id="divImpressao" style="display:none"></div>
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
<!-- Modal Status -->
<!-- *************************************************** -->
<?php modalStatus($parametros,$status); ?>
<?php modalStatusComplementos($parametros,$statusComplementos); ?> 
<?php pesquisarStatus($parametros); ?>

<!-- *************************************************** -->
<!-- Modal Cadastros Rápidos -->
<!-- *************************************************** -->
<?php mdComando('editarStatusComplementos') ?>
<?php mdMatricula('editarStatus') ?>
<?php mdAeroportoOrigem('editarStatus') ?>
<?php mdAeroportoDestino('editarStatus') ?>
<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** --> 

<!-- *************************************************** -->
<!-- Script para  Cadastros Rápidos -->
<!-- *************************************************** -->
<script src="../modais/mdComandante.js"></script>
<script src="../modais/mdMatricula.js"></script>
<script src="../modais/mdAeroportoOrigem.js"></script>
<script src="../modais/mdAeroportoDestino.js"></script>
<!-- *************************************************** -->

<script src="../operacional/opFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script src="../pesquisas/pqPesquisa.js"></script>
<script>
    $(async function() {
        $("#limparFormularioStatus").click(function(){ limparClasse("LimparStatus"); });
        $("#limparFormularioStsComplementos").click(function(){ limparClasse("LimparStsComplementos"); });
        $("#limparConectarStatus").click(function(){ limparClasse("ConectarStatus"); });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
             
        // Preparando e chamando o modal de acordo com o tipo
        if ($('#hdFuncao').val() != "") {
            switch($('#hdTipo').val()) {
                case 'Status':
                    switch($('#hdFuncao').val()) {
                        case 'Visualizar':
                            await opVisualizarStatus(" AND st.idAeroporto = "+$('#hdAeroporto').val()+" AND st.id = "+$('#hdIdStatus').val());
                            $('#botaoVisualizar').trigger('click');
                            break;
                        case 'Conectar':
                            await prepararConectarStatus($('#hdAeroporto').val(),$('#hdIdStatus').val(),$('#hdMovimento').val());
                            $('#botaoConectar').trigger('click');
                        break
                        case 'Desconectar':
                        break
                        case 'Inclusão':       
                        case 'Alteração':                                             
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

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="'+$("#hdSiglaAeroporto").val()+'">';
            form += '<input type="hidden" name="titulo" value="Status de Aeronaves">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divImpressao').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="L">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
        });

        // Adequações para a pesquisa
        $("#limparFiltroStatus").click(function(){ limparClasse("CookieStatus"); buscarStatus(); });
        $("#limparPesquisaStatus").click(function(){ limparClasse("CookieStatus"); $("#ptxStsStatusInicial").focus(); });
        $("#aplicarPesquisaStatus").click(function(){ document.getElementById("hdPagina").value = 1; buscarStatus(); });
        $("#iniciarPesquisaStatus").click(async function(){ 
            await suCarregarSelectTodas('Classe','#pslStsClasse', '', '', 'Consulta');
            await suCarregarSelectTodas('Natureza','#pslStsNatureza', '', '', 'Consulta');
            await suCarregarSelectTodas('Servico','#pslStsServico', '', '', 'Consulta');
            await suCarregarSelectTodos('StatusFaturado','#pslStsFaturado', '', '', 'Consulta');
            await suCarregarSelectTodas('StatusSituacao','#pslStsSituacao', '', '', 'Consulta');
            await suCarregarSelectTodos('Movimentos','#pslStsMovimento', '', 
                " AND mo.idAeroporto = "+$("#hdAeroporto").val()+" AND mo.operacao = 'STA'", 'Consulta');
        });
        $("#pesquisarStatus").click(async function(){});
        $("#buscarStatus").click(function(){ buscarStatus(); });
        $("#ptxStsStatusInicial").mask('9999/99/999999', {'translation': {9: {pattern: /[0-9]/}}});   
        $("#ptxStsStatusFinal").mask('9999/99/999999', {'translation': {9: {pattern: /[0-9]/}}});  

        // Adequações para o cadastro
        // Decidindo o carregamento das informações de acordo com o objetivo do formulario
        var prefixo = '';
        var filtro = '';
        var ordem = 'sm.idStatus desc, sm.id';
        var descricaoFiltro = '';
        switch ($('#hdObjetivo').val()) {
            case "status":
                prefixo = '_opSST_';
                filtro = " AND st.idAeroporto = "+$('#hdAeroporto').val();
            break;
            case "movimento":
                prefixo = '_opMST_';
                filtro = " AND st.idAeroporto = "+$('#hdAeroporto').val()+ 
                        " AND st.faturado = 'NAO' AND st.situacao = 'ATV' AND sm.movimento <> 'CND'"
            break;
            case "painel":
                prefixo = '_opPST_';
                filtro = " AND st.idAeroporto = "+$('#hdAeroporto').val()+ 
                        " AND st.faturado = 'NAO' AND st.situacao = 'ATV' AND sm.movimento <> 'CND'"
            break;
        }
        var statusFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+prefixo+'filtro');
        if (statusFiltro == " ") {
            await criarCookie($('#hdSiglaAeroporto').val()+prefixo+'filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+prefixo+'descricao', descricaoFiltro);  
            await criarCookie($('#hdSiglaAeroporto').val()+prefixo+'ordenacao', ordem);                                               
        }
        ordem = await valorCookie($('#hdSiglaAeroporto').val()+prefixo+'ordenacao');
        filtro = " " + await valorCookie($('#hdSiglaAeroporto').val()+prefixo+'filtro');
        descricaoFiltro = await valorCookie($('#hdSiglaAeroporto').val()+prefixo+'descricao');

        // Criando um intervalo para atualização automática das Grids
        const refresh = $('#hdRefreshPagina').val() * 1000;
        //await ajaxManterStatus($('#hdSiglaAeroporto').val(),prefixo);
        await atualizarStatus(ordem,filtro,descricaoFiltro,prefixo)
        barraProgresso(refresh);
        var interval = setInterval(async function () {
            var date = new Date();
            //await ajaxManterStatus($('#hdSiglaAeroporto').val(),prefixo);
            await atualizarStatus(ordem,filtro,descricaoFiltro,prefixo)            
            barraProgresso(refresh);
        }, refresh);
    });

    async function atualizarStatus(ordem,filtro,descricao,prefixo){
        const busca = '';
        const pagina = 0;
        const limite = 0;
        switch (prefixo) {
            case "_opSST_":
                await opCarregarStatus('Cadastrar', filtro, ordem, descricao, pagina, limite);
            break;
            case "_opMST_":
                await opCarregarUltimosMovimentos('Cadastrar', filtro, ordem, descricao, pagina, limite);
            break;
        }
    }
</script>