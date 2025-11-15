<?php 
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
verificarExecucao();

// Recuperando as informações da pasta
$path = "../logs/";
$extensao = "txt";
$pasta = listarArquivos($path, $extensao, 1);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $arquivo = carregarPosts('arquivo');
    $conteudo = carregarPosts('conteudo');
    $evento = carregarPosts('evento');

    // Controle do Request
    // Caso o request seja invalido (F5) não executa nenhuma funcão, só exibe o formulario
    $hash = md5( implode($_POST) );
    if(isset($_SESSION['hash'] ) && $_SESSION['hash'] == $hash ) { 
        montarMensagem("warning",array('Tentativa de repetir a mesma operação!')); 
        goto formulario; 
    } else { $_SESSION['hash'] = $hash; }  
} else {
    $arquivo = null;
    $conteudo = null; 
    $evento = null;
}

// Excluir Trace
if ($evento == "excluir") {
    if (file_exists($arquivo)) {
        unlink($arquivo);
        if (!file_exists($arquivo)) {
            montarMensagem('success',array('Arquivo '.$arquivo.' excluido com sucesso!'));
            $pasta = listarArquivos($path, $extensao, 1);
        } else {
            montarMensagem('danger',array('Arquivo '.$arquivo.' não foi excluido!'));
        }
    } else {
        montarMensagem('danger',array('Arquivo '.($arquivo != "" ? $arquivo.' não existe!' : 'não informado!')));
    }
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Log de Tarefas";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <form action="#" method="POST" class="form-group" autocomplete="off" id="traceAtividades">
            <?php barraFuncoesCadastro($titulo,array("","","X","","","","X","X","","","","","X")); ?>   
            <div class="form-group">
                <!-- Campos hidden -->
                <input type="hidden" name="evento" id="hdEvento" value=""/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div class ="col-lg-10">                 
                        <div class="row mt-2">
                            <div class="col-md-8">
                                <label for="slArquivo">Arquivo</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slArquivo" name="arquivo">
                                    <?php
                                        echo '<option value="" disabled selected>Selecionar</option>';
                                        if (count($pasta) > 0){
                                            foreach ($pasta as $arq) {
                                                $nome = $arq['nome'];
                                                echo '<option value="'.$nome.'"'.
                                                    (($nome == $arquivo) ? ' selected ' : '').'>'.$nome.'</option>';
                                            }
                                        } else {
                                            echo '<option value="" disabled selected>Sem registros</option>';
                                        }
                                    ?>
                                </select> 
                            </div>
                        </div>
                        <div class="row mt-2">
                             <div class="col-md-8">
                                <label for="txConteudo">Conteúdo</label>
                                <input type="text" class="form-control cpoCookie cpoLimpar input-lg" id="txConteudo" name="conteudo"
                                    <?php echo (!isNullOrEmpty($conteudo)) ? "value=\"{$conteudo}\"" : "";?>/>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </form>
    </div>
    <div class="container">
        <div class="row" >
            <div class="col-md-8" id="divTitulo">
                <?php echo '<h4>Log: '.$arquivo.'</h4>'; ?>
            </div>
            <div class="col-md-4" id="divPagina"></div>
        </div>
    </div>
    <div class="container table-responsive" id="divTabela">
        <table class='table table-striped table-hover table-bordered table-sm'>
            <thead class='table-info'>
                <tr><th>Registros</th></tr>
            </thead>
            <tbody>
                <?php
                if (file_exists($arquivo)) {
                    $dados = file($arquivo);
                    foreach($dados as $linha){
                        $linha = trim($linha);
                        $pos = strripos($linha, $conteudo);
                        if (($conteudo == '') || (($conteudo != '') && ($pos !== FALSE))) {
                            echo '<tr><td>'.trim($linha).'</td></tr>';
                        }
                    }
                } else {
                    echo '<tr><td>Arquivo '.($arquivo != "" ? $arquivo.' não existe!' : 'não informado!').'</td></tr>';
                }
                ?>
            </tbody>
        </tabele>
    </div>
    <div class="container" id="divImpressao" style="display:none"></div>
</div>
<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->     

<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#slArquivo").focus();
        });

        $("#buscarCadastro").click(function(){ 
            $('#hdEvento').val('buscar');
            $('#traceAtividades').submit();});

        $("#excluirCadastro").click(function(){ 
            $('#hdEvento').val('excluir');
            $('#traceAtividades').submit();
        });

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="'+$("#hdSiglaAeroporto").val()+'">';
            form += '<input type="hidden" name="titulo" value="' + $('#divTitulo').text() + '">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divTabela').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="L">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
            $("#slArquivo").focus();
        });

        $("#slArquivo").focus();
    });
</script>