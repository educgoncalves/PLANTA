<?php
// Criar o cabeçalho para retornar JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

// ********************************************************************
// Receber parametros 
// ********************************************************************
    // Pegar o metodo de recebimento
    $_metodo = $_SERVER['REQUEST_METHOD'];
    $_api = basename($_SERVER['PHP_SELF']);
    $_retorno = ['status' => 'ERRO', 'msg' => 'API '.$_api.' 999 - Erro indeterminado!'];
    $_comando = "";
    
    // Consistência e Verificação dos parâmetros 
    try {
        // GET
        if ($_metodo === 'GET') {
            $_token = (isset($_GET['token']) ? $_GET['token'] : null); 
            $_funcao = (isset($_GET['funcao']) ? $_GET['funcao'] : null); 
            $_dados = json_decode((isset($_GET['dados']) ? $_GET['dados'] : null), true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($_dados)) {
                http_response_code(400);
                throw new PDOException("API ".$_api." 400 - Faltam parâmetros para executar esta função!");
            }
        // POST
        } elseif ($_metodo === 'POST') {
            $_post = json_decode(file_get_contents('php://input'), true);

            if (json_last_error() == JSON_ERROR_NONE && !empty($_post)) {
                $_token = (isset($_post['token']) ? $_post['token'] : null); 
                $_funcao = (isset($_post['funcao']) ? $_post['funcao'] : null); 
                $_dados = (isset($_post['dados']) ? $_post['dados'] : null); 
            } else {
                http_response_code(400);
                throw new PDOException("API ".$_api." 400 - Faltam parâmetros para executar esta função!");
            }
        // OUTROS
        } else {
            http_response_code(405);
            throw new PDOException("API ".$_api." 405 - Método de requisição não suportado!");
        }

        // Validar token
        if (!validarToken($_token)) {
            throw new Exception("API ".$_api." 000 - Sem permissão para executar!");
        }

        // Verifica se parâmetros não são nulos
        if (is_null($_funcao)) {
            throw new PDOException("API ".$_api." 001 - Falta parâmetro função obrigatório para executar!");
        } elseif (is_null($_dados) || !is_array($_dados)){
            throw new PDOException("API ".$_api." 001 - Falta ou formato inválido dos dados para executar a função ".$_funcao."!");
        }

        // Validar parâmetros obrigatórios comuns a todas as funções
        $_chaves = array('id','usuario','siglaAeroporto');
        foreach($_chaves as $_chave){
            if (!array_key_exists($_chave, $_dados)) {
                throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
            }
        }        
        $id = $_dados['id'];
        $usuario = $_dados['usuario'];
        $siglaAeroporto = $_dados['siglaAeroporto'];

        // Consistência das funções e seus parâmetros
        switch ($_funcao) {
            case 'Incluir':
            case 'Alterar':
                $_chaves = array('codigo','descricao','email','situacao');
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                       throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $codigo = $_dados['codigo'];
                $descricao = $_dados['descricao'];
                $tmpTolerancia = $_dados['tmpTolerancia'];
                $email = $_dados['email'];
                $situacao = $_dados['situacao'];
            break;

            case 'Excluir':
            break;
                
            default:
                throw new PDOException("API ".$_api." 002 - Função ".$_funcao." não identificada!");
        }

    } catch (Exception $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        die(json_encode($_retorno));
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Incluir ou Alterar
// ********************************************************************
    if ($_funcao == 'Incluir' || $_funcao == 'Alterar') {
        try {
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE planta_tarefas SET codigo = '".$codigo."', descricao = '".$descricao."', tmpTolerancia = ".
                            $tmpTolerancia.", email = '".$email."', situacao = '".$situacao."', cadastro  = UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO planta_tarefas (codigo, descricao, tmpTolerancia, email, situacao, cadastro) VALUES ('".
                            $codigo."', '".$descricao."', ".$tmpTolerancia.", '".$email."', '".$situacao."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLogAPI("planta_tarefas", ($id != "" ? "Alteração" : "Inclusão"), $siglaAeroporto, $usuario, 
                                ($id != "" ? $id  : $conexao->lastInsertId()), $comando);       
                    $_retorno = ['status' => 'OK', 'msg'=> "Registro ".($id != "" ? "alterado" : "incluído")." com sucesso!"];
                } else {
                    throw new PDOException("Não foi possível efetivar esta ".($id != "" ? "alteração" : "inclusão")."!");
                }
            } else {
                throw new PDOException("Não foi possível ".($id != "" ? "alterar" : "incluir")." este registro!");
            } 
        } catch (PDOException $e) {
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())." - ".$comando];
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Excluir
// ********************************************************************
    if ($_funcao == "Excluir") {
        try {
            $conexao = conexao();
            $comando = "DELETE FROM planta_tarefas WHERE id = ".$id;
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()){
                gravaDLogAPI("planta_tarefas", "Exclusão", $siglaAeroporto, $usuario, $id, $comando);   
                $_retorno = ['status' => 'OK', 'msg'=> "Registro excluído com sucesso!"];
            } else {
                throw new PDOException("Não foi possível excluir este registro!");
            } 
        } catch (PDOException $e) {
            gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        }
    }
// ********************************************************************

// ********************************************************************
// Retorno
// ********************************************************************
    die(json_encode($_retorno));
?>