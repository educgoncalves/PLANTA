<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

// Token
$token = gerarToken($_SESSION['plantaSistema']);

// Recuperando as informações do Aeroporto
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];

metaTagsBootstrap('');
$titulo = "Painel Operacional - ".$siglaAeroporto;
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
	<style>
        iframe { width: 450px; height: 250px; }
    </style>
</head>
<body>

<div class="container-fluid">
	<div class="row">
		<main class="col-md-9 ms-sm-auto col-lg-12 px-md-4">
			<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<img class="d-inline-block align-text-top rounded-pill" src="../ativos/img/logo_medio.png" alt="logo">
				<h1 class="h2"><?php echo $titulo ?></h1>
				<div class="btn-toolbar mb-2 mb-md-0">
					<div class="btn-group me-2">
						<button type="button" class="btn btn-sm btn-outline-secondary">Compartilhar</button>
						<button type="button" class="btn btn-sm btn-outline-secondary">Exportar</button>
					</div>
						<button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1">
						Esta semana
						</button>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<aside id="graficos">
							<div class="row row-cols-auto justify-content-md-center">
								<?php      
									$dados = ['tabela'=>'MenusFormulario',
											'filtro'=>" AND me.sistema = '".$_SESSION['plantaSistema']."' AND me.modulo = 'Gráficos' AND me.href <> ''" ,
												'ordem'=>'','busca'=>''];
									$post = ['token'=>$token,'funcao'=>"Consulta",'dados'=>$dados];
									$retorno = executaAPIs('apiConsultas.php', $post);
									if ($retorno['status'] == 'OK') {
										foreach ($retorno['dados'] as $dados) {   
											echo '<div class="col">';
											echo '	<a href="'.$dados['href'].'" target="_blank">';
											echo '		<div class="card p-2 mb-1" style="min-width: 15rem;">';
											echo '      	<iframe src="'.$dados['href'].'?w=450&h=250"></iframe>';
											echo '		</div>';
											echo '	</a>';
											echo '</div>';
										}
									}
								?>          
							</div>        
						</aside>
					</div>
				</div>
			</div>
		</main>
	</div>
</div>
</body> 
</html>