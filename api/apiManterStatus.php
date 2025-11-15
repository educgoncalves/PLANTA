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
            $_status = json_decode((isset($_GET['status']) ? $_GET['status'] : null), true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($_parametros) || empty($_status)) {
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
                $_status = (isset($_post['status']) ? $_post['status'] : null); 
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
        } elseif (is_null($_parametros) || !is_array($_parametros) || is_null($_status) || !is_array($_status)) {
            throw new PDOException("API ".$_api." 001 - Falta ou formato inválido dos dados para executar a função ".$_funcao."!");
        }

        // Montagem do primeiro erro
        $_parametros['status'] = 'danger';
        $_parametros['mensagem'] = array('API '.$_api.' 999 - Erro indeterminado!');
        $_parametros['complemento'] = "";

        // Consistência das funções e seus parâmetros
        switch ($_funcao) {
            case 'SalvarStatus':
            break;

            case 'ExcluirMovimentoStatus':
            break;

            case 'CorrigirStatus':
               $_status = corrigirCamposStatus('origem', $_status);
               die(json_encode($_status));
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
// FUNÇÃO: Salvar Status
// ********************************************************************
    if ($_funcao == 'SalvarStatus') {
        try {
            // Abrir transação
            $_conexao = conexao();
            $_conexao->beginTransaction();

            // Verifica se data e hora do movimento é maior que a data e hora atual
            //
            $_erros = dataHoraMovimentoAtual($_parametros,$_status);
            if ($_erros != "") { throw new PDOException($_erros); } 
            
            // Críticas e consistências
            $erros = array();
            $_movimento = ($_parametros['movimento'] == "Movimento" ? $_status['txMovimento'] : $_parametros['movimento']);
            switch ($_movimento) {
                case "Status":
                    $erros = vetorPreenchido($_status, ['matricula','origem','movimento','dtMovimento','hrMovimento'], array(), false);
                    if (!$erros) {
                        ($_erros = matriculaStatusAberto($_parametros,$_status)) != "" ? $erros[] = $_erros : "";
                    }
                break;
                case "Previsão":
                    $erros = vetorPreenchido($_status, ['matricula','origem','movimento','dtMovimento','hrMovimento'], array(), false);
                    if (!$erros) {
                        ($_erros = matriculaStatusAberto($_parametros,$_status)) != "" ? $erros[] = $_erros : "";
                    }
                break;
                case "Pouso":
                        $erros = vetorPreenchido($_status, ['matricula','origem','movimento','dtMovimento','hrMovimento','recurso'], array(), false);
                        if (!$erros) {
                            ($_erros = matriculaStatusAberto($_parametros,$_status)) != "" ? $erros[] = $_erros : "";
                        }
                break;
                case "Entrada":
                        $erros = vetorPreenchido($_status, ['matricula','origem','movimento','dtMovimento','hrMovimento','recurso'], array(), false);
                        if (!$erros) {
                            //($_erros = posicaoOcupada($_parametros,$_status)) != "" ? $erros[] = $_erros : "";
                            ($_erros = dataHoraMovimentoAnterior($_parametros,$_status)) != "" ? $erros[] = $_erros : "";
                        }
                break;
                case "Saída":
                    $erros = vetorPreenchido($_status, ['matricula','origem','movimento','dtMovimento','hrMovimento','recurso'], array(), false);
                    if (!$erros) {
                        ($_erros = dataHoraMovimentoAnterior($_parametros,$_status)) != "" ? $erros[] = $_erros : "";
                    }
                    break;
                case "Decolagem":
                    $erros = vetorPreenchido($_status, ['matricula','origem','movimento','dtMovimento','hrMovimento','destino','recurso'], array(), false);
                    if (!$erros) {
                        ($_erros = dataHoraMovimentoAnterior($_parametros,$_status)) != "" ? $erros[] = $_erros : "";
                    }
                    break;
                default:
                    $erros = vetorPreenchido($_status, ['matricula','movimento','dtMovimento','hrMovimento'], array(), false);
                    if (!$erros) {
                        ($_erros = matriculaStatusAberto($_parametros,$_status)) != "" ? $erros[] = $_erros : "";
                    }
                break;                
            }

            // Correção da classe, natureza e serviço
            // if (!$erros) {
            //     $_campos = corrigirCamposStatus($_status['txMatricula'], $_status['txOrigem']);
            //     $_status['classe'] = ($_campos['classe'] !== '' ? $_campos['classe'] : $_status['classe']);
            //     $_status['natureza'] = ($_campos['natureza'] !== '' ? $_campos['natureza'] : $_status['natureza']);
            //     $_status['servico'] = ($_campos['servico'] !== '' ? $_campos['servico'] : $_status['servico']);
            // }

            // Só prossegue se tudo ok
            //
            // idStatus => id do registro do status
            // idMovimento => id do registro do movimento que está sendo alterado
            // idUltimo => id do registro do ultimo movimento
            //
            if ($erros) {
                $_parametros['status'] = 'danger';
                $_parametros['mensagem'] = $erros;
                $_parametros['complemento'] = "";
            } else {
                if ($_movimento == "Status") {
                    $_parametros = atualizarStatus($_conexao, $_parametros, $_status);
                } else {
                    // Ajusta campos 
                    $_status['segundoRecurso'] = ($_status['segundoRecurso'] === '' || $_status['segundoRecurso'] === '0' ? 'null' : $_status['segundoRecurso']);
                    $_dhMovimento = mudarDataHoraAMD($_status['dtMovimento']." ".$_status['hrMovimento']);
                    switch ($_movimento) {
                        case "PRV":
                        case "Previsão":
                            $_parametros = atualizarStatus($_conexao, $_parametros, $_status);
                            if ($_parametros['idStatus'] != null) {
                                $_comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, usuario, cadastro) VALUES (".
                                            $_parametros['idStatus'].", ".$_dhMovimento.", 'PRV', '".$_parametros['usuario']."', UTC_TIMESTAMP())";
                            } else {
                                throw new PDOException("Não foi possível criar um status para esta previsão!");
                            }
                        break;

                        case "POU":
                        case "Pouso":
                            if ($_parametros['idMovimento'] != "") {
                                $_comando = "UPDATE gear_status_movimentos SET movimento = 'POU', dhMovimento = ".$_dhMovimento.
                                            ", idRecurso = ".$_status['recurso'].", idSegundoRecurso = ".$_status['segundoRecurso'].
                                            ", usuario = '".$_parametros['usuario']."', cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idMovimento'];
                            } else {
                                $_parametros = atualizarStatus($_conexao, $_parametros, $_status);
                                if ($_parametros['idStatus'] != null) {
                                    $_comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, idRecurso, idSegundoRecurso, usuario, cadastro) VALUES ("
                                                .$_parametros['idStatus'].", ".$_dhMovimento.", 'POU', ".$_status['recurso'].", "
                                                .$_status['segundoRecurso'].", '".$_parametros['usuario']."', UTC_TIMESTAMP())";
                                } else {
                                    throw new PDOException("Não foi possível criar um status para este pouso!");
                                }
                            }
                        break;

                        case "ENT":
                        case "Entrada":
                            if ($_parametros['idMovimento'] != "") {
                                $_comando = "UPDATE gear_status_movimentos SET dhMovimento = ".$_dhMovimento.", idRecurso = ".$_status['recurso'].
                                            ", idSegundoRecurso = ".$_status['segundoRecurso'].", usuario = '".$_parametros['usuario'].
                                            "', cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idMovimento'];
                            } else {
                                $_comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, idRecurso, idSegundoRecurso, usuario, cadastro) VALUES ("
                                            .$_parametros['idStatus'].", ".$_dhMovimento.", 'ENT', ".$_status['recurso'].", "
                                            .$_status['segundoRecurso'].", '".$_parametros['usuario']."', UTC_TIMESTAMP())";
                            }
                        break;

                        case "SAI":
                        case "Saída":
                            if ($_parametros['idMovimento'] != "") {
                                $_comando = "UPDATE gear_status_movimentos SET dhMovimento = ".$_dhMovimento.", idRecurso = ".$_status['recurso'].
                                            ", idSegundoRecurso = ".$_status['segundoRecurso'].", usuario = '".$_parametros['usuario'].
                                            "', cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idMovimento'];
                            } else {
                                $_comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, idRecurso, idSegundoRecurso, usuario, cadastro) VALUES ("
                                            .$_parametros['idStatus'].", ".$_dhMovimento.", 'SAI', ".$_status['recurso'].", ".$_status['segundoRecurso'].
                                            ", '".$_parametros['usuario']."', UTC_TIMESTAMP())";
                            }
                        break;

                        case "DEC":
                        case "Decolagem":
                            if ($_parametros['idMovimento'] != "") {
                                $_comando = "UPDATE gear_status_movimentos SET dhMovimento = ".$_dhMovimento.", idRecurso = ".$_status['recurso'].
                                            ", idSegundoRecurso = ".$_status['segundoRecurso'].", usuario = '".$_parametros['usuario'].
                                            "', cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idMovimento'];
                            } else {
                                $_parametros = atualizarStatus($_conexao, $_parametros, $_status);
                                if ($_parametros['idStatus'] != null) {
                                    $_comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, idRecurso, idSegundoRecurso, usuario, cadastro) VALUES ("
                                                .$_parametros['idStatus'].", ".$_dhMovimento.", 'DEC', ".$_status['recurso'].", ".$_status['segundoRecurso'].
                                                ", '".$_parametros['usuario']."', UTC_TIMESTAMP())";
                                } else {
                                    throw new PDOException("Não foi possível atualizar o status para esta decolagem!");
                                }
                            }
                        break;

                        default:
                            if ($_parametros['idMovimento'] != "") {
                                $_comando = "UPDATE gear_status_movimentos SET dhMovimento = ".$_dhMovimento.
                                            ", usuario = '".$_parametros['usuario']."', cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idMovimento'];
                            } else {
                                $_comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, usuario, cadastro) VALUES ("
                                            .$_parametros['idStatus'].", ".$_dhMovimento.", '".$_status['movimento']."', '".$_parametros['usuario'].
                                            "', UTC_TIMESTAMP())";
                            }
                        break;
                    }
                    $_sql = $_conexao->prepare($_comando);
                    if ($_sql->execute()) {
                        if ($_sql->rowCount() > 0) {
                            gravaDLogAPI("gear_status_movimentos", ($_parametros['idMovimento'] != "" ? "Alteração" : "Inclusão"), $_parametros['siglaAeroporto'],
                                        $_parametros['usuario'], ($_parametros['idMovimento'] != "" ? $_parametros['idMovimento']  : $_conexao->lastInsertId()), $_comando);
                            $_retorno = ['status' => 'OK', 'msg'=> "Registro ".($_parametros['idMovimento'] != "" ? "alterado" : "incluído")." com sucesso!"];
                            $_parametros['status'] = "success";
                            $_parametros['mensagem'] = array("Registro ".($_parametros['idMovimento'] != "" ? "alterado" : "incluído")." com sucesso!");
                            $_parametros['complemento'] = "";
                            $_parametros['idStatus'] = null;
                            $_parametros['idMovimento'] = null;
                            $_parametros['idUltimo'] = null;
                            $_parametros['funcao'] = null;                            
                        } else {
                            throw new PDOException("Não foi possível efetivar esta ".($_parametros['idMovimento'] != "" ? "alteração" : "inclusão")."!");
                        }
                    } else {
                        throw new PDOException("Não foi possível ".($_parametros['idMovimento'] != "" ? "alterar" : "incluir")." este registro!");
                    }
                }
                // Comitar transação
                if ($_conexao->inTransaction()) { $_conexao->commit(); }
            }
        } catch (PDOException $e) {
            gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
            $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()), 'complemento' => $_comando];
            $_parametros['status'] = 'danger';
            $_parametros['mensagem'] = array($_retorno['msg']);
            $_parametros['complemento'] = $_retorno['complemento'];
            $_parametros['idStatus'] = null;
            // Rollback
            if ($_conexao->inTransaction()) { $_conexao->rollBack(); }
        }
    }
