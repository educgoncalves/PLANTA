<?php
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../tarefas/trFuncoes.php");

function executarLimparLogs($identificacao, $usuario = 'GEAR', $modo = 'AUT') {
    $tarefa = 'ELOG';
    $resultado = "trLimparLogs_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Limpeza dos Logs - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$usuario.' '.$modo;

    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

        // Calcula data para exclusao
        $data = date_create()->modify('-3 days')->format('Ymd');

        // Excluindo os arquivos
        $arquivos = 0;
        $pasta = listarArquivos("../logs/", "txt");        
        foreach ($pasta as $arq) {
            if ($data >= $arq['data']) {
                if (unlink($arq['nome'])) {
                    $arquivos++;
                } else {
                    throw new PDOException("Falha na exclusão do arquivo ".$arq['nome']);
                }
            }
        }
        
        // Excluindo os registros
        $data = date_create()->modify('-10 days')->format('Ymd');
        $registros = 0;
        $conexao = conexao();
        $comando = "DELETE FROM planta_logs WHERE DATE_FORMAT(cadastro,'%Y%m%d') <= '".$data."' AND operacao <> 'Exibição'";
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            $registros = $sql->rowCount();  
        } else {
            throw new PDOException("Não foi possível excluir os registros da tabela log!");
        } 

        $_tipoMsg = "success";
        $_mensagens[] = "";
        $_mensagens[] = "Total arquivos excluídos = ".$arquivos;
        $_mensagens[] = "Total de registros excluídos = ".$registros;

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