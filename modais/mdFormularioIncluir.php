<?php
// Incluir a conexao com o banco de dados
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

// Receber os dados
$mdDados = $_POST; //filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Validar o formulario
$mdErros = "";
if (empty($mdDados['mtxSistema']) || empty($mdDados['mtxFormulario'])) {
    $mdRetorna = ['status' => false, 'msg' => "<div class='alert alert-danger' role='alert'>Campos devem ser preenchidos!</div>"];
} else {
    try {
        $mdConexao = conexao();
        $mdComando = "INSERT INTO planta_menus(sistema, formulario, modulo, descricao, href, target, ordem, cadastro) VALUES ('".
                        $mdDados['mtxSistema']."','".$mdDados['mtxFormulario']."','".$mdDados['mtxModulo']."','".
                        $mdDados['mtxDescricao']."','','',0, UTC_TIMESTAMP())";
        $mdSql = $mdConexao->prepare($mdComando); 
        if ($mdSql->execute()) {
            if ($mdSql->rowCount() > 0) {
                $mdRetorna = ['status' => true, 
                    'id' => $mdConexao->lastInsertId(), 
                    'codigo' => $mdDados['mtxSistema'].'#'.$mdDados['mtxFormulario'],
                    'descricao' => $mdDados['mtxSistema'].' - '.$mdDados['mtxModulo'].' - '.$mdDados['mtxDescricao'],
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