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
                //http_response_code(400);
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
                //http_response_code(400);
                throw new PDOException("API ".$_api." 400 - Faltam parâmetros para executar esta função!");
            }
        // OUTROS
        } else {
            //http_response_code(405);
            throw new PDOException("API ".$_api." 405 - Método de requisição não suportado!");
        }

        // Validar token
        if (!validarToken($_token)) {
            throw new Exception("API ".$_api." 000 - sem permissão para executar!");
        }

        // Verifica se parâmetros não são nulos
        if (is_null($_funcao) || is_null($_dados) || !is_array($_dados)){
            throw new PDOException("API ".$_api." 001 - Faltam parâmetros obrigatórios para executar!");
        }

        // Verifica se função está prevista
        $_funcoes = array("MontarMenu");
        if (!in_array($_funcao,$_funcoes)) {
            throw new PDOException("API ".$_api." 002 - [".$_funcao."] função não identificada!");
        }

        // Validar parâmetros obrigatórios comuns a todas as funções
        $_chaves = array('sistema','site','grupo');
        foreach($_chaves as $_chave){
            if (!array_key_exists($_chave, $_dados)) {
                throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Faltam parâmetros obrigatórios para executar!");
            }
        }    
        $_sistema = $_dados['sistema'];
        $_site = $_dados['site'];
        $_grupo = $_dados['grupo'];

        // Consistência das funções e seus parâmetros
        switch ($_funcao) {
            case 'MontarMenu':
            break;
        }

    } catch (Exception $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        die(json_encode($_retorno));
    }
// ********************************************************************    

// ********************************************************************
// FUNÇÃO: Montar Menu
// ********************************************************************
    if ($_funcao == 'MontarMenu') {
        try {    
            $_conexao = conexao();
            $_comando = "SELECT me.tipo, me.formulario, me.modulo, me.descricao, me.href, me.target, me.atalho, me.iconeSVG
                        FROM planta_menus me 
                        WHERE me.sistema = '".$_sistema."'  AND NOT EXISTS (SELECT re.formulario ". 
                        " FROM planta_restricoes re WHERE re.idSite = ".$_site." AND re.sistema = me.sistema ". 
                        " AND re.formulario = me.formulario AND re.grupo = '".$_grupo."') ORDER BY me.sistema, me.ordem, me.formulario";
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