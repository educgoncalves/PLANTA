<?php
// Criar o cabeçalho para retornar JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once("../operacional/opFuncoes.php");
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
            $_parametros = json_decode((isset($_GET['parametros']) ? $_GET['parametros'] : null), true);
            $_partida = json_decode((isset($_GET['partida']) ? $_GET['partida'] : null), true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($_parametros) || empty($_partida)) {
                http_response_code(400);
                throw new PDOException("API ".$_api." 400 - Faltam parâmetros para executar esta função!");
            }
        // POST
        } elseif ($_metodo === 'POST') {
            $_post = json_decode(file_get_contents('php://input'), true);

            if (json_last_error() == JSON_ERROR_NONE && !empty($_post)) {
                $_token = (isset($_post['token']) ? $_post['token'] : null); 
                $_funcao = (isset($_post['funcao']) ? $_post['funcao'] : null); 
                $_parametros = (isset($_post['parametros']) ? $_post['parametros'] : null); 
                $_partida = (isset($_post['partida']) ? $_post['partida'] : null); 
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
        } elseif (is_null($_parametros) || !is_array($_parametros) || is_null($_partida) || !is_array($_partida)) {
            throw new PDOException("API ".$_api." 001 - Falta ou formato inválido dos dados para executar a função ".$_funcao."!");
        }

        // Montagem do primeiro erro
        $_parametros['status'] = 'danger';
        $_parametros['mensagem'] = array('API '.$_api.' 999 - Erro indeterminado!');
        $_parametros['complemento'] = "";

        // Consistência das funções e seus parâmetros
        switch ($_funcao) {
            case 'SalvarPartida':
            break;

            case 'ExcluirMovimentoPartida':
            break;

            default:
                throw new PDOException("API ".$_api." 002 - Função ".$_funcao." não identificada!");
        }

    } catch (Exception $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()), 'complemento' => $_comando];
        $_parametros['status'] = 'danger';
        $_parametros['mensagem'] = array($_retorno['msg']);
        $_parametros['complemento'] = $_retorno['complemento'];
        $_parametros['idStatus'] = null;
        die(json_encode($_parametros));
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Salvar Partida
// ********************************************************************
    if ($_funcao == 'SalvarPartida') {
        // Inclusão ou Alteração
        // Chegada, Previsão, Movimento, outros
        $erros = "";
        $funcao = $_parametros['funcao'];
        $movimento = $_parametros['movimento'];
        //
        // Verifica críticas e consistências de acordo com o movimento que estiver salvando
        //
        $dhMovimento = mudarDataHoraAMD($_partida['dtMovimento']." ".$_partida['hrMovimento']);
        switch ($movimento) {
            case "Partida":
                $erros = vetorPreenchido($_partida, ['partida','equipamento','destino','classe','natureza','servico']);
            break;
            case "Previsão":
                $erros = vetorPreenchido($_partida, ['partida','equipamento','destino','classe','natureza','servico','movimento','dtMovimento','hrMovimento']);
            break;
            case "Movimento":
                $erros = vetorPreenchido($_partida, ['movimento','dtMovimento','hrMovimento']);
            break;
            default:
                $erros = vetorPreenchido($_partida, ['movimento','dtMovimento','hrMovimento']);
            break;
        }
        //
        // Só prossegue se tudo ok
        //
        // idChegada => id do registro de partida
        // idMovimento => id do registro do movimento que está sendo alterado
        // idUltimo => id do registro do ultimo movimento
        //
        if($erros){
            $_parametros['status'] = 'danger';
            $_parametros['mensagem'] = $erros;
            $_parametros['complemento'] = "";
        } else {
            // Monta comando de atualização de acordo com o movimento 
            $comando = "";
            $arqDLOg = "";
            $chvDLOG = "";
            switch ($movimento) {
                case "Partida":
                    if ($_parametros['idPartida'] != "") {
                        $comando = "UPDATE gear_voos_operacionais SET idAeroporto=".$_parametros['aeroporto'].
                            ",equipamento='".substr($_partida['txEquipamento'],0,100).
                            "',assentos=".mudarEmptyZeroMysql($_partida['assentos']).
                            ",classe='".$_partida['classe']."',natureza='".$_partida['natureza'].
                            "',servico='".$_partida['servico'].
                            "',destino='".substr($_partida['txDestino'],0,4).
                            "',idPosicao=".mudarEmptyNuloMysql($_partida['idPosicao']).
                            ",idPortao=".mudarEmptyNuloMysql($_partida['idPortao']).
                            ",pax=".mudarEmptyZeroMysql($_partida['pax']).
                            ",pnae=".mudarEmptyZeroMysql($_partida['pnae']).
                            ", cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idPartida'];
                    }
                    $arqDLOg = "gear_voos_operacionais";
                    $chvDLOG = $_parametros['idPartida'];
                break;
                case "Previsão":
                    $comando = "INSERT INTO gear_voos_operacionais(idAeroporto,operacao,operador,numeroVoo,". 
                                "equipamento,assentos,dtMovimento,dhPrevista,classe,natureza,". 
                                "servico,destino,idPosicao,idPortao,pax,pnae,situacao,fonte,cadastro) VALUES (". 
                                $_parametros['aeroporto'].",'PRT','".substr($_partida['voo'],0,3)."','".
                                substr($_partida['voo'],3,4)."','".
                                substr($_partida['txEquipamento'],0,100)."',".
                                mudarEmptyZeroMysql($_partida['assentos']).",UTC_TIMESTAMP(),".$dhMovimento.",'".
                                $_partida['classe']."','".$_partida['natureza']."','".
                                $_partida['servico']."','".substr($_partida['txDestino'],0,4)."',".
                                mudarEmptyNuloMysql($_partida['idPosicao']).",".
                                mudarEmptyNuloMysql($_partida['idPortao']).",".
                                mudarEmptyZeroMysql($_partida['pax']).",".
                                mudarEmptyZeroMysql($_partida['pnae']).",'ATV','".
                                $_parametros['siglaAeroporto']."', UTC_TIMESTAMP())";
                    $arqDLOg = "gear_voos_operacionais";
                break;
                case "Movimento":
                    $comando = "INSERT INTO gear_voos_movimentos (idVoo, dhMovimento, movimento, usuario, cadastro) VALUES (".
                                $_parametros['idPartida'].",".$dhMovimento.",'".$_partida['movimento']."', '".$_parametros['usuario'].
                                "', UTC_TIMESTAMP())";
                    $arqDLOg = "gear_voos_movimentos";
                break;
                default:
                    if ($_parametros['idMovimento'] != "") {
                        $comando = "UPDATE gear_voos_movimentos SET dhMovimento = ".$dhMovimento.", usuario = '".$_parametros['usuario'].
                                    "' ,cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idMovimento'];
                    }
                    $arqDLOg = "gear_voos_movimentos";
                    $chvDLOG = $_parametros['idMovimento'];
                break;
            }
            // Verifica se comando foi montado
            if ($comando != "") {
                //gravaXTrace($comando);
                try {
                    $conexao = conexao();
                    $sql = $conexao->prepare($comando);
                    if ($sql->execute()) {
                        if ($sql->rowCount() > 0) {
                            gravaDLog($arqDLOg, $funcao, $_parametros['siglaAeroporto'], $_parametros['usuario'], 
                                    ($funcao == "Alteração" ? $chvDLOG : $conexao->lastInsertId()), $comando);
                            $_parametros['status'] = "success";
                            $_parametros['mensagem'] = array("Registro ".($funcao == "Alteração" ? "alterado" : "incluído")." com sucesso!");
                            $_parametros['complemento'] = "";
                            $_parametros['idPartida'] = null;
                            $_parametros['idMovimento'] = null;
                            $_parametros['idUltimo'] = null;
                            $_parametros['funcao'] = null;
                        } else {
                            throw new PDOException("Não foi possível efetivar esta ".($funcao == "Alteração" ? "alteração" : "inclusão")."!");
                        }
                    } else {
                        throw new PDOException("Não foi possível ".($funcao == "Alteração" ? "alterar" : "incluir")." este registro!");
                    }
                } catch (PDOException $e) {
                    $_parametros['status'] = 'danger';
                    $_parametros['mensagem'] = array(traduzPDO($e->getMessage()));
                    $_parametros['complemento'] = $comando;
                }
            } else {
                $_parametros['status'] = 'danger';
                $_parametros['mensagem'] = array("Não foi possível realizar esta operação!");
                $_parametros['complemento'] = "";
            }
        }
        return $_parametros;
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Excluir Movimento Partida
// ********************************************************************
    if ($_funcao == "ExcluirMovimentoPartida") {
        try {
            $_conexao = conexao();
            $_conexao->beginTransaction();
            $_comando = "DELETE FROM gear_voos_movimentos WHERE id = ".$_parametros['idUltimo'];
            $sql = $_conexao->prepare($_comando);
            if ($sql->execute()){
                gravaDLog("gear_voos_movimentos", "Exclusão", $_parametros['siglaAeroporto'], $_parametros['usuario'], 
                            $_parametros['idUltimo'], $_comando);

                //
                // Tenta excluir a partida, se conseguir é porque era o primeiro movimento
                //
                try {
                    $_comando = "DELETE FROM gear_voos_operacionais WHERE id = ".$_parametros['idPartida'];
                    $sql = $_conexao->prepare($_comando);
                    if ($sql->execute()){
                        gravaDLog("gear_voos_operacionais", "Exclusão", $_parametros['siglaAeroporto'], $_parametros['usuario'], 
                                    $_parametros['idPartida'], $_comando);
                    }
                } catch (PDOException $e) {
                    //throw new PDOException("Não foi possível excluir o status!");
                }
                $_retorno = ['status' => 'OK', 'msg'=> "Registro excluído com sucesso!"];
                $_parametros['status'] = 'success';
                $_parametros['mensagem'] = array("Registro excluído com sucesso!");
                $_parametros['complemento'] = "";
                $_parametros['idPartida'] = null;
                $_parametros['idMovimento'] = null;
                $_parametros['idUltimo'] = null;
                $_parametros['funcao'] = null;
                $_conexao->commit();
            } else {
                throw new PDOException("Não foi possível excluir este registro!");
            } 
        } catch (PDOException $e) {
            gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()), 'complemento' => $_comando];
            $_parametros['status'] = 'danger';
            $_parametros['mensagem'] = array($_retorno['msg']);
            $_parametros['complemento'] = $_retorno['complemento'];
            $_parametros['funcao'] = null;
            if ($_conexao->inTransaction()) { $_conexao->rollBack(); }
        }        
    }
// ********************************************************************

// ********************************************************************
// Retorno
// ********************************************************************
    die(json_encode($_parametros));
?>