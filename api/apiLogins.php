<?php
// Criar o cabeçalho para retornar JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once("../administracao/adFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../suporte/suEnviarEmail.php");

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
        $_chaves = array('usuario');
        foreach($_chaves as $_chave){
            if (!array_key_exists($_chave, $_dados)) {
                throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
            }
        }    
        $_usuario = $_dados['usuario'];

        // Consistência das funções e seus parâmetros
        switch ($_funcao) {
            case 'UsuarioSenha':
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                        throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $_senha = $_dados['senha'];
                $_sistema = '';
            break;

            case 'UsuarioSistema':
                $_chaves = array('senha','sistema');
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                        throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $_senha = $_dados['senha'];
                $_sistema = $_dados['sistema'];
            break;

            case 'Completo':
                $_chaves = array('senha','aeroporto','sistema','identificacao');
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                        throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $_senha = $_dados['senha'];
                $_aeroporto = $_dados['aeroporto'];
                $_sistema = $_dados['sistema'];
                $_identificacao = $_dados['identificacao'];
            break;
            
            case 'UsuarioEmail':
                $_chaves = array('email');
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                        throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $_email = $_dados['email'];
            break;

            case 'AtivarConexao':
                $_chaves = array('aeroporto','sistema','grupo','identificacao');
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                        throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $_aeroporto = $_dados['aeroporto'];
                $_sistema = $_dados['sistema'];
                $_grupo = $_dados['grupo'];
                $_identificacao = $_dados['identificacao'];
            break;

            case 'DesativarConexao':
                $_chaves = array('aeroporto','sistema','identificacao');
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                        throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $_aeroporto = $_dados['aeroporto'];
                $_sistema = $_dados['sistema'];
                $_identificacao = $_dados['identificacao'];
            break;

            case 'VerificarConexao':
            case 'VerificarConexaoAtiva':                
                $_chaves = array('aeroporto','sistema','identificacao');
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                        throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $_aeroporto = $_dados['aeroporto'];
                $_sistema = $_dados['sistema'];
                $_identificacao = $_dados['identificacao'];
            break;

            case 'RecuperarSenha':
                $_chaves = array('email');
                foreach($_chaves as $_chave){
                    if (!array_key_exists($_chave, $_dados)) {
                        throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
                    }
                }
                $_email = $_dados['email'];
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
// FUNÇÃO: Usuario Senha ou Usuario Sistema
// ********************************************************************
    if ($_funcao == 'UsuarioSenha' || $_funcao == 'UsuarioSistema') {
        try {    
            $_conexao = conexao();
            $_comando = "SELECT us.id, us.usuario, ac.idAeroporto, ac.sistema, us.senha,
                            (SELECT IFNULL(COUNT(*),0) FROM gear_acessos qt WHERE qt.idUsuario = us.id) as qtdAcessos
                            FROM gear_usuarios us
                            INNER JOIN gear_acessos ac ON ac.idUsuario = us.id ".
                            (!empty($_sistema) ? " AND ac.sistema = '".$_sistema."' " : " ").
                            "WHERE ".(!empty($_usuario) ? " us.usuario = '".$_usuario.
                                "' AND " : "")." us.senha = sha1('".$_senha."') AND us.situacao = 'ATV' LIMIT 1";
            $_sql = $_conexao->prepare($_comando);
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    $_retorno = ['status'=>'OK', 'msg'=>'OK', 'dados'=>$_sql->fetchAll(PDO::FETCH_ASSOC)];
                } else {
                    throw new PDOException("Login inválido!");
                }
            } else {
                throw new PDOException("Login não pode ser executado! [".$_sql->errorInfo()."]");
            }
        } catch (PDOException $e) {
            gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Completo
// ********************************************************************
    if ($_funcao == 'Completo') {
        try {    
            $_conexao = conexao();
            $_comando = "SELECT us.id, us.usuario, us.nome, us.email, ac.grupo, dm.descricao as nivel, ae.icao as aeroporto,
                            ac.idAeroporto, ae.localidade, cl.utc, cl.tmpTaxiG1, cl.tmpTaxiG2, cl.tmpRefreshPagina, cl.tmpRefreshTela,
                            ae.nome as nomeAeroporto, IFNULL(cl.regPorPagina, 10) as regPorPagina, ac.sistema, 
                            IFNULL(cl.debug, 'SIM') as debug, cl.conexoes as conexoesPermitidas,
                            (SELECT COUNT(*) 
                                FROM gear_conexoes cn 
                                WHERE cn.idAeroporto = ac.idAeroporto AND cn.sistema = ac.sistema AND cn.grupo <> 'ADM' 
                                AND cn.identificacao <> '".$_identificacao."' AND cn.situacao = 'ATV') as conexoesAtivas
                        FROM gear_usuarios us
                        INNER JOIN gear_acessos ac ON ac.idUsuario = us.id AND ac.idAeroporto = ".$_aeroporto.
                            " AND ac.sistema = '".$_sistema."' 
                        LEFT JOIN gear_clientes cl ON cl.idAeroporto = ac.idAeroporto AND cl.sistema = ac.sistema
                        LEFT JOIN gear_aeroportos ae ON ae.id = ac.idAeroporto 
                        LEFT JOIN gear_dominios dm ON tabela = 'planta_acessos' and coluna = 'nivel' and codigo = ac.grupo 
                        WHERE us.usuario = '".$_usuario."'".
                            ($_senha != "" ? " AND us.senha = sha1('".$_senha."')" : "").
                            " AND us.situacao = 'ATV' LIMIT 1";
            $_sql = $_conexao->prepare($_comando);
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    $_retorno = ['status' => 'OK', 'msg'=> 'OK', 'dados' => $_sql->fetchAll(PDO::FETCH_ASSOC)];
                } else {
                    throw new PDOException("Login inválido!");
                }
            } else {
                throw new PDOException("Login não pode ser executado! [".$_sql->errorInfo()."]");
            }
        } catch (PDOException $e) {
            gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Usuario Email
// ********************************************************************
    if ($_funcao == 'UsuarioEmail') {
        try {    
            $_conexao = conexao();
            $_comando = "SELECT us.id, us.usuario
                            FROM gear_usuarios us
                            WHERE ".((!empty($_usuario) && $_usuario != '__GEAR__') ? " us.usuario = '".$_usuario."' AND " : "").
                                    " us.email = '".$_email."' AND us.situacao = 'ATV' LIMIT 1";
            $_sql = $_conexao->prepare($_comando);
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    $_retorno = ['status' => 'OK', 'msg'=> 'OK', 'dados' => $_sql->fetchAll(PDO::FETCH_ASSOC)];
                } else {
                    throw new PDOException(((!empty($_usuario) && $_usuario != '__GEAR__') ? "Usuário ou " : "")."E-mail inválido!");
                }
            } else {
                throw new PDOException("Login não pode ser executado! [".$_sql->errorInfo()."]");
            }
        } catch (PDOException $e) {
            gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
            die(json_encode($_retorno));
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Ativar conexão
// ********************************************************************
    if ($_funcao == 'AtivarConexao') {
        $_retorno = ativarConexao($_aeroporto, $_sistema, $_usuario, $_grupo, $_identificacao);
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Desativar conexão
// ********************************************************************
    if ($_funcao == 'DesativarConexao') {
        $_retorno = desativarConexao($_aeroporto, $_sistema, $_usuario, $_identificacao);
    } 
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Verificar conexão
// ********************************************************************
    if ($_funcao == 'VerificarConexao') {
        try {    
            $_conexao = conexao();
            $_comando = "SELECT ac.grupo, cl.conexoes as conexoesPermitidas,
                            (SELECT COUNT(*) 
                            FROM gear_conexoes cn 
                            WHERE cn.idAeroporto = cl.idAeroporto AND cn.sistema = ac.sistema AND cn.grupo <> 'ADM' 
                                AND cn.identificacao <> '".$_identificacao."' AND cn.situacao = 'ATV') as conexoesAtivas
                        FROM gear_usuarios us
                        INNER JOIN gear_acessos ac ON ac.idUsuario = us.id AND ac.idAeroporto = ".$_aeroporto." AND ac.sistema = '".$_sistema."' 
                        LEFT JOIN gear_clientes cl ON cl.idAeroporto = ac.idAeroporto AND cl.sistema = ac.sistema
                        WHERE us.usuario = '".$_usuario."' AND us.situacao = 'ATV' LIMIT 1";
            $_sql = $_conexao->prepare($_comando);
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    $_dados = $_sql->fetch(PDO::FETCH_ASSOC);
                    if ($_dados['grupo'] == 'ADM' || $_dados['conexoesPermitidas'] > $_dados['conexoesAtivas']) {
                        $_retorno = ['status' => 'OK', 'msg'=> 'OK'];
                    } else {
                        throw new PDOException("VerificarConexao - Número de conexões foram excedidas! Favor entrar em contato com o suporte.");
                    }
                } else {
                    throw new PDOException("VerificarConexao - Não consegui determinar o número de conexões para este Aeroporto! Favor entrar em contato com o suporte.");
                }
            } else {
                throw new PDOException("VerificarConexao - Não pode ser executada! [".$_sql->errorInfo()."]");
            }
        } catch (PDOException $e) {
            gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Verificar conexão ativa
// ********************************************************************
    if ($_funcao == 'VerificarConexaoAtiva') {
        try {    
            $_conexao = conexao();
            $_comando = "SELECT cn.id
                    FROM gear_conexoes cn 
                    WHERE cn.idAeroporto = ".$_aeroporto." AND cn.sistema = '".$_sistema."' AND cn.identificacao = '".
                        $_identificacao."' AND cn.usuario =  '".$_usuario."' AND cn.situacao = 'ATV' LIMIT 1";
            $_sql = $_conexao->prepare($_comando);
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    // $_dados = $_sql->fetch(PDO::FETCH_ASSOC);
                    // if ($_dados['grupo'] == 'ADM' || $_dados['conexoesPermitidas'] > $_dados['conexoesAtivas']) {
                    $_retorno = ['status' => 'OK', 'msg'=> 'OK'];
                    // } else {
                    //     throw new PDOException("Número de conexões foram excedidas! Favor entrar em contato com o suporte.");
                    // }
                } else {
                    throw new PDOException("Você não possui nenhuma conexão ativa, favor reconectar!");
                }
            } else {
                throw new PDOException("Você não possui nenhuma conexão ativa, favor reconectar! [".$_sql->errorInfo()."]");
            }
        } catch (PDOException $e) {
            gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Recuperar senha
// ********************************************************************
    if ($_funcao == 'RecuperarSenha') {
        try {   
            $_retorno = loginUsuarioEmail('',$_email);
            if($_retorno['status']=="OK"){
                $_usuario = $_retorno['usuario'];
                $_retorno = criarSenhaRandomica($_usuario,$_email);
                if($_retorno['status']=="OK"){
                    $_senha = $_retorno['senha'];
                    $_retorno = enviarEmailRecuperarSenha($_usuario,$_email,$_senha);
                }
            } else {
                throw new PDOException($_retorno['msg']);
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