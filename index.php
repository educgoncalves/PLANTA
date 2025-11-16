<?php
// Procedimentos iniciais
session_start(); // Inicia a sessão
ob_start(); // Limpar o buffer

header("Content-Type: text/html; charset=UTF-8",true);
require_once("suporte/suConfig.php");
require_once("suporte/suConexao.php");
require_once("suporte/suFuncoes.php");
require_once("administracao/adFuncoes.php");

// Verificar se foi enviado dados via POST 
//
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$retorno = ['status' => "ERRO", 'msg' => "Campos devem ser informados!"];
	$usuario = carregarPosts('usuario');
    $senha = carregarPosts('senha');
	if (!empty($senha)) {
		$retorno = loginUsuarioSenha($usuario,$senha);
		if ($retorno['status'] == "OK"){
			if ($retorno['msg'] == "Conectar") {
				header("Location: menu/menu.php"); 
				exit;
			} else {
				$modalPlanta = "modalPlanta";
			}
		} 
	}
	if ($retorno['status'] == "ERRO"){
		$_SESSION['exibirMensagem'] = '<br><div id="mensagem" class="container alert alert-danger alert-dismissible fade show" role="alert">'. 
										  'Erro: '.$retorno['msg'].
										'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	}
}

// Verificar se foi enviado dados via GET
//
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$evento = carregarGets('evento');
	$parametro = carregarGets('parametro'); 

	// Planta escolhido
	//
	if ($evento == "escolherPlanta" && $parametro != '') {
		$dados= explode("|", $parametro);
		$retorno = loginCompleto($dados[0], $dados[1], $_SESSION['plantaUsuario'], $_SESSION['plantaSenha']);
		if ($retorno['status']=="OK"){
			header("Location: menu/menu.php"); 
			exit;
		} else {
			$_SESSION['exibirMensagem'] = '<br><div id="mensagem" class="container alert alert-danger alert-dismissible fade show" role="alert">'. 
											  'Erro: '.$retorno['msg'].
											'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
		}
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
	<title>PLANTA - Gerenciamento de Plantas de Produção</title>
</head>
<body class="align-items-center py-0 bg-body-padrao">
	<div class="row">
		<div class="col-md-12 d-flex justify-content-center">	
			<main class="form-signin">
				<form action="#" method="post" id="index">
				<input type="hidden" name="evento" id="hdEvento" value=""/>
					<img class="mb-4 rounded-pill" src="ativos/img/logo_encode.png" alt="logo" style="width:100%">
					<h4 class="mb-3">Gerenciamento de Plantas de Produção</h4>
					<div class="form-floating">
						<input type="text" class="form-control cpoObrigatorio input-lg" id="txUsuario" name="usuario" placeholder="Usuário" autocomplete="off">
						<label for="txUsuario">Conta</label>
					</div>
					<div class="form-floating">
						<input type="password" class="form-control cpoObrigatorio input-lg" id="txSenha" name="senha" placeholder="Senha" autocomplete="off">
						<label for="txSenha">Senha</label>
					</div>
					<button class="btn btn-padrao w-100 py-2" type="submit" value="Entrar">Entrar</button>
					<div class="d-flex justify-content-between" >
						<p class="text-body-secondary text-sm-start">&copy; 2024–2025</p>
						<p><a href="./servicos/svRecuperarSenha.php">Esqueceu a senha?</a></p>
					</div>
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

	<!-- *************************************************** -->
	<!-- Modal Escolher PLANTA -->
	<!-- *************************************************** -->
	<button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#modalPlanta" style="display:none" id="botaoModalPlanta"></button>
	<input type="button" class="btn btn-success btn-lg" id="escolherPlanta" style="display:none" value="Trocar Planta"/>

	<div class="modal fade" id="modalPlanta" tabindex="-1" aria-labelledby="siteLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<div class="row text-left">
						<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" id="slPlanta" name="slPlanta" 
							onchange="$('#escolherPlanta').trigger('click');">
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal" id="fecharEscolherPlanta">Fechar</button>
				</div>
			</div>
		</div>
	</div>
	<!-- *************************************************** -->	
</body>
</html>
<script src="administracao/adFuncoes.js"></script>
<script>
    $(async function() {
        // Chamando modal
        var modalPlanta = "<?php echo $modalPlanta; ?>";
        var usID = "<?php echo $_SESSION['plantaIDUsuario']; ?>";
		if (modalPlanta != "") {
			await adCarregarSelectLoginPlantas('#slPlanta','',' AND ac.idUsuario = '+usID,'Cadastrar');
            $('#botaoModalPlanta').trigger('click');
        }

        $("#escolherPlanta").click(function(){
            window.location.href = "index.php?evento=escolherPlanta&parametro="+$("#slPlanta").val();
        });
    });
</script>