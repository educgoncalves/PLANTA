<?php 
// Consiste se sigla IATA ou ICAO do pproprietário já foi utilizada
//
function siglaProprietarioDuplicada($_tipo, $_id, $_sigla){
    $_erros = "";
    $_comando = "SELECT operador FROM planta_operadores WHERE ".($_tipo == 'IATA' ? 'iata' : 'icao')." = '".$_sigla."'".
                    ($_id != "" ? " AND id <> ".$_id : "")."  LIMIT 1";
    try{
        $_conexao = conexao();
        $_sql = $_conexao->prepare($_comando);
		$_sql->execute(); 
		$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
		foreach ($_registros as $_dados) {
            $_erros = "Sigla ".$_tipo." já utlizada pelo operador ".$_dados['operador']."!";
        } 
    } catch (PDOException $e) {
        $_erros = traduzPDO($e->getMessage());
    }
    return $_erros;
}
?>