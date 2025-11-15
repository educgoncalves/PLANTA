<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../administracao/adFuncoes.php");
verificarExecucao();

// metaTagsSVG();
// metaTagsTema();

// Verifica se recebeu parâmetros para a troca de aeroporto
$aeroporto = carregarGets('aeroporto');
if ($aeroporto != '') {
    // Desativar todas as conexões do aeroporto atual + sistema + usuario + identificacao
    $_retorno = desativarConexao($_SESSION['plantaIDAeroporto'], $_SESSION['plantaSistema'], $_SESSION['plantaUsuario'], $_SESSION['plantaIPCliente']);
    if ($_retorno['status'] == "OK"){
        $dados = explode("|", $aeroporto);
        $_SESSION['plantaAeroporto'] = $dados[0];
        $_SESSION['plantaGrupo'] = $dados[1];
        $_SESSION['plantaNivel'] = $dados[2];
        $_SESSION['plantaIDAeroporto'] = $dados[3];
        $_SESSION['plantaNomeAeroporto'] = $dados[4];
        $_SESSION['plantaSistema'] = $dados[5];
        $_SESSION['plantaLocalidadeAeroporto'] = $dados[6];
        // if (!empty($_COOKIE)) {
        //   foreach ($_COOKIE as $name => $value) {
        //     if ($name != "PHPSESSID") {
        //       setcookie($name, "", time() - 3600);
        //     }
        //   }
        // }
        // Ativar a conexão do aeroporto escolhido + sistema + usuario + grupo + identificacao
        $_retorno = ativarConexao($_SESSION['plantaIDAeroporto'], $_SESSION['plantaSistema'], $_SESSION['plantaUsuario'], $_SESSION['plantaGrupo'], $_SESSION['plantaIPCliente']);
        if ($_retorno['status'] == "OK"){
        $_SESSION['plantaIDConexao'] = $_retorno['idConexao'];
        }
    }
    if ($_retorno['status'] != "OK"){
        montarMensagem("danger", array($_retorno['msg']));
        header("Location: ../index.php");
        exit; // Redireciona o visitante
    }
}
metaTagsBootstrap('');
?>
<head>
<title><?php echo $_SESSION['plantaSistema']." - ".$_SESSION['plantaGrupo']?></title>
</head>
<body>
<?php require_once("menuPrincipal.php");?>
<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<script>
    // Exibe a barra de atalhos
    document.getElementById("atalhos").style.display = "block";
    document.getElementById("graficos").style.display = "block";
</script>
<!-- *************************************************** -->