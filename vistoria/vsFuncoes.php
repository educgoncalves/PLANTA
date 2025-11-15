<?php 
// Gerar agendamentos para o Plano
//
function gerarAgendamentosPlano($_parametros){
    $_retorno = ['status' => 'OK', 'msg'=> "Agendamento gerado com sucesso!"];
    try{
        $_conexao = conexao();
        $_conexao->beginTransaction();
        // Excluir todos os agendamentos do plano não executados
        $_comando = "DELETE FROM gear_vistoria_agendamentos WHERE idPlano = ".$_parametros['id']." AND execucao IS NULL";
        $_sql = $_conexao->prepare($_comando);
        if ($_sql->execute()){
            // Decide qual será a data de início do agendamento, a maior data entre a inicial do plano ou a data de hoje 
            // Data e hora local do aeroporto
            $_date = dateTimeUTC($_parametros['utc'])->format('Y-m-d');
            $_inicio = ($_parametros['dtInicio'] > $_date ? $_parametros['dtInicio'] : $_date);

            // Decide qual a frequencia do agendamento
            switch ($_parametros['frequencia']) {
                case 'D':
                    $_incremento = "+1 days";
                break;
                case 'S':
                    $_incremento = "+7 days";
                break;
                case 'Q':
                    $_incremento = "+15 days";
                break;
                case 'M':
                    $_incremento = "+1 month";
                break;
                    
                default:
                    throw new Exception("Frequência não prevista para o plano ".$_parametros['numero']."!");
            }
            // Gerar o agendamento para 4 ciclos a partir da data de inicio do agendamento 
            for ($i = 1; $i <= 4; $i++) {
                $_dtInicio = mudarDataAMD($_inicio);
                // Verifica quantos agendamentos já existem para esta data
                $_qtd = 0;
                $_comando = "SELECT COUNT(*) as qtd
                            FROM gear_vistoria_agendamentos 
                            WHERE idAeroporto = ".$_parametros['idAeroporto']." AND idPlano = ".$_parametros['id'].
                                " AND inicio = ".$_dtInicio;
                $_sql = $_conexao->prepare($_comando);                                 
                if ($_sql->execute()) {
                    $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($_registros as $_dados) { 
                        $_qtd = $_dados['qtd'];
                    }
                }   
                // Gera o agendamento 
                $_comando = "INSERT INTO gear_vistoria_agendamentos (idAeroporto, idPlano, inicio, final, periodo, cadastro) VALUES (".
                            $_parametros['idAeroporto'].", ".$_parametros['id'].", ".$_dtInicio.", ".$_dtInicio.", '".
                            $_parametros['periodo']."', UTC_TIMESTAMP())";
                $_sql = $_conexao->prepare($_comando); 
                $_qtd = $_parametros['quantidade'] - $_qtd;
                for ($j = 1; $j <= $_qtd; $j++) {
                    if ($_sql->execute()) {
                        if ($_sql->rowCount() > 0) {
                            // Para cada agendamento gerar o resultado para garantir que os itens do plano original sejam mantidos
                            // $_cmd = "INSERT INTO gear_vistoria_resultados (idAgendamento, idPlano, idItem) 
                            //             SELECT ".$_conexao->lastInsertId().", vpi.idPlano, vpi.idItem 
                            //                 FROM gear_vistoria_planos_itens vpi WHERE idPlano = ".$_parametros['id'];
                        } else {
                            throw new PDOException("Não foi possível incluir os agendamentos do plano ".$_parametros['numero']."!");
                        }
                    } else {
                        throw new PDOException("Não foi possível incluir os agendamentos do plano ".$_parametros['numero']."!");
                    } 
                }
                $_date = date_create($_inicio);
                date_modify($_date,$_incremento);
                $_inicio = date_format($_date, "Y-m-d");
            }
             
            // Modifica a situação do plano
            $_comando = "UPDATE gear_vistoria_planos SET situacao = 'ATV', cadastro = UTC_TIMESTAMP() WHERE id = ".
                        $_parametros['id']." AND idAeroporto = ".$_parametros['idAeroporto'];
            $_sql = $_conexao->prepare($_comando); 
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    gravaDLogAPI("gear_vistoria_planos", "Agendamento", $_parametros['siglaAeroporto'], 
                                    $_parametros['usuario'], $_parametros['id'], $_comando);    
                    $_conexao->commit(); 
                } else {
                    throw new PDOException("Não foi possível alterar a situação do plano ".$_parametros['numero']."!");
                }
            } else {
                throw new PDOException("Não foi possível alterar a situação do plano ".$_parametros['numero']."!");
            }
        } else {
            throw new PDOException("Não foi possível excluir os agendamentos do plano ".$_parametros['numero']."!");
        } 
    } catch (PDOException $e) {
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()).' - '.$_comando];
        if ($_conexao->inTransaction()) {$_conexao->rollBack();} 
    }
    return $_retorno;
}

// Gerar resultados para o Agendamento
//
function verificarResultadosAgendamento($_idAgendamento){
    $_retorno = ['status' => 'OK', 'msg'=> "Existe resultados para este agendamento!", 'execucao' => ''];
    try{
        $_conexao = conexao();
        // Verifica se agendamento já existe no resultados
        $_comando = "SELECT IFNULL(va.execucao,'') as execucao
                        FROM gear_vistoria_resultados vr 
                        LEFT JOIN gear_vistoria_agendamentos va ON va.id = vr.idAgendamento
                        WHERE vr.idAgendamento = ".$_idAgendamento. " LIMIT 1";
        $_sql = $_conexao->prepare($_comando);
        if ($_sql->execute()){
            if ($_sql->rowCount() == 0) {
                $_retorno['execucao'] = '';
            } else {
                $_retorno['execucao'] = ($_sql->fetch(PDO::FETCH_ASSOC))['execucao'];
            }
        } else {
            throw new Exception("Verificação dos resultados não pode ser feita!");
        }           
        
    } catch (PDOException $e) {
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()).' - '.$_comando, 'execucao' => ''];
    }
    return $_retorno;
}

