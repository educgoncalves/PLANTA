<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../tarefas/trFuncoes.php");
require_once("../tarefas/trPrivadosAnac.php");

verificarExecucao();

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Inicializando variável URL  
$url = 'https://sistemas.anac.gov.br/dadosabertos/Aerodromos/Aeródromos Públicos/Lista de aeródromos públicos/AerodromosPublicos.csv';

// Baixar ANAC
if ($evento == "importar") {
    // Data e hora UTC
    $identificacao = dateTimeUTC()->format('Ymd_His');
    executarImportacaoPrivadosAnac($identificacao,$_SESSION['plantaUsuario'], 'MNL');

    // Verifica se tem arquivo log gerado
    $log = lerXLogProcesso('../logs/trPrivadosAnac_'.$identificacao.'.txt');
    montarMensagem($log[0], $log[1]);
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Importar Aeródromos Privados da ANAC";
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
            <?php destacarTarefaPrivadosANAC(); ?>  
            <!-- <div class="row pt-2 px-2"> 
                <div class="progress progress-striped active">
					<div class="progress-bar" style="width: 0%"></div>
				</div>
            </div>                       -->
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

    // $(document).on('submit', 'form', function (e) {
    //     e.preventDefault();
    //     //Receber os dados
    //     $form = $(this);				
    //     var formdata = new FormData($form[0]);
        
    //     //Criar a conexao com o servidor
    //     var request = new XMLHttpRequest();
        
    //     //Progresso do Upload
    //     request.upload.addEventListener('progress', function (e) {
    //         var percent = Math.round(e.loaded / e.total * 100);
    //         $form.find('.progress-bar').width(percent + '%').html(percent + '%');
    //     });
        
    //     //Upload completo limpar a barra de progresso
    //     request.addEventListener('load', function(e){
    //         $form.find('.progress-bar').addClass('progress-bar-success').html('upload completo...');
    //         //Atualizar a página após o upload completo
    //         setTimeout("window.open(self.location, '_self');", 1000);
    //     });
        
    //     //Arquivo responsável em fazer o upload da imagem
    //     request.open('post', 'opImportarVoosRegularesProcessa.php');
    //     request.send(formdata);
    // });
</script> 