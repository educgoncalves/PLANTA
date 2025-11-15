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
            case 'RecuperarPropaganda':
            break;

            case 'RegistrarExibicao':
                $_chaves = array('propaganda','origem','tela');
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                        throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $propaganda = $_dados['propaganda'];
                $origem = $_dados['origem'];
                $tela = $_dados['tela'];
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
// FUNÇÃO: Recuperar Propaganda
// ********************************************************************
    if ($_funcao == 'RecuperarPropaganda') {
        try {    
            $_conexao = conexao();
            $_comando = "SELECT pg.id, pg.propaganda 
                            FROM gear_propagandas pg 
                            WHERE pg.idAeroporto = ".$id." AND pg.situacao = 'EXB'";
            $_sql = $_conexao->prepare($_comando);
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    $_retorno = ['status' => 'OK', 'msg'=> 'OK', 'dados' => $_sql->fetchAll(PDO::FETCH_ASSOC)];
                } else {
                    throw new PDOException($_funcao." não retornou nenhum registro");
                }
            } else {
                throw new PDOException($_funcao." não pode ser executada! [".$_sql->errorInfo()."]");
            }
        } catch (PDOException $e) {
            //gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: RegistrarExibicao
// ********************************************************************
    if ($_funcao == 'RegistrarExibicao') {
        gravaDLogAPI("gear_propagandas", "Exibição", $siglaAeroporto, $usuario, $id, $propaganda,"Tela: ".$tela." - Origem: ".$origem);
        $_retorno = ['status' => 'OK', 'msg' => $_funcao.' gravando LOG!'];
    }
// ********************************************************************

// ********************************************************************
// Retorno
// ********************************************************************
    die(json_encode($_retorno));
?>