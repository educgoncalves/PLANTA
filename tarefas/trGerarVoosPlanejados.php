<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../operacional/opFuncoes.php");

function gerarVoosPlanejados($identificacao, $aeroporto, $siglaAeroporto, $dtInicio, $dtFinal, $usuario = 'GEAR', $modo = 'AUT') {
    $tarefa = 'GVPL';
    $resultado = "trGerarVoosPlanejados_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Voos Planejados - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$aeroporto.' '.$dtInicio.' '.$dtFinal.' '.$siglaAeroporto.' '.$usuario.' '.$modo;
    
    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

        $conexao = conexao();

        $dtInicio = mudarDataAMD($dtInicio);
        $dtFinal = mudarDataAMD($dtFinal);

        $comando = "DELETE FROM gear_voos_planejados WHERE idAeroporto = ".$aeroporto. " AND fonte = 'ANAC'";
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            $comando = "INSERT INTO gear_voos_planejados(idAeroporto, operador, empresa, numeroVoo, equipamento, segunda, terca, quarta, ".
                        "quinta, sexta, sabado, domingo, assentos, siros, situacaoSiros, dataRegistro, inicioOperacao, fimOperacao, ". 
                        "naturezaOperacao, numeroEtapa, icaoOrigem,icaoDestino, horarioPartida, horarioChegada, servico, objetoTransporte, ". 
                        "codeshare, situacao, fonte, origem, cadastro)".
                        " SELECT ".$aeroporto.", operador, empresa, numeroVoo, equipamento, segunda, terca, quarta, ". 
                            "quinta, sexta, sabado, domingo, assentos, siros, situacaoSiros, dataRegistro, inicioOperacao, fimOperacao, ". 
                            "naturezaOperacao, numeroEtapa, icaoOrigem, icaoDestino, horarioPartida, horarioChegada, servico, objetoTransporte, ". 
                            "codeshare, situacao, 'ANAC', 'GER', UTC_TIMESTAMP()". 
                        " FROM gear_voos_anac vr ". 
                        " WHERE (vr.icaoOrigem = '".$siglaAeroporto."' OR vr.icaoDestino = '".$siglaAeroporto."') ". 
                        "   AND NOT (DATE_FORMAT(vr.inicioOperacao,'%Y-%m-%d') > ".$dtFinal.
                        "         OR DATE_FORMAT(vr.fimOperacao,'%Y-%m-%d') < ".$dtInicio.") ".
                        " ORDER BY vr.operador,vr.numeroVoo,vr.inicioOperacao,vr.numeroEtapa";
                            
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()){           
                $registros = "Número de registros: ".$sql->rowCount();         
                gravaDLog("gear_voos_planejados", "Geração", $siglaAeroporto, $usuario, $aeroporto, $comando, $registros);            
            } else {
                throw new PDOException("Não foi possível gerar o planejamento solicitado!");
            }
        } else {
            throw new PDOException("Não foi possível excluir o planejamento existente!");
        } 

        $_tipoMsg = "success";
        $_mensagens[] = "";
        $_mensagens[] = $registros;

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