<?php
$today = date("Y-m-d H:i:s");
$file = '/opt/gear/tarefas/log.txt';
 
// Check if file exists
if(file_exists($file)) {
	$current = file_get_contents($file);
} else {
	$current = '';
}    

$current .= "trMinuto: " . $today . "\n";
file_put_contents($file, $current);

// Pré-requisitos
require_once("../tarefas/trControlarPropagandas.php");
require_once("../tarefas/trProcessarVoosOperacionais.php");
require_once("../tarefas/trProcessarStatus.php");
require_once("../tarefas/trProcessarReservas.php");
require_once("../tarefas/trProcessarConexoes.php");

// Usar a mesma identificação para todas as tarefas - acumulação diária
$identificacao = date("Ymd");

// Executando as tarefas de forma automática
controlarPropagandas($identificacao);
processarVoosOperacionais($identificacao);
processarStatus($identificacao);
processarReservas($identificacao);
processarConexoes($identificacao);

// ***************************************************************************************************
// Tarefas que devem ser executadas levando em consideração os aeroportos clientes
//
// Looping de todos os aeroportos clientes para executar as tarefas
// Adicionar a sigla do aeroporto na identificacao
//
// Pré-requisitos
//
require_once("../suporte/suConexao.php");
require_once("../tarefas/trConstrutorInformativos.php");

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
	$_comando = "SELECT DISTINCT cl.idSite, st.site
				FROM planta_clientes cl 
				LEFT JOIN planta_aeroportos ae ON ae.id = cl.idSite
				WHERE cl.situacao = 'ATV' AND st.site <> 'GEAR' 
				ORDER BY st.site";	
	$_sql = $_conexao->prepare($_comando); 
	if ($_sql->execute()){
		$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
		foreach ($_registros as $_dados) {
			$identificacaoAeroporto = $_dados['icao'].'_'.$identificacao;
			construtorInformativos($identificacaoAeroporto, $_dados['idSite'], $_dados['icao']);
		}
	} 
} catch (PDOException $e) {
}
// ***************************************************************************************************
?>