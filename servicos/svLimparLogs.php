<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../tarefas/trLimparLogs.php");

verificarExecucao();

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Limpar logs
if ($evento == "excluir") {
    // Data e hora local do aeroporto
    $identificacao = dateTimeUTC($utcAeroporto)->format('Ymd_His');
    executarLimparLogs($identificacao,$_SESSION['plantaUsuario'], 'MNL');

    // Verifica se tem arquivo log gerado
    $log = lerXLogProcesso('../logs/trLimparLogs_'.$identificacao.'.txt');
    montarMensagem($log[0], $log[1]);
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Limpar Logs";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho($titulo); ?>
    <div class="container alert alert-padrao" >
        <form action="?evento=excluir" method="POST" class="form-inline" action="" enctype="multipart/form-data" onsubmit="$('.carregando').show();">
            <div class="row">  
                <div class ="col-lg-10">
                    <div class="row pt-2 px-2">            
                        <?php
                            echo "<p class='text-justify'>Os arquivos de log gerados na pasta <strong>../logs/</strong> e os registros da <strong>tabela de Logs</strong> gerados a mais de 10 dias serão excluídos.";
                            echo "<br><br>Ao pressionar o botão <strong>EXCLUIR</strong>, aguarde até o término do processo.</p>";
                        ?>
                    </div>
                </div>
                <div class ="col-lg-2">
                    <div class="row pt-2 px-2">
                        <input type="submit" class="btn btn-padrao btn-lg btn-group-justified btn-danger" id="excluir" value="Excluir"/>
                    </div>                
                </div>
            </div>
        </form>
    </div>
    <div class="container" id="divTituloTabela"></div>
    <div class="container table-responsive" id="divTabela"></div>
    <div class="container" id="divImpressao" style="display:none"></div>
</div>
<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->

<script>
    $('.carregando').hide();
</script> 