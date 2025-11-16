<?php
	require_once("../administracao/adFuncoes.php");
	require_once("../suporte/suConexao.php");
	require_once("../suporte/suFuncoes.php");
	verificarExecucao();

	// Desativa conexão
	//
	desativarConexao($_SESSION['plantaIDSite'], $_SESSION['plantaSistema'], $_SESSION['plantaUsuario'], $_SESSION['plantaIPCliente']);

	session_start(); // Inicia a sessão
	session_destroy(); // Destrói a sessão limpando todos os valores salvos
	header("Location: ../index.php");
 	exit; // Redireciona o visitante
?>