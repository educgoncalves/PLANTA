<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../operacional/opFuncoes.php");

function gerarVoosOperacionais($identificacao, $aeroporto, $siglaAeroporto, $dtMovimento, $usuario = 'GEAR', $modo = 'AUT') {
    $tarefa = 'GVOP';
    $resultado = "trGerarVoosOperacionais_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Voos Operacionais ".$siglaAeroporto." - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$aeroporto.' '.$dtMovimento.' '.$siglaAeroporto.' '.$usuario.' '.$modo;
    
    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

        $conexao = conexao();

        $diaSemanaAnac = date('w', strtotime($dtMovimento));
        $diaSemanaAnac = ($diaSemanaAnac != 0 ? $diaSemanaAnac : 7);
        $dtMovimento = mudarDataAMD($dtMovimento);

        if (!limparMovimento("AND vo.idAeroporto = ".$aeroporto. " AND vo.dtMovimento = ".$dtMovimento)) {
            throw new PDOException("Não foi possível excluir o movimento existente para a data ".$dtMovimento."!");
        };

        $comando = "INSERT INTO gear_voos_operacionais(idAeroporto, operacao, operador, numeroVoo, equipamento, assentos, dtMovimento, ".
                    "dhPrevista, classe, natureza, servico, origem, destino, numeroEtapa, codeshare, situacao, fonte, cadastro) ";
        // Select para gravação dos voos operacionais           
        $comando .= "SELECT idAeroporto, operacao, operador, numeroVoo, equipamento, assentos, dtMovimento, dhPrevista, ".
                    "classe, natureza, servico, origem, destino, numeroEtapa, codeshare, 'ATV', 'ANAC', UTC_TIMESTAMP() ". 
                    "FROM ".
                    "( ".
                    "SELECT idAeroporto, operacao, operador, numeroVoo, equipamento, assentos, dtMovimento, ". 
                    "CONCAT(dtMovimento,' ',DATE_FORMAT(dhInicioLocal, '%H:%i')) as dhPrevista, ". 
                    "dhInicioANAC, dhInicioLocal, dhFinalANAC, dhFinalLocal, frequencia, classe, natureza, servico, ". 
                    "origem, destino, numeroEtapa, codeshare, ". 
                    "(CASE WHEN DATEDIFF(dhInicioANAC,dhInicioLocal) = 1 THEN SUBSTR(CONCAT(frequencia,frequencia), 2, 7) ".
                    "    WHEN DATEDIFF(dhInicioANAC,dhInicioLocal) = -1 THEN SUBSTR(CONCAT(frequencia,frequencia), 7, 7) ".
                    "    ELSE frequencia END) as frequenciaLocal ".
                    "FROM ".                                    
                    "( ";
        // -- Voos de chegada
        $comando .= "SELECT vp.idAeroporto, 'CHG' as operacao, vp.operador, vp.numeroVoo, vp.equipamento, vp.assentos, ". 
                    $dtMovimento." as dtMovimento, ".
                    "CONCAT(DATE_FORMAT(vp.inicioOperacao,'%Y-%m-%d'),' ',vp.horarioChegada) as dhInicioANAC, ".
                    "DATE_ADD(CONCAT(DATE_FORMAT(vp.inicioOperacao,'%Y-%m-%d'),' ',vp.horarioChegada), INTERVAL cl.utc HOUR) as dhInicioLocal, ".
                    "CONCAT(DATE_FORMAT(vp.fimOperacao,'%Y-%m-%d'),' ',vp.horarioChegada) as dhFinalANAC, ".
                    "DATE_ADD(CONCAT(DATE_FORMAT(vp.fimOperacao,'%Y-%m-%d'),' ',vp.horarioChegada), INTERVAL cl.utc HOUR) as dhFinalLocal, ".
                    "CONCAT(segunda,terca,quarta,quinta,sexta,sabado,domingo) as frequencia, ".
                    "IFNULL(dpc.gear, '???') as classe, IFNULL(dpn.gear, '???') as natureza, IFNULL(dps.gear, '???') as servico, ".
                    "vp.icaoOrigem as origem, '' as destino, vp.numeroEtapa, vp.codeshare ".
                    "FROM gear_voos_planejados vp ".
                    "LEFT JOIN gear_clientes cl ON cl.sistema = 'GEAR' AND cl.idAeroporto = vp.idAeroporto ".
                    "LEFT JOIN gear_aeroportos ae ON ae.id = cl.idAeroporto ".
                    "LEFT JOIN gear_aeroportos og ON og.icao = vp.icaoOrigem ".
                    "LEFT JOIN gear_depara_anac dpc ON dpc.tipo = 'classe' AND dpc.anac = vp.naturezaOperacao ".
                    "LEFT JOIN gear_depara_anac dpn ON dpn.tipo = 'natureza' AND dpn.anac = vp.objetoTransporte ".
                    "LEFT JOIN gear_depara_anac dps ON dps.tipo = 'servico' AND dps.anac = vp.servico ".
                    "WHERE vp.idAeroporto = ".$aeroporto." AND vp.icaoDestino = ae.icao ".
                    "UNION ";
        // -- Voos de partida
        $comando .= "SELECT vp.idAeroporto, 'PRT' as operacao, vp.operador, vp.numeroVoo, vp.equipamento, vp.assentos, ". 
                    $dtMovimento." as dtMovimento, ". 
                    "CONCAT(DATE_FORMAT(vp.inicioOperacao,'%Y-%m-%d'),' ',vp.horarioPartida) as dhInicioANAC, ".
                    "DATE_ADD(CONCAT(DATE_FORMAT(vp.inicioOperacao,'%Y-%m-%d'),' ',vp.horarioPartida), INTERVAL cl.utc HOUR) as dhInicioLocal, ". 
                    "CONCAT(DATE_FORMAT(vp.fimOperacao,'%Y-%m-%d'),' ',vp.horarioPartida) as dhFinalANAC, ".		
                    "DATE_ADD(CONCAT(DATE_FORMAT(vp.fimOperacao,'%Y-%m-%d'),' ',vp.horarioPartida), INTERVAL cl.utc HOUR) as dhFinalLocal, ". 
                    "CONCAT(segunda,terca,quarta,quinta,sexta,sabado,domingo) as frequencia, ". 
                    "IFNULL(dpc.gear, '???') as classe, IFNULL(dpn.gear, '???') as natureza, IFNULL(dps.gear, '???') as servico, ". 
                    "'' as origem,vp.icaoDestino as destino,vp.numeroEtapa,vp.codeshare ".
                    "FROM gear_voos_planejados vp ". 
                    "LEFT JOIN gear_clientes cl ON cl.sistema = 'GEAR' AND cl.idAeroporto = vp.idAeroporto ". 
                    "LEFT JOIN gear_aeroportos ae ON ae.id = cl.idAeroporto ".
                    "LEFT JOIN gear_aeroportos de ON de.icao = vp.icaoDestino ".
                    "LEFT JOIN gear_depara_anac dpc ON dpc.tipo = 'classe' AND dpc.anac = vp.naturezaOperacao ".
                    "LEFT JOIN gear_depara_anac dpn ON dpn.tipo = 'natureza' AND dpn.anac = vp.objetoTransporte ".
                    "LEFT JOIN gear_depara_anac dps ON dps.tipo = 'servico' AND dps.anac = vp.servico ".
                    "WHERE vp.idAeroporto = ".$aeroporto." AND vp.icaoOrigem = ae.icao ";
        // -- Filtros e ordenação do UNION
        $comando .= ") T ". 
                    ") T1 ". 
                    "WHERE NOT (DATE_FORMAT(dhInicioLocal,'%Y-%m-%d %H:%i') > CONCAT(dtMovimento,' 23:59') ".			
                    "           OR DATE_FORMAT(dhFinalLocal,'%Y-%m-%d %H:%i') < CONCAT(dtMovimento,' 00:00')) ".
                    "AND  SUBSTR(frequenciaLocal, ".$diaSemanaAnac.", 1) <> '0' ". 
                    "ORDER BY operacao, dhPrevista, operador, numeroVoo";
        // -- Fechamento do select de gravação
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){       
            $registros = "Número de registros: ".$sql->rowCount();                    
            gravaDLog("gear_voos_operacionais", "Geração", $siglaAeroporto, $usuario, $aeroporto, $comando, $registros);            
        } else {
            throw new PDOException("Não foi possível gerar o movimento operacional solicitado!");
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