<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../tarefas/trFuncoes.php");
require_once("../tarefas/trMatriculasAnac.php");

verificarExecucao();

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Inicializando variável URL  
$url = 'https://sistemas.anac.gov.br/dadosabertos/Aeronaves/RAB/dados_aeronaves.csv';

// Baixar ANAC
if ($evento == "importar") {
    // Data e hora UTC
    $identificacao = dateTimeUTC()->format('Ymd_His');
    executarImportacaoMatriculasAnac($identificacao,$_SESSION['plantaUsuario'], 'MNL');

    // Verifica se tem arquivo log gerado
    $log = lerXLogProcesso('../logs/trMatriculasAnac_'.$identificacao.'.txt');
    montarMensagem($log[0], $log[1]);
}

// Baixar log desprezada
if ($evento == "baixar") {
    if (file_exists('CadastroMoradores.txt')) {
        // Compactando os arquivos
        $_nomeZip = 'Cadastros.zip';
        $_caminhoZip = '../moradores/'.$_nomeZip;
        $zip = new ZipArchive();
        if($zip->open($_caminhoZip, ZipArchive::CREATE)) {
            $zip->addFile('CadastroMoradores.txt','CadastroMoradores.txt');
            $zip->addFile('CadastroVeiculos.txt','CadastroVeiculos.txt');
            $zip->addFile('CadastroTelefones.txt','CadastroTelefones.txt');
        }
        $zip->close();
        //unlink('CadastroMoradores.txt');
        //unlink('CadastroVeiculos.txt');
        //unlink('CadastroTelefones.txt');
        header("Location: ../suporte/suDownload.php?arquivo=".$_caminhoZip."&excluir=sim");
    } else {
        montarMensagem("danger", array("Arquivos de Cadastro não foram gerados!"));
    }
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Importar Matrículas da ANAC - RAB";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho($titulo); ?>    
    <div class="container alert alert-padrao" >
        <form action="?evento=importar" method="POST" class="form-inline" action="" enctype="multipart/form-data" onsubmit="$('.carregando').show();">
            <div class="row">  
                <div class ="col-lg-10">
                    <div class="row pt-2 px-2">            
                        <?php
                            echo "<p class='text-justify'>O arquivo de informações será baixado diretamente da url <strong>".$url."</strong> para nosso servidor.";
                            echo "<br><br>Em seguida todas as informações serão atualizadas.";
                            echo "<br><br>Ao pressionar o botão <strong>IMPORTAR</strong>, aguarde até o término do download e da atualização das informações.</p>";
                        ?>
                    </div>
                </div>
                <div class ="col-lg-2">
                    <div class="row pt-2 px-2">
                        <input type="submit" class="btn btn-padrao btn-lg btn-group-justified" id="importar" value="Importar"/>
                    </div>       
                </div>
            </div>
            <?php destacarTarefaRAB(); ?>   
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