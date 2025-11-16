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
        // if (!validarToken($_token)) {
        //     throw new Exception("API ".$_api." 000 - Sem permissão para executar!");
        // }

        // Verifica se parâmetros não são nulos
        if (is_null($_funcao)) {
            throw new PDOException("API ".$_api." 001 - Falta parâmetro função obrigatório para executar!");
        } elseif (is_null($_dados) || !is_array($_dados)){
            throw new PDOException("API ".$_api." 001 - Falta ou formato inválido dos dados para executar a função ".$_funcao."!");
        }

        // Validar parâmetros obrigatórios comuns a todas as funções
        $_chaves = array('usuario','senha');
        foreach($_chaves as $_chave){
            if (!array_key_exists($_chave, $_dados)) {
                throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Faltam parâmetros obrigatórios para executar!");
            }
        }        
        $_usuario = $_dados['usuario'];
        $_senha = $_dados['senha'];    

        // Consistência das funções e seus parâmetros
        $_conexao = conexao();
        $_comando = "SELECT us.id FROM planta_usuarios us WHERE us.usuario = '".$_usuario.
                    "' AND us.senha = sha1('".$_senha."') AND us.situacao = 'ATV' LIMIT 1";
        $_sql = $_conexao->prepare($_comando);
        if ($_sql->execute()) {
            if ($_sql->rowCount() > 0) {
                switch ($_funcao) {
                    case 'Gerar':
                    break;

                    case 'Validar':
                        if (is_null($_token)) {
                            throw new PDOException("API ".$_api." 004 - [".$_funcao."] - Faltam parâmetros obrigatórios para executar!");
                        } 
                    break;

                    default:
                        throw new PDOException("API ".$_api." 002 - Função ".$_funcao." não identificada!");
                }
            } else {
                throw new PDOException("API ".$_api." 000 - [".$_funcao."] sem permissão para executar!");
            }
        } else {
            throw new PDOException("API ".$_api." 000 - [".$_funcao."] sem permissão para executar! [".$_sql->errorInfo()."] ".$_comando);
        }

    } catch (PDOException $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        die(json_encode($_retorno));
    }
// ********************************************************************
    
// ********************************************************************
// FUNÇÃO: Gerar
// ********************************************************************
    if ($_funcao == 'Gerar') {
        $_retorno = ['status' => 'OK', 'token' => gerarToken($_usuario)];
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Validar
// ********************************************************************
    if ($_funcao == 'Validar') {
        $_retorno = ['status' => (validarToken($_token) ? 'OK' : 'ERRO'), 'token' => $_token];
    }
// ********************************************************************

// ********************************************************************
// Retorno
// ********************************************************************
    die(json_encode($_retorno));
?>    