// ********************************************************************

// ********************************************************************
// FUNÇÃO: Excluir Movimento Status
// ********************************************************************
    if ($_funcao == "ExcluirMovimentoStatus") {
        try {
            $_conexao = conexao();
            $_conexao->beginTransaction();
            $_comando = "DELETE FROM gear_status_movimentos WHERE id = ".$_parametros['idUltimo'];
            $_sql = $_conexao->prepare($_comando); 
            if ($_sql->execute()){
                gravaDLogAPI("gear_status_movimentos", "Exclusão", $_parametros['siglaAeroporto'], $_parametros['usuario'], 
                            $_parametros['idUltimo'], $_comando);

                //
                // Tenta excluir o status, se conseguir é porque era o primeiro movimento
                //
                try {
                    $_comando = "DELETE FROM gear_status WHERE id = ".$_parametros['idStatus'];
                    $_sql = $_conexao->prepare($_comando);
                    if ($_sql->execute()){
                        gravaDLogAPI("gear_status", "Exclusão", $_parametros['siglaAeroporto'], $_parametros['usuario'], 
                                    $_parametros['idStatus'], $_comando);
                    }
                } catch (PDOException $e) {
                    //throw new PDOException("Não foi possível excluir o status!");
                }
                $_retorno = ['status' => 'OK', 'msg'=> "Registro excluído com sucesso!"];
                $_parametros['status'] = 'success';
                $_parametros['mensagem'] = array("Registro excluído com sucesso!");
                $_parametros['complemento'] = "";
                $_parametros['idStatus'] = null;
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