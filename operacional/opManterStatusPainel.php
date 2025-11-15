<?php
require_once("../suporte/suFuncoes.php");
$_filtro = carregarGets('filtro', "", true); 
$_descricao = carregarGets('descricao', "", true);  
$_busca = carregarGets('busca', "", true);  
$_ordem = carregarGets('ordem', "sm.id desc, sm.dhMovimento desc");  
$_pagina = carregarGets('pagina', 0); 
$_limite = carregarGets('limite', 100); 
$_prefixo = carregarGets('prefixo', '_opSST_'); 
?>
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
<script src="../operacional/opFuncoes.js"></script>
<script>
    $(async function() {
        var filtro = <?php echo '"'.$_filtro.'"'; ?>;
        var descricao = <?php echo '"'.$_descricao.'"'; ?>;
        var busca = <?php echo '"'.$_busca.'"'; ?>;
        var ordem = <?php echo '"'.$_ordem.'"'; ?>;
        var pagina = <?php echo $_pagina; ?>;
        var limite = <?php echo $_limite; ?>;
        var prefixo = <?php echo '"'.$_prefixo.'"'; ?>;
        switch (prefixo) {
            case "_opSST_":
                await opCarregarStatus('Cadastrar', filtro, ordem, descricao, pagina, limite);
            break;
            case "_opMST_":
                await opCarregarUltimosMovimentos('Cadastrar', filtro, ordem, descricao, pagina, limite);
            break;
        }
    });
</script>      