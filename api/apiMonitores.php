<?php
// Criar o cabeçalho para retornar JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once("../administracao/adFuncoes.php");
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
        $_chaves = array('id','identificacao','hash');
        foreach($_chaves as $_chave){
            if (!array_key_exists($_chave, $_dados)) {
                throw new PDOException("API ".$_api." 003 - [".$_funcao."] - Falta parâmetro [".$_chave."] obrigatório para executar!");
            }
        }    
        $_id = $_dados['id'];
        $_identificacao = $_dados['identificacao'];
        $_aeroporto = substr($_identificacao,0,4);
        $_monitor = substr($_identificacao,4,3);
        $_hash = $_dados['hash'];
        $_retorno = apiMonitorIdAeroporto($_identificacao);
        if ($_retorno['status'] == 'OK') {
            $_idAeroporto = $_retorno['idAeroporto'];
        } else {
            throw new PDOException("API ".$_api." 004 - Função ".$_funcao." - ".$_retorno['msg']);
        }

        // Consistência das funções e seus parâmetros
        switch ($_funcao) {
            case 'Ativar':
            case 'Acao':
            case 'Desativar':
            break;

            default:
                throw new PDOException("API ".$_api." 002 - Função ".$_funcao." não identificada!");
        }

    } catch (PDOException $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
        die(json_encode($_retorno));
    }
// ********************************************************************    

// ********************************************************************
// FUNÇÃO: Ativar monitor
// ********************************************************************
    if ($_funcao == 'Ativar') {
        $_retorno = apiMonitorAtivar($_id, $_idAeroporto, $_identificacao, $_monitor, $_hash, 'MAER', 'monitores', 'MNT');
        if ($_retorno['status'] == 'ERRO') {
            $_retorno['msg'] = "API ".$_api." 004 - Função ".$_funcao." - ".$_retorno['msg'];
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Recuperar as ações para os monitores
// ********************************************************************
    if ($_funcao == 'Acao') {
        try {    
            $_conexao = conexao();
            $_comando = "SELECT mp.id, dm2.descricao as acao, mp.pagina, mp.segundos, dm3.descricao as resolucao
                        FROM gear_monitores_paginas mp 
                        INNER JOIN gear_monitores mt ON mt.id = mp.idMonitor 
                        LEFT JOIN gear_dominios dm2 ON dm2.tabela = 'planta_monitores_paginas' and dm2.coluna = 'acao' and dm2.codigo = mp.acao
                        LEFT JOIN gear_dominios dm3 ON dm3.tabela = 'planta_monitores_paginas' and dm3.coluna = 'resolucao' and dm3.codigo = mp.resolucao
                        WHERE mp.situacao = 'ATV' AND mt.situacao = 'ATV' AND mt.idAeroporto = ".$_idAeroporto." AND mt.numero = '".$_monitor."'
                        ORDER BY mp.id";
            $_sql = $_conexao->prepare($_comando);
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    $_dados = $_sql->fetchAll(PDO::FETCH_ASSOC);
                    $_retorno = ['status' => 'OK', 'dados' => $_dados];
                } else {
                    throw new PDOException("Não existe página definida para este monitor.");
                }
            } else {
                throw new PDOException("Recuperação das páginas deste monitor não pode ser executada! [".$_sql->errorInfo()."]");
            }
        } catch (PDOException $e) {
            gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => "API ".$_api." 004 - Função ".$_funcao." - ".traduzPDO($e->getMessage())];
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Desativar
// ********************************************************************
    if ($_funcao == 'Desativar') {
        $_retorno = apiMonitorDesativar($_id, 'MAER', 'monitores', $_identificacao);
        if ($_retorno['status'] == 'ERRO') {
            $_retorno['mag'] = "API ".$_api." 004 - Função ".$_funcao." - ".$_retorno['msg'];
        }
    }
// ********************************************************************

// ********************************************************************
// Retorno
// ********************************************************************
    die(json_encode($_retorno));

// ********************************************************************
// FUNÇÃO: Ativar Monitores
// ********************************************************************
function apiMonitorAtivar($__id, $__idAeroporto, $__identificacao, $__monitor, $__hash, $__sistema, $__usuario, $__grupo) {
    $__retorno = ['status' => 'ERRO', 'msg' => 'Erro indeterminado'];
    $__comando = '';

    try {
        // Se primeira ativação 
        //
        if ($__id == 0) {
            // Verificar o hash - retorna OK se conseguiu registrar ou o hash está registrado no mesmo monitor
            //
            $__retorno = apiMonitorHash($__idAeroporto, $__identificacao, $__monitor, $__hash);
            if ($__retorno['status'] == 'ERRO') {
                throw new PDOException($__retorno['msg']);
            }

            // Verificar se a mesma identificação já está ativa
            //
            $__conexao = conexao();
            $__comando = "SELECT id FROM gear_conexoes WHERE situacao = 'ATV' AND identificacao = '".$__identificacao."'";
            $__sql = $__conexao->prepare($__comando);
            if ($__sql->execute()) {
                if ($__sql->rowCount() > 0) {
                    // Renovar a conexão
                    $__dados = $__sql->fetch(PDO::FETCH_ASSOC);
                    $__id = $__dados['id'];
                    $__retorno = apiMonitorRenovarConexao($__id, $__identificacao);
                    if ($__retorno['status'] == 'ERRO') {
                        throw new PDOException($__retorno['msg']);
                    }
                   
                } else {
                    // Ativar a conexão se puder
                    //
                    $__retorno = apiMonitorNumeroConexoes($__idAeroporto, $__sistema);
                    if ($__retorno['status'] == 'ERRO') {
                        throw new PDOException($__retorno['msg']);
                    }

                    // Ativar conexão
                    //
                    $__comando = "INSERT INTO gear_conexoes(idAeroporto, sistema, usuario, grupo, identificacao, entrada, cadastro) VALUES (".
                        $__idAeroporto.", '".$__sistema."', '".$__usuario."', '".$__grupo."', '".$__identificacao.
                        "', UTC_TIMESTAMP(), UTC_TIMESTAMP())";
                    $__sql = $__conexao->prepare($__comando);
                    if ($__sql->execute()) {
                        if ($__sql->rowCount() > 0) {
                            $__retorno = ['status' => 'OK', 'id' => $__conexao->lastInsertId()];
                        } else {
                            throw new PDOException("Não consegui ativar o monitor ".$__identificacao."!");
                        }
                    } else {
                        throw new PDOException("Ativação da monitor não pode ser executada! [".$__sql->errorInfo()."]");
                    }                            
                }
            } else {
                throw new PDOException("Não consegui identificar se o monitor já está ativo!");
            }                
        // Renovando a ativação
        } else {
            $__retorno = apiMonitorRenovarConexao($__id, $__identificacao);
            if ($__retorno['status'] == 'ERRO') {
                throw new PDOException($__retorno['msg']);
            }
        }
    } catch (PDOException $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$__comando);
        $__retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }
    return $__retorno;
}

