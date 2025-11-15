<?php
// Procedimentos iniciais
session_start(); // Inicia a sessão
ob_start(); // Limpar o buffer

header("Content-Type: text/html; charset=UTF-8",true);
require_once("suporte/suConfig.php");
require_once("suporte/suConexao.php");
require_once("suporte/suFuncoes.php");
require_once("administracao/adFuncoes.php");

// var_dump($_SERVER);
// var_dump($_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].($_SERVER["HTTP_HOST"] == "localhost"? "/GEAR" : ""));
//
$retorno = ['status' => "ERRO", 'msg' => "Campos devem ser informados!"];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$usuario = carregarPosts('usuario');
    $senha = carregarPosts('senha');
	$site = explode("|",carregarPosts('site'));
	$aeroporto = $site[0];
	if (!empty($aeroporto)) {
		$sistema = $site[1];
		$retorno = loginCompleto($aeroporto, $sistema, $usuario, $senha);
		if($retorno['status']=="OK"){
			if ($retorno['msg']=="Conectar") {
				header("Location: menu/menu.php"); 
				exit;
			}
		}
	}
	// Verifica erro
	if($retorno['status']=="ERRO"){
		$_SESSION['exibirMensagem'] = '<div id="mensagem" class="container alert alert-danger alert-dismissible fade show" role="alert">'. 
										'Erro: '.$retorno['msg'].'</br>'. 
										'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	}
}

metaTagsBootstrap('raiz');
metaTagsSVG();
metaTagsTema(true);
?>
<!-- Customizações -->
<link rel="stylesheet" href="ativos/css/login.css">
<!-- *************************************************** -->
<head>
	<title>GEAR - Gerenciamento de Aeroportos</title>
</head>
<body class="align-items-center py-0 bg-body-padrao">
	<main class="form-signin w-100 m-auto">
		<!-- <form action="" method="POST" onsubmit="camposObrigatorios(this); return false;"> -->
		<form action="" method="POST">
			<img class="mb-4" src="ativos/img/logo.png" alt="logo" style="width:100%">
			<h4 class="mb-3">Gerenciamento de Aeroportos</h4>
			<!-- <h5 class="mb-3">Entre com sua chave de acesso</h5> -->
			<div class="form-floating">
				<select class="form-select cpoObrigatorio input-lg" id="slPlanta" name="site"></select>
				<label for="slPlanta">Planta</label>
			</div>
			<div class="form-floating">
				<input type="text" class="form-control cpoObrigatorio input-lg" id="txUsuario" name="usuario" placeholder="Usuário" autocomplete="off">
				<label for="txUsuario">Conta</label>
			</div>
			<div class="form-floating">
				<input type="password" class="form-control cpoObrigatorio input-lg" id="txSenha" name="senha" placeholder="Senha" autocomplete="off">
				<label for="txSenha">Chave</label>
			</div>
			<button class="btn btn-padrao w-100 py-2" type="submit" value="Entrar">Entrar</button>
			<p class="mt-2 mb-3 text-body-secondary">&copy; 2024–2024</p>
		</form>
	</main>
	<?php 
		if (isset($_SESSION['exibirMensagem']) && $_SESSION['exibirMensagem'] != "") {
			echo $_SESSION['exibirMensagem'];
			$_SESSION['exibirMensagem'] = "";
		}
	?>
</body>
</html>
<script src="administracao/adFuncoes.js"></script>
<script>
    $(async function() {
		await adCarregarSelectLoginPlanta('#slPlanta','','','Login');
    });
</script>