// Gerar resultados para o Agendamento
//
function excluirResultadosAgendamento($_idAgendamento, $_siglaAeroporto, $_usuario){
    $_retorno = ['status' => 'OK', 'msg'=> "Resultados excluídos com sucesso!"];
    try{
        $_conexao = conexao();
        $_conexao->beginTransaction();
        // Excluindo resultados
        $_comando = "DELETE FROM gear_vistoria_resultados WHERE idAgendamento = ".$_idAgendamento;
        $_sql = $_conexao->prepare($_comando);
        if ($_sql->execute()){
            // Limpando informações da execucao do agendamento
            $_comando = "UPDATE gear_vistoria_agendamentos SET execucao = null, local = null, idUsuario = null
                            WHERE id = ".$_idAgendamento;
            $_sql = $_conexao->prepare($_comando);
            if ($_sql->execute()){
                gravaDLog("gear_vistoria_resultados", "Exclusao", $_siglaAeroporto, $_usuario, $_conexao->lastInsertId(), $_comando); 
                gravaDLog("gear_vistoria_agendamentos", "Alteração", $_siglaAeroporto, $_usuario, $_idAgendamento, $_comando); 
                $_conexao->commit(); 
            } else {
                throw new Exception("Agendamento não pode ser alterado!");
            }
        } else {
            throw new Exception("Resultados não puderam ser criados!");
        }           
    } catch (PDOException $e) {
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()).' - '.$_comando];
        if ($_conexao->inTransaction()) {$_conexao->rollBack();} 
    }
    return $_retorno;
}

// Gerar resultados para o Agendamento
//
function gerarResultadosAgendamento($_idAgendamento, $_siglaAeroporto, $_usuario, $_idUsuario){
    $_retorno = ['status' => 'OK', 'msg'=> "Resultados gerados com sucesso!"];
    try{
        $_conexao = conexao();
        $_conexao->beginTransaction();

        // Verifica se agendamento já existe no resultados
        $_comando = "SELECT * FROM gear_vistoria_resultados WHERE idAgendamento = ".$_idAgendamento;
        $_sql = $_conexao->prepare($_comando);
        if ($_sql->execute()){
            if ($_sql->rowCount() == 0) {
                // Criar todos os itens do agendamento no resultado
                $_comando = "INSERT INTO gear_vistoria_resultados (idAgendamento, idPlano, idItem, cadastro) 
                                SELECT va.id, va.idPlano, vpi.idItem, UTC_TIMESTAMP()
                                FROM gear_vistoria_agendamentos va
                                LEFT JOIN gear_vistoria_planos_itens vpi ON vpi.idPlano = va.idPlano
                                WHERE va.id = ".$_idAgendamento;
                $_sql = $_conexao->prepare($_comando);
                if ($_sql->execute()){
                    if ($_sql->rowCount() == 0) {
                        throw new Exception("Resultados não puderam ser criados!");
                    } else {
                        // Grava a execução e usuário no agendamentos 
                        $_comando = "UPDATE gear_vistoria_agendamentos SET execucao = UTC_TIMESTAMP(), idUsuario = ".$_idUsuario.
                                        ", cadastro = UTC_TIMESTAMP() WHERE id = ".$_idAgendamento;
                        $_sql = $_conexao->prepare($_comando); 
                        if ($_sql->execute()) {
                            if ($_sql->rowCount() > 0) {
                                gravaDLog("gear_vistoria_agendamentos", "Alteração", $_siglaAeroporto, $_usuario, $_idAgendamento, $_comando, "Registro da execução");  
                                gravaDLog("gear_vistoria_resultados", "Inclusão", $_siglaAeroporto, $_usuario, $_conexao->lastInsertId(), $_comando);   
                            } else {
                                throw new PDOException("Não foi possível atualizar o agendamento!");
                            }
                        } else {
                            throw new PDOException("Não foi possível atualizar o agendamento!");
                        }                    
                    }
                } else {
                    throw new Exception("Resultados não puderam ser criados!");
                }
            }
            $_conexao->commit(); 
        } else {
            throw new Exception("Resultados não puderam ser criados!");
        }           
        
    } catch (PDOException $e) {
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()).' - '.$_comando];
        if ($_conexao->inTransaction()) {$_conexao->rollBack();} 
    }
    return $_retorno;
}

//
// Montagem do mapa de grade 
//
function montarMapaGrade($_local,$_x,$_y) {
    $_classe = "";
    $_elemento = "";
    $_html = "";
    //$_letras = ['','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','X','W','Y','Z'];
    for ($_i = 1; $_i <= $_y; $_i++) {
        $_html .= "<tr>";
        for ($_j = 1; $_j <= $_x; $_j++) {
            //$_elemento = $_letras[$_i].($_j<=9 ? "0" : "").$_j;
            $_elemento =($_i<=9 ? "0" : "").$_i.'-'.($_j<=9 ? "0" : "").$_j;
            $_classe = (strpos($_local,$_elemento) === FALSE) ? 'grade' : 'local';
            $_html .= "<td id='".$_elemento."' class='".$_classe."'></td>";
        }
        $_html .= "</tr>";
    }
    return $_html;
}
?>