<?php 
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../tarefas/trEquipamentos.php");

verificarExecucao();

// Importar via CSV	
//
if(!empty($_FILES['arquivo']['tmp_name'])) {
    // Data e hora UTC
    $identificacao = dateTimeUTC()->format('Ymd_His');
    executarImportacaoEquipamentos($identificacao,$_FILES['arquivo']['tmp_name'],$_SESSION['plantaUsuario'], 'MNL');

    // Verifica se tem arquivo log gerado
    $log = lerXLogProcesso('../logs/trEquipamentos_'.$identificacao.'.txt');
    montarMensagem($log[0], $log[1]);
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Importar Equipamentos - ICAO";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <label><h4>Importar Equipamentos - arquivos em formato CSV</h4></label>
        <form method="POST" class="form-inline" action="" enctype="multipart/form-data" onsubmit="$('.carregando').show();">
            <div class="row">  
                <div class ="col-lg-10">
                    <div class="row pt-2 px-2">            
                        <input type="file" class="input-file-block" name="arquivo">
                    </div>
                </div>
                <div class ="col-lg-2">
                    <div class="row pt-2 px-2">
                        <input type="submit" class="btn btn-padrao btn-lg btn-group-justified" id="importar" value="Importar"/>
                    </div>                
                </div>
            </div>
            <?php destacarTarefaICAO(); ?>  
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