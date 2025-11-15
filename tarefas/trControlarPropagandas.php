<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

function controlarPropagandas($identificacao, $idAeroporto = '', $usuario = 'GEAR', $modo = 'AUT') {
    $tarefa = 'PRPG';
    $resultado = "trControlarPropagandas_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Controlar Propagandas - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$idAeroporto.' '.$usuario.' '.$modo;
    
    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

        //
        // Regra: Exibir cada propaganda por 2 minutos e controlar o intervalo de 5 minutos para exibir uma nova propaganda
        //
        // Mudar a situação da propaganda de EXB (Exibindo) para INT (Intervalo) caso a diferença entre o CURRENT_TIME 
        // e a Data e hora da última programação (dhExibicao) seja superior a 2 minutos
        //
        $conexao = conexao();
        $comando = "UPDATE gear_propagandas SET situacao = 'INT' 
                    WHERE situacao = 'EXB' 
                        AND TIMESTAMPDIFF(SECOND, IFNULL(dhExibicao,'1900-01-01 00:00:00'),  UTC_TIMESTAMP) > 120".
                        ($idAeroporto != "" ? " AND pg.idAeroporto = ".$idAeroporto : "");
        $sql = $conexao->prepare($comando);  
        if (!$sql->execute()) {
            throw new PDOException("Não foi possível verificar se a propaganda já ultrapassou o intervalo de 2 minutos para exibição!");
        }
        //
        // Mudar a situação da propaganda de INT (INT) para AGD (Aguardando) caso a diferença entre o CURRENT_TIME 
        // e a data e hora da última programação (dhExibicao) seja superior a 7 minutos 
        //
        // (Calcular 2 min da programação da propaganda +  5 min de intervalo)
        //
        $comando = "UPDATE gear_propagandas SET situacao = 'AGD' 
                    WHERE situacao = 'INT' 
                        AND TIMESTAMPDIFF(SECOND, IFNULL(dhExibicao,'1900-01-01 00:00:00'),  UTC_TIMESTAMP) > 420".
                        ($idAeroporto != "" ? " AND pg.idAeroporto = ".$idAeroporto : "");
        $sql = $conexao->prepare($comando);  
        if (!$sql->execute()) {
            throw new PDOException("Não foi possível verificar o intervalo já ultrapassou o intervalo de 5 minutos para exibição!");
        }

        // Caso não esteja em intervalo, mudar a situação de INT (Intervalo) para EXB (Exibindo) e alterar a data e  
        // hora de última programação (dhExibicao) para CURRENT_TIME
        $comando = selectDB("PropagandasParaAtivar",
                    ($idAeroporto != "" ? " AND pg.idAeroporto = ".$idAeroporto : ""),"pg.idAeroporto, pg.id");
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            // Looping para ativar as propagandas 
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $comando = "UPDATE gear_propagandas 
                            SET situacao = 'EXB', dhExibicao = UTC_TIMESTAMP
                            WHERE id = ".$dados['id'];
                $sql = $conexao->prepare($comando);  
                if (!$sql->execute()) {
                    throw new PDOException("Não foi possível definir qual as propagandas a serem ativadas!");
                } 
            }
        } else {
            throw new PDOException("Não foi possível definir quais serão as propagandas ativadas!");
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