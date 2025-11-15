<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");

verificarExecucao();

// Controle de paginação
$form = carregarGets('form');

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Formulário ".$form." não implementado";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho($titulo, "warning"); ?>
</div>
<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->
