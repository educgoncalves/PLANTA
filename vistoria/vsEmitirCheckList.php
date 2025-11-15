<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

verificarExecucao();

// Recuperando as informações do Aeroporto
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];
$usuario = $_SESSION['plantaUsuario'];

// Recebe os parâmetros
$id = carregarGets('id', "");           // Agendamento
$idPlano = carregarGets('idPlano', "");
$execucao = carregarGets('execucao', "");

// Ponto para exibição do formulário
formulario:
//metaTagsBootstrap('');
$titulo = "Emitir CheckList";
?>
<!-- Bootstrap 5.3.2 JQuery CSS -->
<link rel="stylesheet" href="../ativos/bootstrap_532/dist/css/bootstrap.min.css">
<script src="../ativos/js/jquery-3.7.1.js"></script>
<link rel="stylesheet" href="../ativos/css/mapa.css">
<!-- *************************************************** -->
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo ?></title>
</head>
<body>
<div id="container" class="px-5">
    <!-- Campos hidden -->
    <input type="hidden" name="aeroporto" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
    <input type="hidden" name="id" id="hdId" <?="value=\"{$id}\"";?>/>
    <input type="hidden" name="idPlano" id="hdIdPlano" <?="value=\"{$idPlano}\"";?>/>
    <input type="hidden" name="execucao" id="hdExecucao" <?="value=\"{$execucao}\"";?>/>
    <!--***************************************************************** -->
    <div class="row mt-2" >
         <div class="d-flex justify-content-start">
            <img class="d-inline-block align-text-top rounded-pill" src="../ativos/img/logo_medio.png" alt="logo">
            <div class="mt-2 ps-4" ><H5><?php echo $nomeAeroporto;?></H5></div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="container">
            <div class="row" >
                <div class="col-md-8" id="divTitulo" style="display:none"></div>
                <div class="col-md-4" id="divPagina" style="display:none"></div>
            </div>
        </div>
        <div class="container table-responsive" id="divTabela"></div>
        <div class="container" id="divImpressao" style="display:none"></div>
    </div>
</div>
</body>
<script src="../vistoria/vsFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        filtro = " AND vp.idAeroporto = "+$("#hdAeroporto").val()+" AND va.id = "+$("#hdId").val();
        base = (!isEmpty($("#hdExecucao").val()) ? 'VistoriaItensResultados' : 'VistoriaItensAgendamentos');
        await vsCarregarVistoriaParecer('Consultar', base, filtro);
    });
</script>
<!-- *************************************************** -->