<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

function processarConexoes($identificacao, $idAeroporto = '', $usuario = 'GEAR', $modo = 'AUT') {
    $tarefa = 'PCNX';

    $resultado = "trProcessarConexoes_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Processar Conexões - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$idAeroporto.' '.$usuario.' '.$modo;

    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

        // Inativar as conexoes pendentes
        // 
        $conexao = conexao();
        $filtro = ($idAeroporto != "" ? " AND co.idAeroporto = ".$idAeroporto : "").
                " AND co.situacao = 'ATV' 
                    AND ((co.grupo <> 'MNT' AND TIMESTAMPDIFF(SECOND, co.entrada, UTC_TIMESTAMP()) > 3600) 
                          OR 
                        (co.grupo = 'MNT' AND TIMESTAMPDIFF(SECOND, co.entrada, UTC_TIMESTAMP()) > 360))";
        $comando = selectDB("Conexoes", $filtro);
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            // Looping para criar os movimentos sucessores
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $comando = "UPDATE gear_conexoes SET saida = UTC_TIMESTAMP(), situacao = 'INA' WHERE id = ".$dados['id'];
                $sql = $conexao->prepare($comando);  
                if ($sql->execute()) {
                    gravaDLog('planta_conexoes', 'Alteracao', $dados['icao'], $usuario, $dados['id'], $comando);
                } 
            }
        } else {
            throw new PDOException("Não foi desativar as conexões!");
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