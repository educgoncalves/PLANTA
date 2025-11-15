<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suConexao.php");
require_once("../suporte/suFuncoes.php");
require_once("../administracao/adFuncoes.php");
require_once("../suporte/suEnviarEmail.php");

// Verificar se foi enviado dados via POST 
//
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$_retorno = ['status' => "ERRO", 'msg' => "E-mail deve ser informado!"];
	$_email = carregarPosts('email');
	$_usuario = "__GEAR__";
	if (!empty($_email)) {
        $_token = gerarToken($_usuario);
        $_dados = ['usuario'=>$_usuario,'email'=>$_email];
        $_post = ['token'=>$_token,'funcao'=>'RecuperarSenha','dados'=>$_dados];
		$_retorno = executaAPIs('apiLogins.php', $_post);
	}
	montarMensagem(($_retorno['status']=="OK" ? "success" : "danger"), array($_retorno['msg'])); 
	if ($_retorno['status'] == "OK") {
		header("Location: ../index.php");
      	exit; // Redireciona o visitante
	}
}
metaTagsBootstrap('');
metaTagsSVG();
metaTagsTema(true);
?>
<!-- Customizações -->
<link rel="stylesheet" href="../ativos/css/login.css">
<!-- *************************************************** -->
<head>
	<title>GEAR - Gerenciamento de Aeroportos</title>
</head>
<body class="align-items-center py-0 bg-body-padrao">
	<div class="row">
		<div class="col-md-6 d-flex justify-content-center">
			<a href="http://www.decolamais.com.br">
			<img class="mt-2 d-inline-block align-text-top" src="../ativos/img/decola+.png" alt="logo"/>
			</a>
		</div>
		<div class="col-md-6">	
			<main class="form-signin">
				<form action="#" method="post" id="index">
					<a href="../index.php">
					<img class="mb-4 rounded-pill" src="../ativos/img/logo.png" alt="logo" style="width:100%"></a>
					<h4 class="mb-3">Gerenciamento de Aeroportos</h4>
					<div class="form-floating">
						<input type="text" class="form-control cpoObrigatorio input-lg" id="txEmail" name="email" placeholder="Email" autocomplete="off">
						<label for="txEmail">E-mail</label>
					</div>
					<p class="text-justify fw-bold h6">Atenção</p>
					<p class="text-justify h6">Caso não lembre da sua senha para o acesso, informe apenas o seu e-mail 
						cadastrado e clique no botão abaixo, para enviarmos uma nova senha para você.</p>
					<button class="btn btn-padrao w-100 py-2" type="submit" value="Enviar">Recuperar senha</button>
					<p class="text-body-secondary text-sm-start">&copy; 2024–2025</p>
				</form>
			</main>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">	
			<?php 
			if (isset($_SESSION['exibirMensagem']) && $_SESSION['exibirMensagem'] != "") {
				echo $_SESSION['exibirMensagem'];
				$_SESSION['exibirMensagem'] = "";
			}
			?>
		</div>
	</div>
</body>
</html>
<script src="../administracao/adFuncoes.js"></script>
<script>
    $(async function() {
		$("#txEmail").focus();
    });
</script>