// ********************************************************************
// FUNÇÃO: Desativar Monitores
// ********************************************************************
function apiMonitorDesativar($__id, $__sistema, $__usuario, $__identificacao) {
    $__retorno = ['status' => 'ERRO', 'msg' => 'Erro indeterminado'];
    $__comando = '';

    try {
		// Pegando usuário do login
		$__conexao = conexao();
        $__comando = "UPDATE gear_conexoes SET saida = UTC_TIMESTAMP(), situacao = 'INA' WHERE situacao = 'ATV' ".
                    ($__id != "" ? " AND id = ".$__id : "").
                    ($__sistema != "" ? " AND sistema = '".$__sistema."'" : "").
                    ($__usuario != "" ? " AND usuario = '".$__usuario."'" : "").
                    ($__identificacao != "" ? " AND identificacao = '".$__identificacao."'" : ""); 
        $__sql = $__conexao->prepare($__comando);
        if ($__sql->execute()) {
			if ($__sql->rowCount() > 0) {
                $__retorno = ['status' => 'OK', 'msg' => 'Monitor desativado!'];
            } else {
                throw new PDOException("Não consegui desativar o monitor");
            }
        } else {
            throw new PDOException("Desativação do monitor não pode ser executada! [".$__sql->errorInfo()."]");
        }
    } catch (PDOException $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$__comando);
        $__retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }
    return $__retorno;
}

