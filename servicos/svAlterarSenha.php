<?php 
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

// Verifica se houve POST 
$chaveAtual = carregarPosts('chaveAtual');
$chaveNova = carregarPosts('chaveNova');
$confirmacao = carregarPosts('confirmacao');

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = "show"; //($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Salvando as informações
if ($evento == "salvar") {
    // Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['chaveNova','chaveAtual','confirmacao']);
    if (!$erros) {
        // Verifica se a senha nova está confirmada 
        $comando = "UPDATE planta_usuarios SET senha = sha1('".$chaveNova."') WHERE usuario = '".
        $_SESSION['plantaUsuario']."' AND senha = sha1('".$chaveAtual."')";
        try {
            if ($chaveNova == $confirmacao) {
                $conexao = conexao();
                $sql = $conexao->prepare($comando);
                if ($sql->execute()) {
                    if ($sql->rowCount() > 0) {
                        $_SESSION['TipoMensagem'] = "success";
                        $_SESSION['Mensagem'] = "Chave alterada com sucesso!";
                        header ("Location: ../menu/menu.php"); exit;
                    } else {
                        throw new PDOException("Não foi possível alterar este registro!");
                    }
                } else {
                    throw new PDOException("Não foi possível alterar este registro!");
                }
            } else {
                throw new PDOException("Nova chave não foi confirmada corretamente!");
            }
        } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
        }
    } else {
        montarMensagem("danger", $erros);
    } 
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Alterar de Chave de Acesso";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <form action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;"> 
            <?php barraFuncoesCadastro($titulo,array("X","","X")); ?>   
            <div class="form-group">
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">             
                        <div class="row mt-2">
                            <div class="col-md-4">    
                                <label for="txChaveAtual">Chave Atual</label>
                                <input type="password" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txChaveAtual"  name="chaveAtual"/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">                             
                                <label for="txChaveNova">Chave Nova</label>
                                <input type="password" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txChaveNova" name="chaveNova"/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">                              
                                <label for="txConfirmacao">Confirmação</label>
                                <input type="password" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txConfirmacao" name="confirmacao" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->  
<script src="../servicos/svFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script> 
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#txChaveAtual").focus();
        });
        $("#txChaveAtual").focus();

        // // Interceptar a tecla enter e submeter o botão principal
        // //
        // document.addEventListener("keypress", function(e) {
        //     if(e.key === 'Enter') {
        //         event.preventDefault();
        //         document.querySelector("#submit").click();
        //     }
        // });
    });
</script>