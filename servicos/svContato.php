<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../suporte/suEnviarEmail.php");
verificarExecucao();

// Recuperando as informações do Sistema e remetente
$sistema = $_SESSION['plantaSistema'];
$aeroporto = $_SESSION['plantaSite'];
$usuario = $_SESSION['plantaUsuario'];
$email = $_SESSION['plantaEMail'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = "show"; //($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Verifica se houve POST 
$assunto = carregarPosts('assunto','');
$mensagem = carregarPosts('mensagem','');

// Enviando as informações
if ($evento == "enviar") {
    $erros = camposPreenchidos(['assunto','mensagem']);
    if (!$erros) {
        if (enviarEmail($sistema, $aeroporto, $usuario, $email, $assunto, $mensagem)) {
            montarMensagem("success",array("Email enviado!"));
        } else {
            montarMensagem("warning",array("Email não pode ser enviado!"));
        }	
    } else {
        montarMensagem("danger", $erros);
    } 
} 

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Enviar contato";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <form action="?evento=enviar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;">
            <?php barraFuncoesCadastro($titulo,array("","","X","","","","","","X")); ?>  
            <div class="form-group">
			 	<div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">				
						<label>Aeroporto</label>
						<h4><?php echo $_SESSION['plantaSite'].' - '.$_SESSION['plantaLocalidadeSite']  ?></h4>
						<label>Usuário</label>
						<h4><?php echo $_SESSION['plantaUsuario'].' - '.$_SESSION['plantaNome'] ?></h4>
						<label>Email</label>
						<h4><?php echo $email?></h4>
						<label for="slAssunto">Assunto</label>
						<select class="form-select cpoObrigatorio selLimpar input-lg" id="slAssunto" name="assunto">
							<option value="Elogio">Elogio</option>
							<option value="Reclamação">Reclamação</option>
							<option value="Sugestão">Sugestão</option>
							<option value="Suporte">Suporte</option>
							<option value="Outros">Outros</option>
						</select> 
						<label for="txMensagem">Mensagem</label>
						<textarea class="form-select cpoObrigatorio cpoLimpar input-lg" rows="5" id="txMensagem" name="mensagem">
                        </textarea>
                    </div>
                    <!-- Botão protegido para submissão do formulário pelo Javascript -->
                    <button hidden class="submit"></button>
                </div>
			</div>
		</form>
	</div>
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
            $("#slAssunto").focus();
        });
        $("#slAssunto").focus();
		
        // Simular o submit pelo javascript quando pressionar um botão
        //
        $("#enviarFormulario").click(function(){ event.preventDefault(); document.querySelector(".submit").click();
        });
        // // Interceptar a tecla enter e submeter o botão principal
        // //
        // document.addEventListener("keypress", function(e) {
        //     if(e.key === 'Enter') {
        //         event.preventDefault();
        //         document.querySelector(".submit").click();
        //     }
        // });
    });
</script>