// ********************************************************************
// FUNÇÃO: Verificar número de conexões
// ********************************************************************
function apiMonitorNumeroConexoes($__idAeroporto, $__sistema) {
    $__retorno = ['status' => 'ERRO', 'msg' => 'Erro indeterminado'];
    $__comando = '';

    try {    
        $__conexao = conexao();
        $__comando = "SELECT cl.conexoes as conexoesPermitidas,
                        (SELECT COUNT(*) 
                        FROM gear_conexoes cn 
                        WHERE cn.idAeroporto = cl.idAeroporto AND cn.sistema = cl.sistema AND cn.grupo <> 'ADM' 
                            AND cn.situacao = 'ATV') as conexoesAtivas
                    FROM gear_clientes cl
                    WHERE cl.sistema = '".$__sistema."' AND cl.idAeroporto = ".$__idAeroporto;
        $__sql = $__conexao->prepare($__comando);
        if ($__sql->execute()) {
            if ($__sql->rowCount() > 0) {
                $__dados = $__sql->fetch(PDO::FETCH_ASSOC);
                if ($__dados['conexoesPermitidas'] > $__dados['conexoesAtivas']) {
                    $__retorno = ['status' => 'OK', 'msg' => 'Conexão permitida!'];
                } else {
                    throw new PDOException("Número de conexões foram excedidas!");
                }
            } else {
                throw new PDOException("Não consegui determinar o número de conexões para este Aeroporto!");
            }
        } else {
            throw new PDOException("Verificação do número de conexões não pode ser executada! [".$__sql->errorInfo()."]");
        }
    } catch (PDOException $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$__comando);
        $__retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }
    return $__retorno;
}
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Verificar hash
// ********************************************************************
function apiMonitorHash($__idAeroporto, $__identificacao, $__monitor, $__hash) {
    $__retorno = ['status' => 'ERRO', 'msg' => 'Erro indeterminado'];
    $__comando = '';

    try {    
        // Verificar se o hash existe e se a identificação é a mesma
        //
        $__conexao = conexao();
        $__comando = "SELECT mt.id, CONCAT(ae.icao,mt.numero) as identificacao 
                    FROM gear_monitores mt 
                    LEFT JOIN gear_aeroportos ae ON ae.id = mt.idAeroporto
                    WHERE mt.hash = '".$__hash."'";
        $__sql = $__conexao->prepare($__comando);
        if ($__sql->execute()) {
            if ($__sql->rowCount() > 0) {
                $__dados = $__sql->fetch(PDO::FETCH_ASSOC);
                if ($__dados['identificacao'] == $__identificacao) {
                    $__retorno = ['status' => 'OK', 'msg' => 'CORRETO'];
                } else {
                    throw new PDOException("Hash pertence ao monitor ".$__dados['identificacao']."!");
                }
            } else {
                // Verificar se a identificação já tem algum hash registrado
                //
                $__comando = "SELECT mt.id
                        FROM gear_monitores mt 
                        WHERE IFNULL(mt.hash,'') = '' AND mt.idAeroporto = ".$__idAeroporto." AND mt.numero = '".$__monitor."'";
                $__sql = $__conexao->prepare($__comando);
                if ($__sql->execute()) {
                    if ($__sql->rowCount() >= 0) {
                        $__retorno = ['status' => 'OK', 'msg' => 'REGISTRAR'];
                    } else {
                        throw new PDOException("Monitor registrado anteriormente com outro hash!");
                    }
                } else {
                    throw new PDOException("Verificação de registro do monitor não pode ser executada! [".$__sql->errorInfo()."]");
                }
            }
        } else {
            throw new PDOException("Verificação do hash não pode ser executada! [".$__sql->errorInfo()."]");
        }
 
        // Registrar o hash
        //
        if ($__retorno['msg'] == 'REGISTRAR') {
            $__comando = "UPDATE gear_monitores SET hash = '".$__hash."' 
                        WHERE idAeroporto = ".$__idAeroporto." AND numero = '".$__monitor."'";
            $__sql = $__conexao->prepare($__comando);
            if ($__sql->execute()) {
                if ($__sql->rowCount() > 0) {
                    $__retorno = ['status' => 'OK', 'msg' => 'REGISTRADO'];
                } else {
                    throw new PDOException("Não consegui registrar o hash para o monitor ".$__identificacao."!");
                }
            } else {
                throw new PDOException("Registro do hash não pode ser executado! [".$__sql->errorInfo()."]");
            }
        }
    } catch (PDOException $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$__comando);
        $__retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }
    return $__retorno;
}
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Identificar o idAeroporto do monitor
// ********************************************************************
function apiMonitorIdAeroporto($__identificacao) {
    $__retorno = ['status' => 'ERRO', 'msg' => 'Erro indeterminado'];
    $__comando = '';

    try {    
        $__conexao = conexao();
        $__comando = "SELECT ae.id FROM gear_aeroportos ae WHERE ae.icao = '".substr($__identificacao,0,4)."'";
        $__sql = $__conexao->prepare($__comando);
        if ($__sql->execute()) {
            if ($__sql->rowCount() > 0) {
                $__dados = $__sql->fetch(PDO::FETCH_ASSOC);
                $__retorno = ['status' => 'OK', 'idAeroporto' => $__dados['id']];
            } else {
                throw new PDOException("Não identifiquei o aeroporto deste monitor!");
            }
        } else {
            throw new PDOException("Verificação do aeroporto deste monitor não pode ser executada! [".$__sql->errorInfo()."]");
        }
    } catch (PDOException $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$__comando);
        $__retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }
    return $__retorno;
}
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Renovar conexão
// ********************************************************************
function apiMonitorRenovarConexao($__id, $__identificacao) {
    $__retorno = ['status' => 'ERRO', 'msg' => 'Erro indeterminado'];
    $__comando = '';

    try {    
        $__conexao = conexao();
        $__comando = "UPDATE gear_conexoes SET entrada = UTC_TIMESTAMP() WHERE situacao = 'ATV' ".
                        ($__id != "" ? " AND id = ".$__id : "").
                        ($__identificacao != "" ? " AND identificacao = '".$__identificacao."'" : ""); 
        $__sql = $__conexao->prepare($__comando);
        if ($__sql->execute()) {
            if ($__sql->rowCount() > 0) {
                $__retorno = ['status' => 'OK', 'id' => $__id];
            } else {
                throw new PDOException("Não consegui renovar a ativação do monitor ".$__identificacao."!");
            }
        } else {
            throw new PDOException("Renovação do monitor não pode ser executada! [".$__sql->errorInfo()."]");
        }
    } catch (PDOException $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$__comando);
        $__retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }
    return $__retorno;
}
// ********************************************************************
?>