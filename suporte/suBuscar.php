<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

$funcao = (isset($_REQUEST['funcao']) ? $_REQUEST['funcao'] : ''); 
$busca = (isset($_REQUEST['busca']) ? $_REQUEST['busca'] : '');
$filtro = (isset($_REQUEST['filtro']) ? $_REQUEST['filtro'] : '');
$ordem = (isset($_REQUEST['ordem']) ? $_REQUEST['ordem'] : '');
$pagina = (isset($_REQUEST['pagina']) ? $_REQUEST['pagina'] : 0);
$limite = (isset($_REQUEST['limite']) ? $_REQUEST['limite'] : 10);
$grupamento = (isset($_REQUEST['grupamento']) ? $_REQUEST['grupamento'] : '');

$pesquisa = null;
$select = selectDB($funcao,$filtro,$ordem,$busca);
//gravaTrace($select);

if ($select != "") {
	try {
		$conexao = conexao();
		//
		// Adicionar grupamento caso haja 
		//
		if ($grupamento != "") {
			$selectGrupado = str_replace("[query]", $select, $grupamento);
			$select = $selectGrupado;
			//gravaTrace($select);
		}
		//
		// Executa a primeira query para pegar a quantidade total de registros
		//
		$sql = $conexao->prepare($select);
		$sql->execute(); 
		$total = $sql->rowCount();
		//
		// Executa a segunda query só se precisar paginar 
		//
		if ($pagina != 0 ) {
			$select .= " LIMIT ".(($pagina - 1) * $limite).",".$limite;
			//gravaTrace($select);
			$sql = $conexao->prepare($select);
			$sql->execute(); 
		}
		$registros = $sql->fetchAll(PDO::FETCH_ASSOC);
		foreach ($registros as $dados) {
				$dados['total'] = $total;
				$pesquisa[] = $dados;
		} 
	} catch (PDOException $e) {
		$pesquisa = null;
	}
}

echo json_encode($pesquisa);
?>
