<?php
require_once("../suporte/suFuncoes.php");
$_filtro = carregarGets('filtro', "", true); 
$_descricao = carregarGets('descricao', "", true);  
$_busca = carregarGets('busca', "", true);  
$_ordem = carregarGets('ordem', "sm.id desc, sm.dhMovimento desc");  
$_pagina = carregarGets('pagina', 0); 
$_limite = carregarGets('limite', 100); 
?>
<div id="divStatus">
</div>
<script src="../operacional/opFuncoes.js"></script>
<script>
    $(async function() {
        var filtro = <?php echo '"'.$_filtro.'"'; ?>;
        var descricao = <?php echo '"'.$_descricao.'"'; ?>;
        var busca = <?php echo '"'.$_busca.'"'; ?>;
        var ordem = <?php echo '"'.$_ordem.'"'; ?>;
        var pagina = <?php echo $_pagina; ?>;
        var limite = <?php echo $_limite; ?>;
        await opPainelStatus('Cadastrar', filtro, ordem, descricao, busca, pagina, limite);
    });
</script>      