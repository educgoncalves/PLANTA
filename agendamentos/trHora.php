<?php
$today = date("Y-m-d H:i:s");
$file = '/opt/gear/tarefas/log.txt';
 
// Check if file exists
if(file_exists($file)) {
	$current = file_get_contents($file);
} else {
	$current = '';
}    

$current .= "trHora: " . $today . "\n";
file_put_contents($file, $current);

// Pré-requisitos

// Usar a mesma identificação para todas as tarefas
$identificacao = date("Ymd_His");

// Executando as tarefas de forma automática
?>
