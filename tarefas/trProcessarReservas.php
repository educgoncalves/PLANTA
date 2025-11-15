<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

function processarReservas($identificacao, $idAeroporto = '', $usuario = 'GEAR', $modo = 'AUT') {
    $tarefa = 'PRSR';

    $resultado = "trProcessarReservas_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Processar Reservas - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$idAeroporto.' '.$usuario.' '.$modo;

    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

        // RESERVAS - Gerar notificação para os usuarios do site  
        //  Verificar
        //      Só criticar as resevas que estejam na situação 'PEN' Pendente, 'AVN' A Vender ou 'VEN' Vencidas
        //      e que a última comunicação como o solicitante tenha sido a mais de 5 minutos
        //      Calcular a diferença em minutos entre o horário de chegada do voo e o horário atual
        //          Cancelar: Se a diferença for menor que 0 ou a situacao atual seja vencida
        //          Vencida: Se a diferença estiver entre 0 e 80% do tempo de tolerância para reserva
        //          A Vencer: Se a diferença estiver entre 80% e 100% do tempo de tolerância para a reserva
        //          Normal: Se a difrença for superior a 100% do tempo de tolerância para a reserva
        //     
        $conexao = conexao();
        $comando = "SELECT *
                    FROM 
                    (
                        SELECT (CASE WHEN (tmpDiferenca <= 0 OR situacao = 'VEN')      
                                THEN 'CAN'
                                ELSE (CASE WHEN tmpDiferenca BETWEEN 0 AND tmpTolerancia 
                                    THEN 'VEN'
                                    ELSE (CASE WHEN tmpDiferenca BETWEEN tmpTolerancia AND tmpReserva
                                            THEN 'AVN' ELSE 'PEN' END) END) END) AS tipo, T.*
                    FROM
                    (
                        SELECT rs.id, rs.observacao, rs.enviar, rs.envio, rs.situacao, ae.icao,
                            cl.tmpReserva, (cl.tmpReserva * 0.8) AS tmpTolerancia, 
                            TIMESTAMPDIFF(MINUTE, UTC_TIMESTAMP, rs.chegada) as tmpDiferenca
                        FROM gear_reservas rs 
                        LEFT JOIN gear_aeroportos ae ON ae.id = rs.idAeroporto
                        INNER JOIN gear_clientes cl ON cl.idAeroporto = rs.idAeroporto AND cl.sistema = 'MAER'
                        WHERE rs.situacao IN ('PEN', 'AVN', 'VEN') 
                            AND rs.enviar = 'NAO'
                            AND TIMESTAMPDIFF(MINUTE, IFNULL(rs.envio, '1900-01-01 00:00'), UTC_TIMESTAMP) >= 5";
        $comando .= ($idAeroporto != "" ? " AND rs.idAeroporto = ".$idAeroporto : "");
        $comando .= ") AS T ) AS R 
                    WHERE tipo IN ('CAN','AVN','VEN')
                    ORDER BY id DESC";
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            // Looping para criar gerar as notificações
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $enviar = 'SIM';
                $situacao = $dados['tipo'];
                switch ($situacao) {
                    case 'CAN':
                        $observacao = 'Sua reserva foi cancelada por não ter sido avaliada em tempo hábil!';
                    break;
                    case 'AVN':
                        $observacao = 'Sua reserva vencerá em breve por não ter sido avaliada!';
                    break;
                    case 'VEN':
                        $observacao = 'Sua reserva se encontra vencida e será cancelada em breve por não ter sido avaliada!';
                    break;
                }
                // Gravação da mensagem
                $comando = "UPDATE gear_reservas SET situacao = '".$situacao."', observacao = '".$observacao."', enviar = '".$enviar.
                            "' WHERE id = ".$dados['id'];
                $sql = $conexao->prepare($comando);  
                if ($sql->execute()) {
                    gravaDLog('planta_reservas', 'Alteração', $dados['icao'], $usuario, $dados['id'], $comando);
                } 
            }
        } else {
            throw new PDOException("Não foi possível criar as avaliações das reservas");
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