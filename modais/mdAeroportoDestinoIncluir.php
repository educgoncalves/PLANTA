<?php
// Incluir a conexao com o banco de dados
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

// Receber os dados
$mdDados = $_POST; //filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Validar o formulario
$mdErros = "";
if (empty($mdDados['dtxIcao']) || empty($mdDados['dtxNome']) || empty($mdDados['dtxLocalidade']) || empty($mdDados['dtxPais'])) {
    $mdRetorna = ['status' => false, 'msg' => "<div class='alert alert-danger' role='alert'>Campos devem ser preenchidos!</div>"];
} else {
    try {
        $mdConexao = conexao();
        $mdComando = "INSERT INTO planta_aeroportos (iata, icao, nome, localidade, pais, situacao, origem, fonte, cadastro) VALUES ('".
            $mdDados['dtxIata']."', '".$mdDados['dtxIcao']."', '".$mdDados['dtxNome']."', '".$mdDados['dtxLocalidade']."', '".
            $mdDados['dtxPais']."', 'ATV','MNL','".$_SESSION['plantaSite']."', UTC_TIMESTAMP())";
        $mdSql = $mdConexao->prepare($mdComando); 
        if ($mdSql->execute()) {
            if ($mdSql->rowCount() > 0) {
                $mdRetorna = ['status' => true, 
                    'id' => $mdConexao->lastInsertId(), 
                    'codigo' => $mdDados['dtxIcao'],
                    'descricao' => $mdDados['dtxIcao'].' - '.$mdDados['dtxLocalidade'],
                    'msg' => "<div class='alert alert-success' role='alert'>Registro cadastrado com sucesso!</div>"];
            } else {
                throw new PDOException("Não foi possível incluir este registro!");
            }
        } else {
            throw new PDOException("Não foi possível incluir este registro!");
        } 
    } catch (PDOException $e) {
        $mdRetorna = ['status' => false, 'msg' => "<div class='alert alert-danger' role='alert'>".traduzPDO($e->getMessage())."!</div>"];
    }
}

// Retornar a resposta para o JavaScript em formato de objeto
echo json_encode($mdRetorna);
?>