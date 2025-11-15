<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");

verificarExecucao();

// Pegando o número da página a exibir
$monitor = carregarGets('monitor', 18);  

metaTagsBootstrap('');
?>
<html>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - Exibir monitor ".$monitor?></title>
</head>
<body>
    <input type="hidden" id="hdMonitor" <?="value=\"{$monitor}\"";?>/>
</body>
</html>
<!-- *************************************************** -->
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        var src = ($("#hdMonitor").val());
        window.open(src,"newWin","width="+screen.availWidth+",height="+screen.availHeight);
    });
</script>
