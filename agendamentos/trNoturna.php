<?php
$today = date("Y-m-d H:i:s");
$file = '/opt/gear/tarefas/log.txt';
 
// Check if file exists
if(file_exists($file)) {
	$current = file_get_contents($file);
} else {
	$current = '';
}    

$current .= "trNoturna: " . $today . "\n";
file_put_contents($file, $current);

// Pré-requisitos
require_once("../suporte/suConexao.php");
require_once("../tarefas/trVoosAnac.php");
require_once("../tarefas/trPublicosAnac.php");
require_once("../tarefas/trPrivadosAnac.php");
require_once("../tarefas/trMatriculasAnac.php");
require_once("../tarefas/trEquipamentos.php");
require_once("../tarefas/trLimparLogs.php");

// Usar a mesma identificação para todas as tarefas - acumulação diária
$identificacao = date("Ymd");

// Executando as tarefas de forma automática
executarImportacaoVoosAnac($identificacao);
executarImportacaoPublicosAnac($identificacao);
executarImportacaoPrivadosAnac($identificacao);
executarImportacaoMatriculasAnac($identificacao);
executarImportacaoEquipamentos($identificacao,"../arquivos/anac/Tabela Modelo Equipamentos.csv");
executarLimparLogs($identificacao);

// ***************************************************************************************************
// Tarefas que devem ser executadas levando em consideração os aeroportos clientes
//
// Looping de todos os aeroportos clientes para executar as tarefas
// Adicionar a sigla do aeroporto na identificacao
// Calcular parâmetros de período para os voos planejados e a data do movimento para gerar os voos operacionais
//
// Pré-requisitos
//
require_once("../suporte/suConexao.php");
require_once("../tarefas/trGerarVoosPlanejados.php");
require_once("../tarefas/trGerarVoosOperacionais.php");

// Montagem dos parâmetros
//
$d = new DateTime(date("Y-m-d"));
$d->add(new DateInterval("P2D"));
$inicio = $d->format("Y-m-01");
$final = $d->format("Y-m-t");
$dtMovimento = $d->format("Y-m-d");

// Looping dos aeroportos clientes para gerar as informações automáticas
//
try {
	$_conexao = conexao();
	$_comando = "SELECT DISTINCT cl.idAeroporto, ae.icao
				FROM gear_clientes cl 
				LEFT JOIN gear_aeroportos ae ON ae.id = cl.idAeroporto
				WHERE cl.situacao = 'ATV' AND ae.icao <> 'GEAR' 
				ORDER BY ae.icao";	
	$_sql = $_conexao->prepare($_comando); 
	if ($_sql->execute()){
		$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
		foreach ($_registros as $_dados) {
			$identificacaoAeroporto = $_dados['icao'].'_'.$identificacao;
			gerarVoosPlanejados($identificacaoAeroporto, $_dados['idAeroporto'], $_dados['icao'], $inicio, $final);
			gerarVoosOperacionais($identificacaoAeroporto, $_dados['idAeroporto'], $_dados['icao'], $dtMovimento);
		}
	} 
} catch (PDOException $e) {
}
// ***************************************************************************************************
?>
