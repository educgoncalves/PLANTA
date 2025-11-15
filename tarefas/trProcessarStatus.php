<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

function processarStatus($identificacao, $idAeroporto = '', $usuario = 'GEAR', $modo = 'AUT') {
    $tarefa = 'PSTA';

    $resultado = "trProcessarStatus_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Processar Status - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$idAeroporto.' '.$usuario.' '.$modo;

    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

        // STATUS - movimento sucessor depois
        // 
        // $conexao = conexao();
        // $filtro = ($idAeroporto != "" ? " AND st.idAeroporto = ".$idAeroporto : "").
        //         " AND mo.sucessora <> '' 
        //             AND ((mo.antes <> 0 AND TIMESTAMPDIFF(SECOND, DATE_ADD(UTC_TIMESTAMP(), INTERVAL cl.utc HOUR), sm.dhMovimento) <= (mo.antes * 60))
        //                 OR 
        //                  (mo.depois <> 0 AND TIMESTAMPDIFF(SECOND, sm.dhMovimento, DATE_ADD(UTC_TIMESTAMP(), INTERVAL cl.utc HOUR)) >= (mo.depois * 60)))";
        // $comando = selectDB("UltimosMovimentosStatusSucessora", $filtro);
        // $sql = $conexao->prepare($comando);  
        // if ($sql->execute()) {
        //     // Looping para criar os movimentos sucessores
        //     $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
        //     foreach ($registros as $dados) {
        //         $comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, cadastro) VALUES (".
        //                         $dados['id'].", DATE_ADD(UTC_TIMESTAMP(), INTERVAL ".$dados['utc']." HOUR), '".
        //                         $dados['sucessora']."', UTC_TIMESTAMP())";
        //         $sql = $conexao->prepare($comando);  
        //         if ($sql->execute()) {
        //             gravaDLog('planta_status_movimentos', 'Inclusão', $dados['icao'], $usuario, $conexao->lastInsertId(), $comando);
        //         } 
        //     }
        // } else {
        //     throw new PDOException("Não foi possível criar as situações sucessoras do movimento!");
        // } 

        // STATUS - movimento sucessor antes do tempo
        // 
        $conexao = conexao();
        $filtro = ($idAeroporto != "" ? " AND st.idAeroporto = ".$idAeroporto : "").
                " AND mo.sucessora <> '' 
                    AND ((mo.antes <> 0 AND TIMESTAMPDIFF(SECOND, DATE_ADD(UTC_TIMESTAMP(), INTERVAL cl.utc HOUR), sm.dhMovimento) <= (mo.antes * 60)))";
        $comando = selectDB("UltimosMovimentosStatusSucessora", $filtro);
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            // Looping para criar os movimentos sucessores
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, cadastro) VALUES (".
                                $dados['id'].", DATE_SUB('".$dados['dhMovimento']."', INTERVAL ".$dados['antes']." MINUTE), '".
                                $dados['sucessora']."', UTC_TIMESTAMP())";
                $sql = $conexao->prepare($comando);  
                if ($sql->execute()) {
                    gravaDLog('planta_status_movimentos', 'Inclusão', $dados['icao'], $usuario, $conexao->lastInsertId(), $comando);
                } else {
                    throw new PDOException("Não foi possível criar as situações sucessoras antes do movimento!");
                } 
            }
        } else {
            throw new PDOException("Não foi possível criar as situações sucessoras antes do movimento!");
        } 

        // STATUS - movimento sucessor depois do tempo
        // 
        $filtro = ($idAeroporto != "" ? " AND st.idAeroporto = ".$idAeroporto : "").
                " AND mo.sucessora <> '' 
                    AND ((mo.depois <> 0 AND TIMESTAMPDIFF(SECOND, sm.dhMovimento, DATE_ADD(UTC_TIMESTAMP(), INTERVAL cl.utc HOUR)) >= (mo.depois * 60)))";
        $comando = selectDB("UltimosMovimentosStatusSucessora", $filtro);
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            // Looping para criar os movimentos sucessores
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, cadastro) VALUES (".
                                $dados['id'].", DATE_ADD('".$dados['dhMovimento']."', INTERVAL ".$dados['depois']." MINUTE), '".
                                $dados['sucessora']."', UTC_TIMESTAMP())";
                $sql = $conexao->prepare($comando);  
                if ($sql->execute()) {
                    gravaDLog('planta_status_movimentos', 'Inclusão', $dados['icao'], $usuario, $conexao->lastInsertId(), $comando);
                } else {
                    throw new PDOException("Não foi possível criar as situações sucessoras depois do movimento!");
                } 
            }
        } else {
            throw new PDOException("Não foi possível criar as situações sucessoras depois do movimento!");
        } 

        // STATUS - movimento integrado
        // 
        $filtro = ($idAeroporto != "" ? " AND st.idAeroporto = ".$idAeroporto : "").
                " AND (mo.sucessora <> '' AND sm.idSegundoRecurso IS NOT NULL) ".
	            " AND   (TIMESTAMPDIFF(SECOND, sm.dhMovimento, DATE_ADD(UTC_TIMESTAMP(), INTERVAL cl.utc HOUR)) ".
                "           >= (CASE WHEN op.grupo = '1' THEN cl.tmpTaxiG1 ELSE cl.tmpTaxiG2 END) * 60)";
        $comando = selectDB("UltimosMovimentosStatusIntegrado", $filtro);
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            // Looping para criar os movimentos sucessores
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $comando = "INSERT INTO gear_status_movimentos (idStatus, dhMovimento, movimento, idRecurso, cadastro) VALUES (".
                                $dados['id'].", DATE_ADD('".$dados['dhMovimento']."', INTERVAL ".$dados['tmpGap']." MINUTE), '".
                                $dados['sucessora']."', ".$dados['idSegundoRecurso'].", UTC_TIMESTAMP())";                            
                $sql = $conexao->prepare($comando);  
                if ($sql->execute()) {
                    gravaDLog('planta_status_movimentos', 'Inclusão', $dados['icao'], $usuario, $conexao->lastInsertId(), $comando);
                } else {
                    throw new PDOException("Não foi possível criar a situação sucessora integrada do movimento!");
                }
            }
        } else {
            throw new PDOException("Não foi possível criar a situação sucessora integrada do movimento!");
        } 

        $_tipoMsg = "success";

    } catch (PDOException $e) {
        $_tipoMsg = "danger";
        $_mensagens[] = "";
        $_mensagens[] = traduzPDO($e->getMessage());
    }

    // Registrando o log
    foreach ($_mensagens as $msg) {
        gravaXLogProcesso($resultado, $_tipoMsg, $msg, $identificacao);
    }
            
    // Enviar email de resultado da execução da importação - depende de configuração na tabela de tarefas
    enviarEmailTarefa($tarefa,$resultado,$_tipoMsg);
}                       
?>